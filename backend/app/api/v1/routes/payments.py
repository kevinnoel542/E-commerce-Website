from fastapi import APIRouter, HTTPException, status, Depends, Request
from typing import Dict, Any
from app.core.security import get_current_user
from app.core.logging import log_request, payment_logger, log_payment_event
from app.services.payment_service import payment_service
import stripe
import logging

logger = logging.getLogger(__name__)
router = APIRouter()

@router.post("/initiate")
async def initiate_payment(
    email: str,
    amount: float,
    order_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Initiate a payment (deprecated - use order payment endpoint instead)"""
    log_request("POST", "/api/v1/payments/initiate", current_user["email"])

    # This endpoint is kept for backward compatibility
    # In practice, payments should be created through the orders endpoint

    try:
        from app.models.payment import PaymentCreate

        payment_data = PaymentCreate(
            order_id=order_id,
            amount=str(amount),
            currency="usd",
            customer_email=email
        )

        payment_result = await payment_service.create_checkout_session(payment_data)
        return payment_result

    except Exception as e:
        logger.error(f"Payment initiation failed: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to initiate payment"
        )

@router.get("/verify/{tx_ref}")
async def verify_payment(
    tx_ref: str,
    current_user: dict = Depends(get_current_user)
):
    """Verify a payment by transaction reference"""
    log_request("GET", f"/api/v1/payments/verify/{tx_ref}", current_user["email"])
    
    # Check if user has access to this payment
    payment_status = await payment_service.get_payment_status(tx_ref)
    if not payment_status:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Payment not found"
        )
    
    # Verify the payment with Flutterwave
    verification_result = await payment_service.verify_payment(tx_ref)
    
    return verification_result

@router.get("/status/{tx_ref}")
async def get_payment_status(
    tx_ref: str,
    current_user: dict = Depends(get_current_user)
):
    """Get payment status from database"""
    log_request("GET", f"/api/v1/payments/status/{tx_ref}", current_user["email"])
    
    payment_status = await payment_service.get_payment_status(tx_ref)
    if not payment_status:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Payment not found"
        )
    
    return payment_status

@router.post("/webhook")
async def handle_payment_webhook(request: Request):
    """Handle Stripe payment webhooks"""
    try:
        # Get the raw body for webhook verification
        body = await request.body()

        # Get Stripe signature from headers
        sig_header = request.headers.get("stripe-signature")

        if not sig_header:
            logger.error("Missing Stripe signature header")
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Missing signature header"
            )

        logger.info("Processing Stripe webhook")

        # Process the Stripe webhook
        result = await payment_service.handle_stripe_webhook(body, sig_header)

        logger.info(f"Stripe webhook processed successfully: {result}")
        return {"status": "success", "message": "Webhook processed"}

    except stripe.error.SignatureVerificationError as e:
        logger.error(f"Stripe signature verification failed: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Invalid signature"
        )
    except Exception as e:
        logger.error(f"Webhook processing error: {str(e)}")
        # Return 200 to prevent webhook retries for processing errors
        return {"status": "error", "message": "Webhook processing failed"}

@router.post("/refund/{tx_ref}")
async def initiate_refund(
    tx_ref: str,
    amount: float = None,
    current_user: dict = Depends(get_current_user)
):
    """Initiate a refund (admin only)"""
    log_request("POST", f"/api/v1/payments/refund/{tx_ref}", current_user["email"])
    
    # In a real app, check if user has admin privileges
    # For now, any authenticated user can initiate refunds
    
    refund_result = await payment_service.refund_payment(tx_ref, amount)
    
    return refund_result

@router.get("/history")
async def get_payment_history(
    current_user: dict = Depends(get_current_user)
):
    """Get payment history for current user"""
    log_request("GET", "/api/v1/payments/history", current_user["email"])
    
    try:
        from app.db.client import db
        
        # Get all payments for user's orders
        user_orders = await db.get_records("orders", {"user_id": current_user["user_id"]})
        order_ids = [order["id"] for order in user_orders]
        
        if not order_ids:
            return {"payments": [], "total": 0}
        
        # Get payments for these orders
        all_payments = []
        for order_id in order_ids:
            payments = await db.get_records("payments", {"order_id": order_id})
            all_payments.extend(payments)
        
        # Sort by creation date (newest first)
        all_payments.sort(key=lambda p: p["created_at"], reverse=True)
        
        return {
            "payments": all_payments,
            "total": len(all_payments)
        }
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve payment history"
        )

@router.get("/methods")
async def get_payment_methods():
    """Get available payment methods"""
    log_request("GET", "/api/v1/payments/methods")

    # Return available payment methods supported by Stripe
    return {
        "methods": [
            {
                "id": "card",
                "name": "Credit/Debit Card",
                "description": "Pay with Visa, Mastercard, American Express, and other cards",
                "enabled": True
            },
            {
                "id": "apple_pay",
                "name": "Apple Pay",
                "description": "Pay with Apple Pay",
                "enabled": True
            },
            {
                "id": "google_pay",
                "name": "Google Pay",
                "description": "Pay with Google Pay",
                "enabled": True
            },
            {
                "id": "link",
                "name": "Link",
                "description": "Pay with Link by Stripe",
                "enabled": True
            }
        ],
        "currency": "USD",
        "country": "US"
    }

@router.get("/currencies")
async def get_supported_currencies():
    """Get supported currencies"""
    log_request("GET", "/api/v1/payments/currencies")

    return {
        "currencies": [
            {
                "code": "USD",
                "name": "US Dollar",
                "symbol": "$",
                "default": True
            },
            {
                "code": "EUR",
                "name": "Euro",
                "symbol": "€",
                "default": False
            },
            {
                "code": "GBP",
                "name": "British Pound",
                "symbol": "£",
                "default": False
            },
            {
                "code": "CAD",
                "name": "Canadian Dollar",
                "symbol": "C$",
                "default": False
            }
        ]
    }

# Admin endpoints
@router.get("/admin/all")
async def get_all_payments_admin(
    current_user: dict = Depends(get_current_user)
):
    """Get all payments (admin only)"""
    log_request("GET", "/api/v1/payments/admin/all", current_user["email"])
    
    # In a real app, check if user has admin privileges
    
    try:
        from app.db.client import db
        
        payments = await db.get_records("payments")
        
        # Sort by creation date (newest first)
        payments.sort(key=lambda p: p["created_at"], reverse=True)
        
        return {
            "payments": payments,
            "total": len(payments)
        }
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve payments"
        )

@router.get("/admin/stats")
async def get_payment_stats_admin(
    current_user: dict = Depends(get_current_user)
):
    """Get payment statistics (admin only)"""
    log_request("GET", "/api/v1/payments/admin/stats", current_user["email"])
    
    # In a real app, check if user has admin privileges
    
    try:
        from app.db.client import db
        from decimal import Decimal
        
        all_payments = await db.get_records("payments")
        
        stats = {
            "total_payments": len(all_payments),
            "successful_payments": len([p for p in all_payments if p["status"] == "successful"]),
            "pending_payments": len([p for p in all_payments if p["status"] == "pending"]),
            "failed_payments": len([p for p in all_payments if p["status"] == "failed"]),
            "total_amount": sum(Decimal(p["amount"]) for p in all_payments if p["status"] == "successful"),
            "currency": "USD"
        }
        
        # Calculate success rate
        if stats["total_payments"] > 0:
            stats["success_rate"] = (stats["successful_payments"] / stats["total_payments"]) * 100
        else:
            stats["success_rate"] = 0
        
        return stats
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve payment statistics"
        )
