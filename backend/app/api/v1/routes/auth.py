from fastapi import APIRouter, HTTPException, status, Depends
from app.models.auth import (
    LoginData, RegisterData, TokenResponse, RefreshTokenRequest,
    PasswordResetRequest, ChangePasswordRequest, UserProfile,
    UpdateProfileRequest, AuthResponse
)
from app.core.security import (
    create_token_pair, verify_token, get_current_user,
    verify_password, get_password_hash
)
from app.db.client import supabase, db
from app.core.logging import auth_logger, log_auth_event, log_error, logger
from datetime import datetime
import uuid

router = APIRouter()

@router.post("/register", response_model=AuthResponse)
async def register(data: RegisterData):
    """Register a new user"""
    try:
        auth_logger.info(f"Registration attempt for {data.email}")
        
        # Check if user already exists
        existing_users = await db.get_records("profiles", {"email": data.email})
        if existing_users:
            log_auth_event("REGISTER", data.email, False)
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="User with this email already exists"
            )
        
        # Register with Supabase Auth
        try:
            logger.info(f"Attempting Supabase registration for {data.email}")
            response = supabase.auth.sign_up({
                "email": data.email,
                "password": data.password,
                "options": {
                    "data": {
                        "full_name": data.full_name,
                        "phone": data.phone
                    }
                }
            })
            logger.info(f"Supabase registration response for {data.email}: user_id={response.user.id if response.user else 'None'}")
        except Exception as auth_error:
            error_message = str(auth_error)
            logger.error(f"Supabase registration failed for {data.email}: {error_message}")

            if "429" in error_message or "rate limit" in error_message.lower():
                log_auth_event("REGISTER", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_429_TOO_MANY_REQUESTS,
                    detail="Too many registration attempts. Please wait a moment and try again."
                )
            elif "email" in error_message.lower() and "invalid" in error_message.lower():
                log_auth_event("REGISTER", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Invalid email address format."
                )
            else:
                log_auth_event("REGISTER", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail=f"Registration failed: {error_message}"
                )
        
        if response.user:
            logger.info(f"User created in Supabase auth: {response.user.id}")
        else:
            logger.warning(f"Supabase registration succeeded but no user returned for {data.email}")
            # This might happen if email confirmation is required
            log_auth_event("REGISTER", data.email, True)
            return AuthResponse(
                message="Registration initiated. Please check your email for verification link.",
                user=None
            )

        if response.user:
            # Create user profile in our database using admin client to bypass RLS
            profile_data = {
                "id": response.user.id,
                "email": data.email,
                "full_name": data.full_name,
                "phone": data.phone,
                "role": "user",  # Default role for new users
                "created_at": datetime.utcnow().isoformat(),
                "is_active": True,
                "email_verified": response.user.email_confirmed_at is not None
            }

            try:
                # Use admin client to bypass RLS policies for profile creation
                logger.info(f"Creating profile for user {response.user.id}")
                from app.db.client import admin_supabase
                admin_response = admin_supabase.table("profiles").insert(profile_data).execute()

                if admin_response.data:
                    logger.info(f"Profile created successfully for {data.email}")
                    log_auth_event("REGISTER", data.email, True)
                    return AuthResponse(
                        message="User registered successfully. Please check your email for verification.",
                        user=UserProfile(**profile_data)
                    )
                else:
                    # Profile creation failed, but user was created in auth
                    logger.warning(f"Profile creation returned no data for {data.email}")
                    log_auth_event("REGISTER", data.email, True)
                    return AuthResponse(
                        message="User registered successfully. Profile will be created on first login.",
                        user=None
                    )
            except Exception as profile_error:
                # Profile creation failed, but user was created in auth
                logger.error(f"Profile creation failed for {data.email}: {str(profile_error)}")
                log_auth_event("REGISTER", data.email, True)
                return AuthResponse(
                    message="User registered successfully. Profile will be created on first login.",
                    user=None
                )
        else:
            error_message = "Registration failed"
            if hasattr(response, 'error') and response.error:
                error_message = response.error.message
            
            log_auth_event("REGISTER", data.email, False)
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=error_message
            )
    
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Registration for {data.email}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Registration failed"
        )

@router.post("/login", response_model=AuthResponse)
async def login(data: LoginData):
    """Login user and return tokens"""
    try:
        auth_logger.info(f"Login attempt for {data.email}")
        
        # Authenticate with Supabase
        try:
            logger.info(f"Attempting Supabase login for {data.email}")
            response = supabase.auth.sign_in_with_password({
                "email": data.email,
                "password": data.password
            })
            logger.info(f"Supabase login response for {data.email}: user_id={response.user.id if response.user else 'None'}")
        except Exception as auth_error:
            error_message = str(auth_error)
            logger.error(f"Supabase login failed for {data.email}: {error_message}")

            if "429" in error_message or "rate limit" in error_message.lower():
                log_auth_event("LOGIN", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_429_TOO_MANY_REQUESTS,
                    detail="Too many login attempts. Please wait a moment and try again."
                )
            elif "email not confirmed" in error_message.lower() or "email_not_confirmed" in error_message.lower():
                log_auth_event("LOGIN", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Please verify your email address before logging in."
                )
            elif "invalid" in error_message.lower() and ("credentials" in error_message.lower() or "password" in error_message.lower()):
                log_auth_event("LOGIN", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_401_UNAUTHORIZED,
                    detail="Invalid email or password."
                )
            else:
                log_auth_event("LOGIN", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_401_UNAUTHORIZED,
                    detail=f"Login failed: {error_message}"
                )
        
        if response.user:
            # Get user profile
            profile_data = await db.get_record("profiles", response.user.id)
            if not profile_data:
                # Create profile if it doesn't exist (for existing Supabase users)
                profile_data = {
                    "id": response.user.id,
                    "email": data.email,
                    "full_name": response.user.user_metadata.get("full_name", ""),
                    "phone": response.user.user_metadata.get("phone", ""),
                    "role": "user",  # Default role for new users
                    "created_at": datetime.utcnow().isoformat(),
                    "is_active": True,
                    "email_verified": response.user.email_confirmed_at is not None
                }

                try:
                    # Use admin client to create profile
                    from app.db.client import admin_supabase
                    admin_response = admin_supabase.table("profiles").insert(profile_data).execute()
                    if admin_response.data:
                        profile_data = admin_response.data[0]
                except Exception as e:
                    logger.warning(f"Could not create profile for {data.email}: {str(e)}")
                    # Continue with login even if profile creation fails
            
            # Check if user is active
            if not profile_data.get("is_active", True):
                log_auth_event("LOGIN", data.email, False)
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="Account is deactivated"
                )
            
            # Create tokens
            token_data = {
                "sub": data.email,
                "user_id": response.user.id
            }
            tokens = create_token_pair(token_data)
            
            log_auth_event("LOGIN", data.email, True)
            
            return AuthResponse(
                message="Login successful",
                user=UserProfile(**profile_data),
                tokens=TokenResponse(**tokens)
            )
        else:
            error_message = "Invalid credentials"
            if hasattr(response, 'error') and response.error:
                error_message = response.error.message
            
            log_auth_event("LOGIN", data.email, False)
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail=error_message
            )
    
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Login for {data.email}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Login failed"
        )

@router.post("/refresh", response_model=TokenResponse)
async def refresh_token(data: RefreshTokenRequest):
    """Refresh access token using refresh token"""
    try:
        # Verify refresh token
        payload = verify_token(data.refresh_token)
        
        if payload.get("type") != "refresh":
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid token type"
            )
        
        # Create new token pair
        token_data = {
            "sub": payload.get("sub"),
            "user_id": payload.get("user_id")
        }
        tokens = create_token_pair(token_data)
        
        auth_logger.info(f"Token refreshed for {payload.get('sub')}")
        return TokenResponse(**tokens)
    
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, "Token refresh")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Token refresh failed"
        )

@router.post("/logout")
async def logout(current_user: dict = Depends(get_current_user)):
    """Logout user (client should discard tokens)"""
    try:
        # In a production app, you might want to blacklist the token
        auth_logger.info(f"User logged out: {current_user['email']}")
        return {"message": "Logged out successfully"}
    
    except Exception as e:
        log_error(e, f"Logout for {current_user.get('email', 'unknown')}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Logout failed"
        )

@router.get("/profile", response_model=UserProfile)
async def get_profile(current_user: dict = Depends(get_current_user)):
    """Get current user profile"""
    try:
        user_id = current_user["user_id"]
        logger.info(f"Getting profile for user: {user_id}")

        # Try to get profile with admin client first (bypasses RLS)
        profile_data = await db.get_record_admin("profiles", user_id)

        if not profile_data:
            # If profile doesn't exist, try to create it from the current user data
            logger.info(f"Profile not found for user {user_id}, attempting to create one")

            # Get user info from current_user token data
            new_profile_data = {
                "id": user_id,
                "email": current_user.get("email", ""),
                "full_name": current_user.get("user_metadata", {}).get("full_name", ""),
                "role": "user",
                "is_active": True,
                "email_verified": current_user.get("email_confirmed_at") is not None,
                "created_at": datetime.utcnow().isoformat(),
                "updated_at": datetime.utcnow().isoformat()
            }

            try:
                profile_data = await db.create_record_admin("profiles", new_profile_data)
                if profile_data:
                    logger.info(f"Profile created successfully for user {user_id}")
                else:
                    logger.warning(f"Profile creation returned no data for user {user_id}")
                    profile_data = new_profile_data
            except Exception as create_error:
                logger.error(f"Failed to create profile for user {user_id}: {str(create_error)}")
                # Use the profile data we prepared
                profile_data = new_profile_data

        if not profile_data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Profile not found and could not be created"
            )

        return UserProfile(**profile_data)

    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Getting profile for {current_user.get('email', 'unknown')}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve profile"
        )

@router.put("/profile", response_model=UserProfile)
async def update_profile(
    profile_data: UpdateProfileRequest,
    current_user: dict = Depends(get_current_user)
):
    """Update user profile"""
    try:
        update_dict = profile_data.dict(exclude_unset=True)
        update_dict["updated_at"] = datetime.utcnow().isoformat()
        
        updated_profile = await db.update_record("profiles", current_user["user_id"], update_dict)
        if not updated_profile:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Profile not found"
            )
        
        auth_logger.info(f"Profile updated for {current_user['email']}")
        return UserProfile(**updated_profile)
    
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Updating profile for {current_user.get('email', 'unknown')}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to update profile"
        )

@router.post("/change-password")
async def change_password(
    password_data: ChangePasswordRequest,
    current_user: dict = Depends(get_current_user)
):
    """Change user password"""
    try:
        # Update password in Supabase
        response = supabase.auth.update_user({
            "password": password_data.new_password
        })
        
        if response.user:
            auth_logger.info(f"Password changed for {current_user['email']}")
            return {"message": "Password changed successfully"}
        else:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Failed to change password"
            )
    
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Changing password for {current_user.get('email', 'unknown')}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to change password"
        )

@router.post("/forgot-password")
async def forgot_password(data: PasswordResetRequest):
    """Send password reset email"""
    try:
        response = supabase.auth.reset_password_email(data.email)
        auth_logger.info(f"Password reset requested for {data.email}")
        return {"message": "Password reset email sent"}
    
    except Exception as e:
        log_error(e, f"Password reset for {data.email}")
        # Don't reveal if email exists or not
        return {"message": "If the email exists, a password reset link has been sent"}

@router.get("/verify-email")
async def verify_email(token: str):
    """Verify email address"""
    try:
        # This would typically be handled by Supabase's built-in email verification
        # You can customize this endpoint based on your needs
        return {"message": "Email verification handled by Supabase"}
    
    except Exception as e:
        log_error(e, "Email verification")
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email verification failed"
        )
