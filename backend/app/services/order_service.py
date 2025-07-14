from typing import List, Optional, Dict, Any
from decimal import Decimal
import uuid
from datetime import datetime
from app.db.client import db
from app.models.order import (
    Order, OrderCreate, OrderUpdate, OrderResponse, OrderListResponse,
    OrderStatus, PaymentStatus, OrderSummary, Cart, CartSummary, OrderItem
)
from app.services.product_service import product_service
from app.services.payment_service import payment_service
from app.core.logging import order_logger, log_order_event, log_error
from app.core.config import DEFAULT_CURRENCY
from fastapi import HTTPException, status

class OrderService:
    """Service for managing orders"""
    
    async def calculate_cart_summary(self, cart: Cart) -> CartSummary:
        """Calculate cart totals and return summary"""
        try:
            items_with_details = []
            subtotal = Decimal('0.00')
            
            for cart_item in cart.items:
                # Get product details
                product = await product_service.get_product(cart_item.product_id)
                if not product:
                    raise HTTPException(
                        status_code=status.HTTP_404_NOT_FOUND,
                        detail=f"Product {cart_item.product_id} not found"
                    )
                
                if not product.is_active:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"Product {product.name} is not available"
                    )
                
                if product.stock_quantity < cart_item.quantity:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"Insufficient stock for {product.name}. Available: {product.stock_quantity}"
                    )
                
                # Convert float price to Decimal for precise calculations
                unit_price_decimal = Decimal(str(product.price))
                item_total = unit_price_decimal * cart_item.quantity
                subtotal += item_total
                
                items_with_details.append({
                    "product_id": product.id,
                    "product_name": product.name,
                    "unit_price": unit_price_decimal,
                    "quantity": cart_item.quantity,
                    "total_price": item_total,
                    "stock_available": product.stock_quantity
                })
            
            # Calculate additional charges
            shipping_amount = self._calculate_shipping(subtotal)
            tax_amount = self._calculate_tax(subtotal)
            discount_amount = Decimal('0.00')  # Implement discount logic as needed
            
            total_amount = subtotal + shipping_amount + tax_amount - discount_amount
            
            summary = OrderSummary(
                subtotal=subtotal,
                shipping_amount=shipping_amount,
                tax_amount=tax_amount,
                discount_amount=discount_amount,
                total_amount=total_amount,
                currency=DEFAULT_CURRENCY
            )
            
            return CartSummary(items=items_with_details, summary=summary)
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, "Calculating cart summary")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to calculate cart summary"
            )
    
    async def create_order(self, order_data: OrderCreate, user_id: str, user_email: str) -> Order:
        """Create a new order"""
        try:
            # Validate cart and calculate totals
            cart = Cart(items=[{"product_id": item.product_id, "quantity": item.quantity} for item in order_data.items])
            cart_summary = await self.calculate_cart_summary(cart)
            
            # Generate order number
            order_number = f"ORD-{datetime.utcnow().strftime('%Y%m%d')}-{uuid.uuid4().hex[:8].upper()}"
            order_id = str(uuid.uuid4())
            
            # Create order record
            order_dict = {
                "id": order_id,
                "user_id": user_id,
                "order_number": order_number,
                "status": OrderStatus.PENDING.value,  # Explicitly convert enum to string
                "payment_status": PaymentStatus.PENDING.value,  # Explicitly convert enum to string
                "total_amount": str(cart_summary.summary.subtotal),
                "shipping_amount": str(cart_summary.summary.shipping_amount),
                "tax_amount": str(cart_summary.summary.tax_amount),
                "discount_amount": str(cart_summary.summary.discount_amount),
                "final_amount": str(cart_summary.summary.total_amount),
                "currency": DEFAULT_CURRENCY,
                "shipping_address": order_data.shipping_address.dict(),
                "notes": order_data.notes,
                "created_at": datetime.utcnow().isoformat()
            }
            
            # Use admin client for order creation to bypass RLS, but ensure user_id matches authenticated user
            created_order = await db.create_record_admin("orders", order_dict)
            
            # Create order items
            for i, item_data in enumerate(order_data.items):
                cart_item = cart_summary.items[i]
                order_item = {
                    "id": str(uuid.uuid4()),
                    "order_id": order_id,
                    "product_id": item_data.product_id,
                    "product_name": cart_item["product_name"],
                    "quantity": item_data.quantity,
                    "unit_price": str(cart_item["unit_price"]),  # Use price from cart calculation
                    "total_price": str(cart_item["total_price"]),
                    "created_at": datetime.utcnow().isoformat()
                }
                await db.create_record_admin("order_items", order_item)
            
            # Update product stock
            for item_data in order_data.items:
                product = await product_service.get_product(item_data.product_id)
                new_stock = product.stock_quantity - item_data.quantity
                await db.update_record("products", item_data.product_id, {
                    "stock_quantity": new_stock,
                    "updated_at": datetime.utcnow().isoformat()
                })
            
            log_order_event("CREATED", order_id, user_email)
            
            # Get complete order with items - use admin client to ensure we can read what we just created
            created_order_data = await db.get_record_admin("orders", order_id)
            if not created_order_data:
                raise HTTPException(
                    status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                    detail="Failed to retrieve created order"
                )

            # Get order items
            order_items = await db.get_records_admin("order_items", {"order_id": order_id})
            items = [OrderItem(**item) for item in order_items]

            created_order_data["items"] = items
            return Order(**created_order_data)
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Creating order for user {user_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to create order"
            )
    
    async def get_order(self, order_id: str, user_id: Optional[str] = None) -> Optional[Order]:
        """Get an order by ID"""
        try:
            # Try regular client first, then admin client if needed
            order_data = await db.get_record("orders", order_id)
            if not order_data:
                # If regular client fails (due to RLS), try admin client
                order_data = await db.get_record_admin("orders", order_id)
                if not order_data:
                    return None

            # Check user access
            if user_id and order_data["user_id"] != user_id:
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="Access denied"
                )

            # Get order items (try regular first, then admin)
            order_items = await db.get_records("order_items", {"order_id": order_id})
            if not order_items:
                order_items = await db.get_records_admin("order_items", {"order_id": order_id})

            items = [OrderItem(**item) for item in order_items]

            order_data["items"] = items
            return Order(**order_data)
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Getting order {order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to retrieve order"
            )
    
    async def update_order(self, order_id: str, order_data: OrderUpdate, user_id: Optional[str] = None) -> Optional[Order]:
        """Update an order"""
        try:
            # Check if order exists and user has access
            existing_order = await self.get_order(order_id, user_id)
            if not existing_order:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Order not found"
                )
            
            update_dict = order_data.dict(exclude_unset=True)
            update_dict["updated_at"] = datetime.utcnow().isoformat()
            
            updated_order = await db.update_record("orders", order_id, update_dict)
            log_order_event("UPDATED", order_id)
            
            return await self.get_order(order_id, user_id) if updated_order else None
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Updating order {order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to update order"
            )
    
    async def list_user_orders(self, user_id: str, page: int = 1, per_page: int = 20) -> OrderListResponse:
        """List orders for a user"""
        try:
            offset = (page - 1) * per_page
            orders_data = await db.get_records("orders", {"user_id": user_id}, per_page, offset)
            
            # Get total count
            total_orders = await db.get_records("orders", {"user_id": user_id})
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
            log_error(e, f"Listing orders for user {user_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to retrieve orders"
            )
    
    async def cancel_order(self, order_id: str, user_id: str) -> bool:
        """Cancel an order"""
        try:
            order = await self.get_order(order_id, user_id)
            if not order:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Order not found"
                )
            
            if order.status not in [OrderStatus.PENDING.value, OrderStatus.CONFIRMED.value]:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Order cannot be cancelled"
                )

            # Update order status
            await db.update_record("orders", order_id, {
                "status": OrderStatus.CANCELLED.value,  # Explicitly convert enum to string
                "updated_at": datetime.utcnow().isoformat()
            })
            
            # Restore product stock
            for item in order.items:
                product = await product_service.get_product(item.product_id)
                if product:
                    new_stock = product.stock_quantity + item.quantity
                    await db.update_record("products", item.product_id, {
                        "stock_quantity": new_stock,
                        "updated_at": datetime.utcnow().isoformat()
                    })
            
            log_order_event("CANCELLED", order_id, user_id)
            return True
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Cancelling order {order_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to cancel order"
            )
    
    def _calculate_shipping(self, subtotal: Decimal) -> Decimal:
        """Calculate shipping cost based on subtotal"""
        # Simple shipping calculation - customize as needed
        if subtotal >= Decimal('100.00'):
            return Decimal('0.00')  # Free shipping over 100
        return Decimal('10.00')  # Flat rate shipping
    
    def _calculate_tax(self, subtotal: Decimal) -> Decimal:
        """Calculate tax based on subtotal"""
        # Simple tax calculation - customize based on location/regulations
        tax_rate = Decimal('0.18')  # 18% VAT
        return subtotal * tax_rate

# Create global order service instance
order_service = OrderService()
