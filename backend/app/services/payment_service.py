import stripe
import uuid
import requests
from typing import Dict, Any, Optional
from decimal import Decimal
from datetime import datetime
from app.core.config import (
    STRIPE_SECRET_KEY, STRIPE_PUBLISHABLE_KEY,
    PAYMENT_SUCCESS_URL, PAYMENT_CANCEL_URL, DEFAULT_CURRENCY
)
from app.core.logging import payment_logger, log_payment_event, log_error
from app.db.client import db
from app.models.payment import Payment, PaymentCreate, PaymentStatus, PaymentMethodType
from fastapi import HTTPException, status
import logging

logger = logging.getLogger(__name__)

# Initialize Stripe
stripe.api_key = STRIPE_SECRET_KEY

class PaymentService:
    """Service for handling payments with Stripe"""

    def __init__(self):
        self.secret_key = STRIPE_SECRET_KEY
        self.publishable_key = STRIPE_PUBLISHABLE_KEY

        if not self.secret_key:
            raise ValueError("Stripe secret key not configured")

        # Set the API key for stripe
        stripe.api_key = self.secret_key

    async def create_checkout_session(self, payment_data: PaymentCreate) -> Dict[str, Any]:
        """Create a Stripe checkout session"""
        try:
            # Convert amount to cents (Stripe uses smallest currency unit)
            amount_cents = int(payment_data.amount * 100)

            # Use provided URLs or defaults
            success_url = payment_data.success_url or PAYMENT_SUCCESS_URL
            cancel_url = payment_data.cancel_url or PAYMENT_CANCEL_URL

            # Add order_id as query parameter to success URL
            success_url = f"{success_url}?order_id={payment_data.order_id}&session_id={{CHECKOUT_SESSION_ID}}"
            cancel_url = f"{cancel_url}?order_id={payment_data.order_id}"

            # Create checkout session
            session = stripe.checkout.Session.create(
                payment_method_types=payment_data.payment_method_types or ['card'],
                line_items=[{
                    'price_data': {
                        'currency': payment_data.currency,
                        'product_data': {
                            'name': f'Order #{payment_data.order_id}',
                            'description': payment_data.description or f'Payment for order {payment_data.order_id}',
                        },
                        'unit_amount': amount_cents,
                    },
                    'quantity': 1,
                }],
                mode='payment',
                success_url=success_url,
                cancel_url=cancel_url,
                customer_email=payment_data.customer_email,
                metadata={
                    'order_id': payment_data.order_id,
                    'customer_email': payment_data.customer_email
                }
            )

            # Store payment record in database using existing table structure
            payment_record = {
                "order_id": payment_data.order_id,
                "stripe_checkout_session_id": session.id,
                "amount": str(payment_data.amount),
                "currency": payment_data.currency,
                "customer_email": payment_data.customer_email,
                "status": PaymentStatus.PENDING.value,
                "checkout_url": session.url,
                "success_url": success_url,
                "cancel_url": cancel_url,
                "description": payment_data.description
            }

            # Create payment record using admin client
            created_payment = await db.create_record_admin("payments", payment_record)
            logger.info(f"Payment record created successfully for order: {payment_data.order_id}")
            log_payment_event("CHECKOUT_SESSION_CREATED", session.id, float(payment_data.amount), "pending")

            return {
                "status": "success",
                "checkout_url": session.url,
                "session_id": session.id,
                "amount": payment_data.amount,
                "currency": payment_data.currency
            }
        
        except stripe.error.StripeError as e:
            log_error(e, f"Creating checkout session for order {payment_data.order_id}")
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Stripe error: {str(e)}"
            )
        except Exception as e:
            log_error(e, f"Creating checkout session for order {payment_data.order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to create checkout session"
            )
    
    async def verify_payment(self, session_id: str) -> Dict[str, Any]:
        """Verify payment status with Stripe"""
        try:
            # Retrieve the checkout session from Stripe
            session = stripe.checkout.Session.retrieve(session_id)

            # Get payment intent if available
            payment_intent = None
            if session.payment_intent:
                payment_intent = stripe.PaymentIntent.retrieve(session.payment_intent)

            # Map Stripe status to our status
            status_mapping = {
                'complete': PaymentStatus.SUCCEEDED.value,
                'open': PaymentStatus.PENDING.value,
                'expired': PaymentStatus.FAILED.value
            }

            payment_status = status_mapping.get(session.payment_status, PaymentStatus.PENDING.value)

            # Update payment record in database
            payment_update = {
                "status": payment_status,
                "stripe_payment_intent_id": session.payment_intent,
                "amount_received": str(session.amount_total / 100) if session.amount_total else "0",
                "payment_method_type": payment_intent.payment_method_types[0] if payment_intent and payment_intent.payment_method_types else None,
                "updated_at": datetime.utcnow().isoformat()
            }

            if payment_status == PaymentStatus.SUCCEEDED.value:
                payment_update["succeeded_at"] = datetime.utcnow().isoformat()

            # Find and update payment record
            payment_records = await db.get_records("payments", {"stripe_checkout_session_id": session_id})
            if payment_records:
                payment_id = payment_records[0]["id"]
                await db.update_record("payments", payment_id, payment_update)
                log_payment_event("VERIFIED", session_id, session.amount_total / 100 if session.amount_total else 0, payment_status)

            return {
                "status": payment_status,
                "amount": session.amount_total / 100 if session.amount_total else 0,
                "currency": session.currency,
                "session_id": session_id,
                "payment_intent_id": session.payment_intent
            }

        except stripe.error.StripeError as e:
            log_error(e, f"Verifying payment {session_id}")
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Stripe error: {str(e)}"
            )
        except Exception as e:
            log_error(e, f"Verifying payment {session_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to verify payment"
            )
    
    async def handle_webhook(self, webhook_data: Dict[str, Any], signature: str = None) -> bool:
        """Handle Stripe webhook"""
        try:
            event_type = webhook_data.get("type")
            data = webhook_data.get("data", {}).get("object", {})

            if event_type == "checkout.session.completed":
                session_id = data.get("id")
                payment_status = data.get("payment_status")
                amount_total = data.get("amount_total", 0)

                if session_id:
                    # Update payment status
                    payment_update = {
                        "status": PaymentStatus.SUCCEEDED.value if payment_status == "paid" else PaymentStatus.FAILED.value,
                        "amount_received": str(amount_total / 100) if amount_total else "0",
                        "succeeded_at": datetime.utcnow().isoformat() if payment_status == "paid" else None,
                        "webhook_received_at": datetime.utcnow().isoformat(),
                        "webhook_data": webhook_data
                    }

                    # Find and update payment record
                    payment_records = await db.get_records("payments", {"stripe_checkout_session_id": session_id})
                    if payment_records:
                        payment_id = payment_records[0]["id"]
                        await db.update_record("payments", payment_id, payment_update)
                        log_payment_event("WEBHOOK_RECEIVED", session_id, amount_total / 100 if amount_total else 0, payment_update["status"])

                        # If payment is successful, update order status
                        if payment_status == "paid":
                            order_id = payment_records[0].get("order_id")
                            if order_id:
                                await db.update_record("orders", order_id, {
                                    "payment_status": "paid",
                                    "status": "confirmed",
                                    "updated_at": datetime.utcnow().isoformat()
                                })
                                payment_logger.info(f"Order {order_id} marked as paid")

                    return True

            elif event_type == "checkout.session.expired":
                session_id = data.get("id")
                if session_id:
                    # Mark payment as failed
                    payment_update = {
                        "status": PaymentStatus.FAILED.value,
                        "canceled_at": datetime.utcnow().isoformat(),
                        "webhook_received_at": datetime.utcnow().isoformat(),
                        "webhook_data": webhook_data
                    }

                    payment_records = await db.get_records("payments", {"stripe_checkout_session_id": session_id})
                    if payment_records:
                        payment_id = payment_records[0]["id"]
                        await db.update_record("payments", payment_id, payment_update)
                        log_payment_event("WEBHOOK_RECEIVED", session_id, 0, "failed")

                    return True

            return False
        
        except Exception as e:
            log_error(e, "Processing payment webhook")
            return False
    
    async def get_payment_status(self, session_id: str) -> Optional[Dict[str, Any]]:
        """Get payment status from database"""
        try:
            payment_records = await db.get_records("payments", {"stripe_checkout_session_id": session_id})
            if payment_records:
                payment_record = payment_records[0]
                return {
                    "session_id": payment_record["stripe_checkout_session_id"],
                    "order_id": payment_record["order_id"],
                    "amount": payment_record["amount"],
                    "currency": payment_record["currency"],
                    "status": payment_record["status"],
                    "created_at": payment_record["created_at"],
                    "checkout_url": payment_record.get("checkout_url")
                }
            return None

        except Exception as e:
            log_error(e, f"Getting payment status for {session_id}")
            return None
    
    async def refund_payment(self, payment_intent_id: str, amount: Optional[Decimal] = None) -> Dict[str, Any]:
        """Initiate a refund with Stripe"""
        try:
            # Find payment record by payment intent ID
            payment_records = await db.get_records("payments", {"stripe_payment_intent_id": payment_intent_id})
            if not payment_records:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Payment not found"
                )

            payment_record = payment_records[0]
            refund_amount = amount or Decimal(payment_record["amount"])

            # Create refund with Stripe
            refund = stripe.Refund.create(
                payment_intent=payment_intent_id,
                amount=int(refund_amount * 100),  # Convert to cents
                reason='requested_by_customer'
            )

            # Update payment record
            current_refunded = Decimal(payment_record.get("amount_refunded", "0"))
            new_refunded_amount = current_refunded + refund_amount

            payment_update = {
                "amount_refunded": str(new_refunded_amount),
                "updated_at": datetime.utcnow().isoformat()
            }

            # Determine new status
            total_amount = Decimal(payment_record["amount"])
            if new_refunded_amount >= total_amount:
                payment_update["status"] = PaymentStatus.REFUNDED.value
            else:
                payment_update["status"] = PaymentStatus.PARTIALLY_REFUNDED.value

            await db.update_record("payments", payment_record["id"], payment_update)
            log_payment_event("REFUND_CREATED", payment_intent_id, float(refund_amount), payment_update["status"])

            return {
                "refund_id": refund.id,
                "payment_intent_id": payment_intent_id,
                "refund_amount": refund_amount,
                "status": refund.status,
                "currency": refund.currency
            }

        except stripe.error.StripeError as e:
            log_error(e, f"Creating refund for {payment_intent_id}")
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Stripe error: {str(e)}"
            )
        except Exception as e:
            log_error(e, f"Initiating refund for {payment_intent_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to initiate refund"
            )

# Create global payment service instance
payment_service = PaymentService()
