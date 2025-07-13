from typing import List, Optional, Dict, Any
from decimal import Decimal
import uuid
from datetime import datetime
from app.db.client import db
from app.models.product import (
    Product, ProductCreate, ProductUpdate, ProductResponse, 
    ProductListResponse, ProductSearchRequest, Category, CategoryCreate, CategoryUpdate
)
from app.core.logging import product_logger, log_error
from fastapi import HTTPException, status

class ProductService:
    """Service for managing products and categories"""
    
    async def create_product(self, product_data: ProductCreate, user_id: str) -> Product:
        """Create a new product"""
        try:
            # Generate unique SKU if not provided
            if not product_data.sku:
                product_data.sku = f"PRD-{uuid.uuid4().hex[:8].upper()}"
            
            # Check if SKU already exists
            existing_product = await db.get_records("products", {"sku": product_data.sku})
            if existing_product:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Product with this SKU already exists"
                )
            
            product_dict = product_data.dict()
            product_dict.update({
                "id": str(uuid.uuid4()),
                "created_by": user_id,
                "created_at": datetime.utcnow().isoformat(),
                "is_active": True
            })
            
            created_product = await db.create_record("products", product_dict)
            product_logger.info(f"Product created: {created_product['id']} by user {user_id}")
            
            return Product(**created_product)
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Creating product for user {user_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to create product"
            )
    
    async def get_product(self, product_id: str) -> Optional[Product]:
        """Get a product by ID"""
        try:
            product_data = await db.get_record("products", product_id)
            if not product_data:
                return None
            
            return Product(**product_data)
        
        except Exception as e:
            log_error(e, f"Getting product {product_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to retrieve product"
            )
    
    async def update_product(self, product_id: str, product_data: ProductUpdate, user_id: str) -> Optional[Product]:
        """Update a product"""
        try:
            # Check if product exists
            existing_product = await self.get_product(product_id)
            if not existing_product:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Product not found"
                )
            
            # Check SKU uniqueness if updating SKU
            if product_data.sku and product_data.sku != existing_product.sku:
                existing_sku = await db.get_records("products", {"sku": product_data.sku})
                if existing_sku:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail="Product with this SKU already exists"
                    )
            
            update_dict = product_data.dict(exclude_unset=True)
            update_dict["updated_at"] = datetime.utcnow().isoformat()
            update_dict["updated_by"] = user_id
            
            updated_product = await db.update_record("products", product_id, update_dict)
            product_logger.info(f"Product updated: {product_id} by user {user_id}")
            
            return Product(**updated_product) if updated_product else None
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Updating product {product_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to update product"
            )
    
    async def delete_product(self, product_id: str, user_id: str) -> bool:
        """Soft delete a product"""
        try:
            # Check if product exists
            existing_product = await self.get_product(product_id)
            if not existing_product:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Product not found"
                )
            
            # Soft delete by setting is_active to False
            await db.update_record("products", product_id, {
                "is_active": False,
                "updated_at": datetime.utcnow().isoformat(),
                "updated_by": user_id
            })
            
            product_logger.info(f"Product deleted: {product_id} by user {user_id}")
            return True
        
        except HTTPException:
            raise
        except Exception as e:
            log_error(e, f"Deleting product {product_id}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to delete product"
            )
    
    async def list_products(self, page: int = 1, per_page: int = 20, 
                           category_id: Optional[str] = None, 
                           active_only: bool = True) -> ProductListResponse:
        """List products with pagination"""
        try:
            filters = {}
            if active_only:
                filters["is_active"] = True
            if category_id:
                filters["category_id"] = category_id
            
            offset = (page - 1) * per_page
            products_data = await db.get_records("products", filters, per_page, offset)
            
            # Get total count for pagination
            total_products = await db.get_records("products", filters)
            total = len(total_products)
            
            products = [ProductResponse(**product) for product in products_data]
            
            return ProductListResponse(
                products=products,
                total=total,
                page=page,
                per_page=per_page,
                total_pages=(total + per_page - 1) // per_page
            )
        
        except Exception as e:
            log_error(e, "Listing products")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to retrieve products"
            )
    
    async def search_products(self, search_request: ProductSearchRequest) -> ProductListResponse:
        """Search products with filters"""
        try:
            # This is a simplified search - in production, you'd use full-text search
            filters = {"is_active": True}
            
            if search_request.category_id:
                filters["category_id"] = search_request.category_id
            if search_request.brand:
                filters["brand"] = search_request.brand
            
            offset = (search_request.page - 1) * search_request.per_page
            products_data = await db.get_records("products", filters, search_request.per_page, offset)
            
            # Apply additional filters (price range, text search)
            filtered_products = []
            for product in products_data:
                # Price filter
                if search_request.min_price and Decimal(product["price"]) < search_request.min_price:
                    continue
                if search_request.max_price and Decimal(product["price"]) > search_request.max_price:
                    continue
                
                # Stock filter
                if search_request.in_stock_only and product["stock_quantity"] <= 0:
                    continue
                
                # Text search (simple implementation)
                if search_request.query:
                    query_lower = search_request.query.lower()
                    if (query_lower not in product["name"].lower() and 
                        query_lower not in product["description"].lower()):
                        continue
                
                filtered_products.append(product)
            
            products = [ProductResponse(**product) for product in filtered_products]
            
            return ProductListResponse(
                products=products,
                total=len(filtered_products),
                page=search_request.page,
                per_page=search_request.per_page,
                total_pages=(len(filtered_products) + search_request.per_page - 1) // search_request.per_page
            )
        
        except Exception as e:
            log_error(e, "Searching products")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to search products"
            )

# Create global product service instance
product_service = ProductService()
