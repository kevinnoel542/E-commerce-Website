import stripe
import uuid
from typing import Dict, Any, Optional
from decimal import Decimal
from datetime import datetime
from app.core.config import (
    STRIPE_SECRET_KEY, STRIPE_PUBLISHABLE_KEY, 
    PAYMENT_SUCCESS_URL, PAYMENT_CANCEL_URL
)
from app.core.logging import payment_logger, log_payment_event, log_error
from app.db.client import db
from fastapi import HTTPException, status

# Initialize Stripe
stripe.api_key = STRIPE_SECRET_KEY

class StripePaymentService:
    """Service for handling payments with Stripe"""
    
    def __init__(self):
        self.secret_key = STRIPE_SECRET_KEY
        self.publishable_key = STRIPE_PUBLISHABLE_KEY
        
        if not self.secret_key:
            raise ValueError("Stripe secret key not configured")
        
        # Set the API key for stripe
        stripe.api_key = self.secret_key
    
    async def create_checkout_session(self, email: str, amount: Decimal, 
                                     order_id: str, customer_name: str = "",
                                     success_url: str = None, cancel_url: str = None) -> Dict[str, Any]:
        """Create a Stripe checkout session"""
        try:
            # Convert amount to cents (Stripe uses smallest currency unit)
            amount_cents = int(amount * 100)
            
            # Use provided URLs or defaults
            success_url = success_url or PAYMENT_SUCCESS_URL
            cancel_url = cancel_url or PAYMENT_CANCEL_URL
            
            # Add order_id as query parameter to success URL
            success_url = f"{success_url}?order_id={order_id}&session_id={{CHECKOUT_SESSION_ID}}"
            cancel_url = f"{cancel_url}?order_id={order_id}"
            
            # Create checkout session
            session = stripe.checkout.Session.create(
                payment_method_types=['card'],
                line_items=[{
                    'price_data': {
                        'currency': 'usd',  # Stripe requires 3-letter currency codes
                        'product_data': {
                            'name': f'Order #{order_id}',
                            'description': f'Payment for order {order_id}',
                        },
                        'unit_amount': amount_cents,
                    },
                    'quantity': 1,
                }],
                mode='payment',
                success_url=success_url,
                cancel_url=cancel_url,
                customer_email=email,
                metadata={
                    'order_id': order_id,
                    'customer_name': customer_name
                }
            )
            
            # Store payment record
            payment_data = {
                "id": str(uuid.uuid4()),
                "tx_ref": session.id,
                "order_id": order_id,
                "amount": str(amount),
                "currency": "USD",
                "customer_email": email,
                "status": "pending",
                "payment_method": "stripe",
                "stripe_session_id": session.id,
                "payment_link": session.url,
                "created_at": datetime.utcnow().isoformat()
            }
            
            await db.create_record("payments", payment_data)
            log_payment_event("CHECKOUT_SESSION_CREATED", session.id, float(amount), "pending")
            
            return {
                "status": "success",
                "checkout_url": session.url,
                "session_id": session.id,
                "amount": amount,
                "currency": "USD"
            }
                
        except stripe.error.StripeError as e:
            log_payment_event("CHECKOUT_SESSION_FAILED", order_id, float(amount), "failed")
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Stripe error: {str(e)}"
            )
        except Exception as e:
            log_error(e, f"Creating checkout session for order {order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to create checkout session"
            )
    
    async def verify_payment(self, session_id: str) -> Dict[str, Any]:
        """Verify payment status using Stripe session ID"""
        try:
            # Retrieve the checkout session
            session = stripe.checkout.Session.retrieve(session_id)
            
            # Get payment intent if session is completed
            if session.payment_status == 'paid':
                payment_intent = stripe.PaymentIntent.retrieve(session.payment_intent)
                
                # Update payment record in database
                payment_record = await db.get_records("payments", {"stripe_session_id": session_id})
                if payment_record:
                    payment_id = payment_record[0]["id"]
                    update_data = {
                        "status": "completed",
                        "stripe_payment_intent_id": payment_intent.id,
                        "updated_at": datetime.utcnow().isoformat()
                    }
                    await db.update_record("payments", payment_id, update_data)
                    
                    # Update order payment status
                    order_id = session.metadata.get('order_id')
                    if order_id:
                        await db.update_record("orders", order_id, {
                            "payment_status": "paid",
                            "updated_at": datetime.utcnow().isoformat()
                        })
                    
                    log_payment_event("PAYMENT_VERIFIED", session_id, 
                                    float(session.amount_total / 100), "completed")
                    
                    return {
                        "status": "success",
                        "payment_status": "paid",
                        "order_id": order_id,
                        "amount": session.amount_total / 100,
                        "currency": session.currency.upper()
                    }
            
            return {
                "status": "pending",
                "payment_status": session.payment_status,
                "order_id": session.metadata.get('order_id')
            }
            
        except stripe.error.StripeError as e:
            log_payment_event("PAYMENT_VERIFICATION_FAILED", session_id, 0, "failed")
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Stripe error: {str(e)}"
            )
        except Exception as e:
            log_error(e, f"Verifying payment for session {session_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to verify payment"
            )
    
    async def handle_webhook(self, payload: str, signature: str) -> Dict[str, Any]:
        """Handle Stripe webhook events"""
        try:
            from app.core.config import STRIPE_WEBHOOK_SECRET
            
            # Verify webhook signature
            event = stripe.Webhook.construct_event(
                payload, signature, STRIPE_WEBHOOK_SECRET
            )
            
            # Handle the event
            if event['type'] == 'checkout.session.completed':
                session = event['data']['object']
                await self.handle_successful_payment(session)
            elif event['type'] == 'payment_intent.payment_failed':
                payment_intent = event['data']['object']
                await self.handle_failed_payment(payment_intent)
            
            return {"status": "success"}
            
        except ValueError as e:
            # Invalid payload
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Invalid payload"
            )
        except stripe.error.SignatureVerificationError as e:
            # Invalid signature
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Invalid signature"
            )
        except Exception as e:
            log_error(e, "Processing webhook")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Webhook processing failed"
            )
    
    async def handle_successful_payment(self, session):
        """Handle successful payment from webhook"""
        try:
            order_id = session['metadata'].get('order_id')
            if order_id:
                # Update order status
                await db.update_record("orders", order_id, {
                    "payment_status": "paid",
                    "status": "confirmed",
                    "updated_at": datetime.utcnow().isoformat()
                })
                
                # Update payment record
                payment_records = await db.get_records("payments", {"stripe_session_id": session['id']})
                if payment_records:
                    payment_id = payment_records[0]["id"]
                    await db.update_record("payments", payment_id, {
                        "status": "completed",
                        "updated_at": datetime.utcnow().isoformat()
                    })
                
                log_payment_event("PAYMENT_COMPLETED", session['id'], 
                                session['amount_total'] / 100, "completed")
                
        except Exception as e:
            log_error(e, f"Handling successful payment for session {session['id']}")
    
    async def handle_failed_payment(self, payment_intent):
        """Handle failed payment from webhook"""
        try:
            # Find payment record by payment intent
            payment_records = await db.get_records("payments", {"stripe_payment_intent_id": payment_intent['id']})
            if payment_records:
                payment_id = payment_records[0]["id"]
                order_id = payment_records[0]["order_id"]
                
                # Update payment status
                await db.update_record("payments", payment_id, {
                    "status": "failed",
                    "updated_at": datetime.utcnow().isoformat()
                })
                
                # Update order status
                await db.update_record("orders", order_id, {
                    "payment_status": "failed",
                    "updated_at": datetime.utcnow().isoformat()
                })
                
                log_payment_event("PAYMENT_FAILED", payment_intent['id'], 
                                payment_intent['amount'] / 100, "failed")
                
        except Exception as e:
            log_error(e, f"Handling failed payment for intent {payment_intent['id']}")

# Create global instance
stripe_payment_service = StripePaymentService()
