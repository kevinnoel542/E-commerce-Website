from fastapi import APIRouter, HTTPException, status, Depends, Request
from decimal import Decimal
from app.models.order import PaymentRequest
from app.core.security import get_current_user
from app.services.stripe_payment_service import stripe_payment_service
from app.core.logging import log_request, log_error
from app.db.client import db

router = APIRouter()

@router.post("/create-checkout-session")
async def create_checkout_session(
    payment_data: PaymentRequest,
    current_user: dict = Depends(get_current_user)
):
    """Create a Stripe checkout session for an order"""
    try:
        log_request("POST", "/api/v1/payments/create-checkout-session", current_user["email"])
        
        # Get order details
        order = await db.get_record("orders", payment_data.order_id)
        if not order:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Order not found"
            )
        
        # Verify order belongs to current user
        if order["user_id"] != current_user["user_id"]:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Access denied"
            )
        
        # Check if order is already paid
        if order["payment_status"] == "paid":
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Order is already paid"
            )
        
        # Get user profile for payment details
        user_profile = await db.get_record("profiles", current_user["user_id"])
        
        # Create Stripe checkout session
        payment_result = await stripe_payment_service.create_checkout_session(
            email=current_user["email"],
            amount=Decimal(str(order["final_amount"])),
            order_id=payment_data.order_id,
            customer_name=user_profile.get("full_name", ""),
            success_url=payment_data.success_url,
            cancel_url=payment_data.cancel_url
        )
        
        return payment_result
        
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Creating checkout session for order {payment_data.order_id}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to create checkout session"
        )

@router.get("/verify/{session_id}")
async def verify_payment(
    session_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Verify a payment by Stripe session ID"""
    try:
        log_request("GET", f"/api/v1/payments/verify/{session_id}", current_user["email"])
        
        # Verify the payment with Stripe
        verification_result = await stripe_payment_service.verify_payment(session_id)
        
        return verification_result
        
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Verifying payment for session {session_id}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to verify payment"
        )

@router.get("/status/{session_id}")
async def get_payment_status(
    session_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get payment status from database"""
    try:
        log_request("GET", f"/api/v1/payments/status/{session_id}", current_user["email"])
        
        # Get payment record from database
        payment_records = await db.get_records("payments", {"stripe_session_id": session_id})
        if not payment_records:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Payment not found"
            )
        
        payment = payment_records[0]
        
        # Verify user has access to this payment
        order = await db.get_record("orders", payment["order_id"])
        if not order or order["user_id"] != current_user["user_id"]:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Access denied"
            )
        
        return {
            "session_id": session_id,
            "order_id": payment["order_id"],
            "amount": payment["amount"],
            "currency": payment["currency"],
            "status": payment["status"],
            "created_at": payment["created_at"],
            "updated_at": payment.get("updated_at")
        }
        
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Getting payment status for session {session_id}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to get payment status"
        )

@router.post("/webhook")
async def stripe_webhook(request: Request):
    """Handle Stripe webhook events"""
    try:
        payload = await request.body()
        signature = request.headers.get('stripe-signature')
        
        if not signature:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Missing Stripe signature"
            )
        
        # Process webhook
        result = await stripe_payment_service.handle_webhook(
            payload.decode('utf-8'), 
            signature
        )
        
        return result
        
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, "Processing Stripe webhook")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Webhook processing failed"
        )

@router.get("/config")
async def get_stripe_config():
    """Get Stripe publishable key for frontend"""
    from app.core.config import STRIPE_PUBLISHABLE_KEY
    
    return {
        "publishable_key": STRIPE_PUBLISHABLE_KEY
    }

@router.get("/orders/{order_id}/payment-status")
async def get_order_payment_status(
    order_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get payment status for a specific order"""
    try:
        log_request("GET", f"/api/v1/payments/orders/{order_id}/payment-status", current_user["email"])
        
        # Get order details
        order = await db.get_record("orders", order_id)
        if not order:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Order not found"
            )
        
        # Verify order belongs to current user
        if order["user_id"] != current_user["user_id"]:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Access denied"
            )
        
        # Get payment records for this order
        payment_records = await db.get_records("payments", {"order_id": order_id})
        
        return {
            "order_id": order_id,
            "payment_status": order["payment_status"],
            "order_status": order["status"],
            "total_amount": order["final_amount"],
            "payments": payment_records
        }
        
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Getting payment status for order {order_id}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to get order payment status"
        )
