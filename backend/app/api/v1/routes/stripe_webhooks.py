from fastapi import APIRouter, HTTPException, status, Request, Header
from typing import Optional
import stripe
from app.services.payment_service import payment_service
from app.core.config import STRIPE_WEBHOOK_SECRET
from app.core.logging import log_request, payment_logger, log_error

router = APIRouter()

@router.post("/webhook")
async def stripe_webhook(
    request: Request,
    stripe_signature: Optional[str] = Header(None, alias="stripe-signature")
):
    """Handle Stripe webhook events"""
    try:
        # Get the raw body
        body = await request.body()
        
        # Verify webhook signature if secret is configured
        if STRIPE_WEBHOOK_SECRET and stripe_signature:
            try:
                event = stripe.Webhook.construct_event(
                    body, stripe_signature, STRIPE_WEBHOOK_SECRET
                )
            except ValueError as e:
                payment_logger.error(f"Invalid payload: {e}")
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Invalid payload"
                )
            except stripe.error.SignatureVerificationError as e:
                payment_logger.error(f"Invalid signature: {e}")
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Invalid signature"
                )
        else:
            # If no webhook secret is configured, parse the body directly
            # Note: This is less secure and should only be used in development
            import json
            event = json.loads(body.decode('utf-8'))
        
        payment_logger.info(f"Received Stripe webhook: {event['type']}")
        
        # Handle the event
        if event['type'] == 'checkout.session.completed':
            session = event['data']['object']
            payment_logger.info(f"Checkout session completed: {session['id']}")
            
            # Process the successful payment
            success = await payment_service.handle_webhook(event)
            if success:
                payment_logger.info(f"Successfully processed checkout session: {session['id']}")
            else:
                payment_logger.warning(f"Failed to process checkout session: {session['id']}")
        
        elif event['type'] == 'checkout.session.expired':
            session = event['data']['object']
            payment_logger.info(f"Checkout session expired: {session['id']}")
            
            # Process the expired session
            success = await payment_service.handle_webhook(event)
            if success:
                payment_logger.info(f"Successfully processed expired session: {session['id']}")
        
        elif event['type'] == 'payment_intent.succeeded':
            payment_intent = event['data']['object']
            payment_logger.info(f"Payment intent succeeded: {payment_intent['id']}")
            
            # Additional processing for payment intent if needed
            # This is usually handled by checkout.session.completed
        
        elif event['type'] == 'payment_intent.payment_failed':
            payment_intent = event['data']['object']
            payment_logger.info(f"Payment intent failed: {payment_intent['id']}")
            
            # Handle failed payment
            # You might want to update order status or notify customer
        
        elif event['type'] == 'charge.dispute.created':
            dispute = event['data']['object']
            payment_logger.warning(f"Dispute created for charge: {dispute['charge']}")
            
            # Handle dispute - you might want to notify admins
        
        else:
            payment_logger.info(f"Unhandled event type: {event['type']}")
        
        return {"status": "success"}
    
    except Exception as e:
        log_error(e, "Processing Stripe webhook")
        payment_logger.error(f"Error processing webhook: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Webhook processing failed"
        )

@router.get("/test")
async def test_webhook():
    """Test endpoint to verify webhook route is working"""
    return {
        "message": "Stripe webhook endpoint is working",
        "webhook_secret_configured": bool(STRIPE_WEBHOOK_SECRET)
    }

@router.post("/simulate-success")
async def simulate_successful_payment(session_id: str):
    """Simulate a successful payment for testing (development only)"""
    try:
        # Create a mock webhook event
        mock_event = {
            "type": "checkout.session.completed",
            "data": {
                "object": {
                    "id": session_id,
                    "payment_status": "paid",
                    "amount_total": 5000,  # $50.00 in cents
                    "currency": "usd"
                }
            }
        }
        
        success = await payment_service.handle_webhook(mock_event)
        
        if success:
            return {"status": "success", "message": f"Simulated successful payment for session {session_id}"}
        else:
            return {"status": "error", "message": "Failed to process simulated payment"}
    
    except Exception as e:
        log_error(e, f"Simulating payment for session {session_id}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to simulate payment"
        )

@router.post("/simulate-expired")
async def simulate_expired_session(session_id: str):
    """Simulate an expired session for testing (development only)"""
    try:
        # Create a mock webhook event
        mock_event = {
            "type": "checkout.session.expired",
            "data": {
                "object": {
                    "id": session_id,
                    "payment_status": "unpaid"
                }
            }
        }
        
        success = await payment_service.handle_webhook(mock_event)
        
        if success:
            return {"status": "success", "message": f"Simulated expired session for {session_id}"}
        else:
            return {"status": "error", "message": "Failed to process simulated expiration"}
    
    except Exception as e:
        log_error(e, f"Simulating expired session {session_id}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to simulate session expiration"
        )
