from fastapi import APIRouter, HTTPException, status, Depends, Query
from typing import Optional
from app.models.order import (
    Order, OrderCreate, OrderUpdate, OrderPatch, OrderAdminPatch, OrderListResponse,
    Cart, CartSummary, OrderStatus, PaymentStatus
)
from app.services.order_service import order_service
from app.services.payment_service import payment_service
from app.core.security import get_current_user
from app.core.logging import log_request, log_order_event

router = APIRouter()

@router.post("/cart/summary", response_model=CartSummary)
async def calculate_cart_summary(
    cart: Cart,
    current_user: dict = Depends(get_current_user)
):
    """Calculate cart totals before creating order"""
    log_request("POST", "/api/v1/orders/cart/summary", current_user["email"])
    
    return await order_service.calculate_cart_summary(cart)

@router.post("/", response_model=Order)
async def create_order(
    order_data: OrderCreate,
    current_user: dict = Depends(get_current_user)
):
    """Create a new order"""
    log_request("POST", "/api/v1/orders/", current_user["email"])
    
    order = await order_service.create_order(
        order_data, current_user["user_id"], current_user["email"]
    )
    
    return order

@router.get("/", response_model=OrderListResponse)
async def get_user_orders(
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100),
    current_user: dict = Depends(get_current_user)
):
    """Get current user's orders"""
    log_request("GET", "/api/v1/orders/", current_user["email"])
    
    return await order_service.list_user_orders(
        current_user["user_id"], page, per_page
    )

@router.get("/{order_id}", response_model=Order)
async def get_order(
    order_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get a specific order"""
    log_request("GET", f"/api/v1/orders/{order_id}", current_user["email"])
    
    order = await order_service.get_order(order_id, current_user["user_id"])
    if not order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found"
        )
    
    return order

@router.patch("/{order_id}", response_model=Order)
async def update_order(
    order_id: str,
    order_data: OrderPatch,
    current_user: dict = Depends(get_current_user)
):
    """Partially update an order (customers can only update notes)"""
    log_request("PATCH", f"/api/v1/orders/{order_id}", current_user["email"])

    # Convert to OrderUpdate for service layer compatibility
    allowed_updates = OrderUpdate(notes=order_data.notes)
    
    updated_order = await order_service.update_order(
        order_id, allowed_updates, current_user["user_id"]
    )
    
    if not updated_order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found"
        )
    
    return updated_order

@router.post("/{order_id}/cancel")
async def cancel_order(
    order_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Cancel an order"""
    log_request("POST", f"/api/v1/orders/{order_id}/cancel", current_user["email"])
    
    success = await order_service.cancel_order(order_id, current_user["user_id"])
    
    if not success:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to cancel order"
        )
    
    return {"message": "Order cancelled successfully"}

@router.post("/{order_id}/payment")
async def create_payment_for_order(
    order_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Create payment link for an order"""
    log_request("POST", f"/api/v1/orders/{order_id}/payment", current_user["email"])
    
    # Get order details
    order = await order_service.get_order(order_id, current_user["user_id"])
    if not order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found"
        )
    
    # Check if order can be paid
    if order.payment_status != PaymentStatus.PENDING:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Order payment is not pending"
        )
    
    if order.status == OrderStatus.CANCELLED:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Cannot pay for cancelled order"
        )
    
    # Get user profile for payment details
    from app.db.client import db
    profile = await db.get_record("profiles", current_user["user_id"])
    
    # Create payment checkout session
    from app.models.payment import PaymentCreate

    payment_data = PaymentCreate(
        order_id=order_id,
        amount=order.final_amount,
        currency="usd",  # or get from config
        customer_email=current_user["email"],
        description=f"Payment for Order #{order.order_number}"
    )

    payment_result = await payment_service.create_checkout_session(payment_data)
    
    log_order_event("PAYMENT_INITIATED", order_id, current_user["email"])
    
    return {
        "checkout_url": payment_result["checkout_url"],
        "session_id": payment_result["session_id"],
        "amount": payment_result["amount"],
        "currency": payment_result["currency"],
        "order_id": order_id,
        "status": payment_result["status"]
    }

@router.get("/{order_id}/payment/status")
async def get_order_payment_status(
    order_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get payment status for an order"""
    log_request("GET", f"/api/v1/orders/{order_id}/payment/status", current_user["email"])
    
    # Verify user owns the order
    order = await order_service.get_order(order_id, current_user["user_id"])
    if not order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found"
        )
    
    # Get payment records for this order
    from app.db.client import db
    payments = await db.get_records("payments", {"order_id": order_id})
    
    if not payments:
        return {
            "order_id": order_id,
            "payment_status": order.payment_status,
            "payments": []
        }
    
    # Get the latest payment
    latest_payment = max(payments, key=lambda p: p["created_at"])
    
    return {
        "order_id": order_id,
        "payment_status": order.payment_status,
        "latest_payment": {
            "session_id": latest_payment.get("stripe_checkout_session_id"),
            "payment_intent_id": latest_payment.get("stripe_payment_intent_id"),
            "amount": latest_payment["amount"],
            "currency": latest_payment["currency"],
            "status": latest_payment["status"],
            "checkout_url": latest_payment.get("checkout_url"),
            "created_at": latest_payment["created_at"]
        },
        "payments": payments
    }

# Admin endpoints (in a real app, these would be in a separate admin router with proper permissions)
@router.get("/admin/all", response_model=OrderListResponse)
async def get_all_orders_admin(
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100),
    status: Optional[OrderStatus] = None,
    payment_status: Optional[PaymentStatus] = None,
    current_user: dict = Depends(get_current_user)
):
    """Get all orders (admin only)"""
    log_request("GET", "/api/v1/orders/admin/all", current_user["email"])
    
    # In a real app, check if user has admin privileges
    # For now, any authenticated user can access this
    
    try:
        from app.db.client import db
        from app.models.order import OrderResponse
        from decimal import Decimal
        from datetime import datetime
        
        filters = {}
        if status:
            filters["status"] = status
        if payment_status:
            filters["payment_status"] = payment_status
        
        offset = (page - 1) * per_page
        orders_data = await db.get_records("orders", filters, per_page, offset)
        
        # Get total count
        total_orders = await db.get_records("orders", filters)
        total = len(total_orders)
        
        orders = []
        for order_data in orders_data:
            # Get items count
            order_items = await db.get_records("order_items", {"order_id": order_data["id"]})
            
            order_response = OrderResponse(
                id=order_data["id"],
                order_number=order_data["order_number"],
                status=order_data["status"],
                payment_status=order_data["payment_status"],
                total_amount=Decimal(order_data["total_amount"]),
                final_amount=Decimal(order_data["final_amount"]),
                currency=order_data["currency"],
                created_at=datetime.fromisoformat(order_data["created_at"]),
                items_count=len(order_items),
                shipping_address=order_data["shipping_address"]
            )
            orders.append(order_response)
        
        return OrderListResponse(
            orders=orders,
            total=total,
            page=page,
            per_page=per_page,
            total_pages=(total + per_page - 1) // per_page
        )
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve orders"
        )

@router.patch("/admin/{order_id}", response_model=Order)
async def update_order_admin(
    order_id: str,
    order_data: OrderAdminPatch,
    current_user: dict = Depends(get_current_user)
):
    """Partially update any order (admin only) - allows more fields"""
    log_request("PATCH", f"/api/v1/orders/admin/{order_id}", current_user["email"])

    # In a real app, check if user has admin privileges
    # For now, any authenticated user can access this

    # Convert to OrderUpdate for service layer compatibility
    admin_updates = OrderUpdate(
        status=order_data.status,
        payment_status=order_data.payment_status,
        tracking_number=order_data.tracking_number,
        notes=order_data.notes
    )

    updated_order = await order_service.update_order(order_id, admin_updates)
    
    if not updated_order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found"
        )
    
    return updated_order
