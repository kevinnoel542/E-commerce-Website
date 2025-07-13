import requests
import uuid
from typing import Dict, Any, Optional
from decimal import Decimal
from datetime import datetime
from app.core.config import (
    FLUTTERWAVE_SECRET_KEY, FLUTTERWAVE_PUBLIC_KEY, 
    PAYMENT_SUCCESS_URL, PAYMENT_CANCEL_URL, DEFAULT_CURRENCY
)
from app.core.logging import payment_logger, log_payment_event, log_error
from app.db.client import db
from fastapi import HTTPException, status

class PaymentService:
    """Service for handling payments with Flutterwave"""
    
    def __init__(self):
        self.base_url = "https://api.flutterwave.com/v3"
        self.headers = {
            "Authorization": f"Bearer {FLUTTERWAVE_SECRET_KEY}",
            "Content-Type": "application/json"
        }
    
    async def create_payment_link(self, email: str, amount: Decimal, 
                                 order_id: str, customer_name: str = "",
                                 phone: str = "") -> Dict[str, Any]:
        """Create a payment link with Flutterwave"""
        try:
            tx_ref = f"ORDER-{order_id}-{uuid.uuid4().hex[:8]}"
            
            payload = {
                "tx_ref": tx_ref,
                "amount": str(amount),
                "currency": DEFAULT_CURRENCY,
                "redirect_url": PAYMENT_SUCCESS_URL,
                "payment_options": "card,mobilemoney,ussd,banktransfer",
                "customer": {
                    "email": email,
                    "phonenumber": phone,
                    "name": customer_name or email
                },
                "customizations": {
                    "title": "E-Commerce Store",
                    "description": f"Payment for Order #{order_id}",
                    "logo": "https://yourdomain.com/logo.png"
                },
                "meta": {
                    "order_id": order_id,
                    "customer_email": email
                }
            }
            
            response = requests.post(
                f"{self.base_url}/payments",
                headers=self.headers,
                json=payload,
                timeout=30
            )
            
            if response.status_code == 200:
                result = response.json()
                if result.get("status") == "success":
                    # Store payment record
                    payment_data = {
                        "id": str(uuid.uuid4()),
                        "tx_ref": tx_ref,
                        "order_id": order_id,
                        "amount": str(amount),
                        "currency": DEFAULT_CURRENCY,
                        "customer_email": email,
                        "status": "pending",
                        "payment_link": result["data"]["link"],
                        "created_at": datetime.utcnow().isoformat()
                    }
                    
                    await db.create_record("payments", payment_data)
                    log_payment_event("LINK_CREATED", tx_ref, float(amount), "pending")
                    
                    return {
                        "payment_link": result["data"]["link"],
                        "tx_ref": tx_ref,
                        "amount": amount,
                        "currency": DEFAULT_CURRENCY
                    }
                else:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"Payment creation failed: {result.get('message', 'Unknown error')}"
                    )
            else:
                raise HTTPException(
                    status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                    detail="Failed to create payment link"
                )
        
        except requests.RequestException as e:
            log_error(e, f"Creating payment link for order {order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Payment service unavailable"
            )
        except Exception as e:
            log_error(e, f"Creating payment link for order {order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to create payment link"
            )
    
    async def verify_payment(self, tx_ref: str) -> Dict[str, Any]:
        """Verify payment status with Flutterwave"""
        try:
            response = requests.get(
                f"{self.base_url}/transactions/verify_by_reference?tx_ref={tx_ref}",
                headers=self.headers,
                timeout=30
            )
            
            if response.status_code == 200:
                result = response.json()
                if result.get("status") == "success":
                    transaction_data = result["data"]
                    
                    # Update payment record
                    payment_update = {
                        "status": transaction_data["status"],
                        "flutterwave_id": transaction_data["id"],
                        "amount_paid": transaction_data["amount"],
                        "currency_paid": transaction_data["currency"],
                        "payment_type": transaction_data.get("payment_type"),
                        "verified_at": datetime.utcnow().isoformat()
                    }
                    
                    await db.update_record("payments", tx_ref, payment_update, "tx_ref")
                    log_payment_event("VERIFIED", tx_ref, transaction_data["amount"], transaction_data["status"])
                    
                    return {
                        "status": transaction_data["status"],
                        "amount": transaction_data["amount"],
                        "currency": transaction_data["currency"],
                        "tx_ref": tx_ref,
                        "transaction_id": transaction_data["id"]
                    }
                else:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"Payment verification failed: {result.get('message', 'Unknown error')}"
                    )
            else:
                raise HTTPException(
                    status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                    detail="Failed to verify payment"
                )
        
        except requests.RequestException as e:
            log_error(e, f"Verifying payment {tx_ref}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Payment verification service unavailable"
            )
        except Exception as e:
            log_error(e, f"Verifying payment {tx_ref}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to verify payment"
            )
    
    async def handle_webhook(self, webhook_data: Dict[str, Any]) -> bool:
        """Handle Flutterwave webhook"""
        try:
            event_type = webhook_data.get("event")
            data = webhook_data.get("data", {})
            
            if event_type == "charge.completed":
                tx_ref = data.get("tx_ref")
                status = data.get("status")
                amount = data.get("amount")
                
                if tx_ref and status:
                    # Update payment status
                    payment_update = {
                        "status": status,
                        "webhook_received_at": datetime.utcnow().isoformat(),
                        "webhook_data": webhook_data
                    }
                    
                    await db.update_record("payments", tx_ref, payment_update, "tx_ref")
                    log_payment_event("WEBHOOK_RECEIVED", tx_ref, amount, status)
                    
                    # If payment is successful, update order status
                    if status == "successful":
                        payment_record = await db.get_record("payments", tx_ref, "tx_ref")
                        if payment_record:
                            order_id = payment_record.get("order_id")
                            if order_id:
                                await db.update_record("orders", order_id, {
                                    "payment_status": "paid",
                                    "status": "confirmed",
                                    "updated_at": datetime.utcnow().isoformat()
                                })
                                payment_logger.info(f"Order {order_id} marked as paid")
                    
                    return True
            
            return False
        
        except Exception as e:
            log_error(e, "Processing payment webhook")
            return False
    
    async def get_payment_status(self, tx_ref: str) -> Optional[Dict[str, Any]]:
        """Get payment status from database"""
        try:
            payment_record = await db.get_record("payments", tx_ref, "tx_ref")
            if payment_record:
                return {
                    "tx_ref": payment_record["tx_ref"],
                    "order_id": payment_record["order_id"],
                    "amount": payment_record["amount"],
                    "currency": payment_record["currency"],
                    "status": payment_record["status"],
                    "created_at": payment_record["created_at"]
                }
            return None
        
        except Exception as e:
            log_error(e, f"Getting payment status for {tx_ref}")
            return None
    
    async def refund_payment(self, tx_ref: str, amount: Optional[Decimal] = None) -> Dict[str, Any]:
        """Initiate a refund (placeholder - implement based on Flutterwave refund API)"""
        try:
            # This is a placeholder - implement actual refund logic
            payment_record = await db.get_record("payments", tx_ref, "tx_ref")
            if not payment_record:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Payment not found"
                )
            
            refund_amount = amount or Decimal(payment_record["amount"])
            
            # Update payment record to show refund initiated
            await db.update_record("payments", tx_ref, {
                "refund_amount": str(refund_amount),
                "refund_initiated_at": datetime.utcnow().isoformat(),
                "status": "refund_pending"
            }, "tx_ref")
            
            log_payment_event("REFUND_INITIATED", tx_ref, float(refund_amount), "refund_pending")
            
            return {
                "tx_ref": tx_ref,
                "refund_amount": refund_amount,
                "status": "refund_initiated"
            }
        
        except Exception as e:
            log_error(e, f"Initiating refund for {tx_ref}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to initiate refund"
            )

# Create global payment service instance
payment_service = PaymentService()
