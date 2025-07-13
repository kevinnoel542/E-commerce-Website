from fastapi import APIRouter, HTTPException, status, Depends, Query
from typing import Optional
from app.models.product import (
    Product, ProductCreate, ProductUpdate, ProductListResponse, 
    ProductSearchRequest, Category, CategoryCreate, CategoryUpdate
)
from app.services.product_service import product_service
from app.core.security import get_current_user
from app.core.logging import log_request

router = APIRouter()

@router.get("/", response_model=ProductListResponse)
async def get_products(
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100),
    category_id: Optional[str] = None,
    active_only: bool = True
):
    """Get list of products with pagination"""
    log_request("GET", "/api/v1/products/")
    return await product_service.list_products(page, per_page, category_id, active_only)

@router.get("/search", response_model=ProductListResponse)
async def search_products(
    query: Optional[str] = None,
    category_id: Optional[str] = None,
    min_price: Optional[float] = None,
    max_price: Optional[float] = None,
    brand: Optional[str] = None,
    in_stock_only: bool = False,
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100)
):
    """Search products with filters"""
    log_request("GET", "/api/v1/products/search")
    
    search_request = ProductSearchRequest(
        query=query,
        category_id=category_id,
        min_price=min_price,
        max_price=max_price,
        brand=brand,
        in_stock_only=in_stock_only,
        page=page,
        per_page=per_page
    )
    
    return await product_service.search_products(search_request)

@router.get("/{product_id}", response_model=Product)
async def get_product(product_id: str):
    """Get a specific product by ID"""
    log_request("GET", f"/api/v1/products/{product_id}")
    
    product = await product_service.get_product(product_id)
    if not product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found"
        )
    
    return product

@router.post("/", response_model=Product)
async def create_product(
    product_data: ProductCreate,
    current_user: dict = Depends(get_current_user)
):
    """Create a new product (admin only)"""
    log_request("POST", "/api/v1/products/", current_user["email"])
    
    # In a real application, you'd check if user has admin privileges
    # For now, any authenticated user can create products
    
    return await product_service.create_product(product_data, current_user["user_id"])

@router.put("/{product_id}", response_model=Product)
async def update_product(
    product_id: str,
    product_data: ProductUpdate,
    current_user: dict = Depends(get_current_user)
):
    """Update a product (admin only)"""
    log_request("PUT", f"/api/v1/products/{product_id}", current_user["email"])
    
    # Check if product exists
    existing_product = await product_service.get_product(product_id)
    if not existing_product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found"
        )
    
    updated_product = await product_service.update_product(
        product_id, product_data, current_user["user_id"]
    )
    
    if not updated_product:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to update product"
        )
    
    return updated_product

@router.delete("/{product_id}")
async def delete_product(
    product_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Delete a product (admin only)"""
    log_request("DELETE", f"/api/v1/products/{product_id}", current_user["email"])
    
    # Check if product exists
    existing_product = await product_service.get_product(product_id)
    if not existing_product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found"
        )
    
    success = await product_service.delete_product(product_id, current_user["user_id"])
    
    if not success:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete product"
        )
    
    return {"message": "Product deleted successfully"}

# Category endpoints
@router.get("/categories/", response_model=list[Category])
async def get_categories():
    """Get list of all categories"""
    log_request("GET", "/api/v1/products/categories/")
    
    try:
        from app.db.client import db
        categories_data = await db.get_records("categories", {"is_active": True})
        return [Category(**category) for category in categories_data]
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve categories"
        )

@router.post("/categories/", response_model=Category)
async def create_category(
    category_data: CategoryCreate,
    current_user: dict = Depends(get_current_user)
):
    """Create a new category (admin only)"""
    log_request("POST", "/api/v1/products/categories/", current_user["email"])
    
    try:
        from app.db.client import db
        import uuid
        from datetime import datetime
        
        category_dict = category_data.dict()
        category_dict.update({
            "id": str(uuid.uuid4()),
            "created_by": current_user["user_id"],
            "created_at": datetime.utcnow().isoformat(),
            "is_active": True
        })
        
        created_category = await db.create_record("categories", category_dict)
        return Category(**created_category)
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to create category"
        )

@router.get("/categories/{category_id}", response_model=Category)
async def get_category(category_id: str):
    """Get a specific category by ID"""
    log_request("GET", f"/api/v1/products/categories/{category_id}")
    
    try:
        from app.db.client import db
        category_data = await db.get_record("categories", category_id)
        if not category_data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Category not found"
            )
        
        return Category(**category_data)
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to retrieve category"
        )

@router.put("/categories/{category_id}", response_model=Category)
async def update_category(
    category_id: str,
    category_data: CategoryUpdate,
    current_user: dict = Depends(get_current_user)
):
    """Update a category (admin only)"""
    log_request("PUT", f"/api/v1/products/categories/{category_id}", current_user["email"])
    
    try:
        from app.db.client import db
        from datetime import datetime
        
        # Check if category exists
        existing_category = await db.get_record("categories", category_id)
        if not existing_category:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Category not found"
            )
        
        update_dict = category_data.dict(exclude_unset=True)
        update_dict["updated_at"] = datetime.utcnow().isoformat()
        update_dict["updated_by"] = current_user["user_id"]
        
        updated_category = await db.update_record("categories", category_id, update_dict)
        return Category(**updated_category)
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to update category"
        )

@router.delete("/categories/{category_id}")
async def delete_category(
    category_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Delete a category (admin only)"""
    log_request("DELETE", f"/api/v1/products/categories/{category_id}", current_user["email"])
    
    try:
        from app.db.client import db
        from datetime import datetime
        
        # Check if category exists
        existing_category = await db.get_record("categories", category_id)
        if not existing_category:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Category not found"
            )
        
        # Soft delete by setting is_active to False
        await db.update_record("categories", category_id, {
            "is_active": False,
            "updated_at": datetime.utcnow().isoformat(),
            "updated_by": current_user["user_id"]
        })
        
        return {"message": "Category deleted successfully"}
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete category"
        )
