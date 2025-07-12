from fastapi import APIRouter, HTTPException, status, Depends, Query, UploadFile, File, Form
from typing import Optional, List
from app.models.product import (
    Product, ProductCreate, ProductUpdate, ProductPatch, ProductListResponse,
    ProductSearchRequest, Category, CategoryCreate, CategoryUpdate, CategoryPatch,
    ImageUploadResponse, ProductWithImages
)
from app.services.product_service import product_service
from app.services.image_service import image_service
from app.core.security import get_current_user, require_admin
from app.core.logging import log_request, log_error, logger
from app.db.client import db

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
    current_admin: dict = Depends(require_admin)
):
    """Create a new product (admin only)"""
    log_request("POST", "/api/v1/products/", current_admin["email"])

    return await product_service.create_product(product_data, current_admin["user_id"])

@router.patch("/{product_id}", response_model=Product)
async def update_product(
    product_id: str,
    product_data: ProductPatch,
    current_admin: dict = Depends(require_admin)
):
    """Partially update a product (admin only) - limited fields for safety"""
    log_request("PATCH", f"/api/v1/products/{product_id}", current_admin["email"])
    
    # Check if product exists
    existing_product = await product_service.get_product(product_id)
    if not existing_product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found"
        )
    
    updated_product = await product_service.update_product(
        product_id, product_data, current_admin["user_id"]
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
    current_admin: dict = Depends(require_admin)
):
    """Delete a product (admin only)"""
    log_request("DELETE", f"/api/v1/products/{product_id}", current_admin["email"])
    
    # Check if product exists
    existing_product = await product_service.get_product(product_id)
    if not existing_product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found"
        )
    
    success = await product_service.delete_product(product_id, current_admin["user_id"])
    
    if not success:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete product"
        )
    
    return {"message": "Product deleted successfully"}

# Category endpoints
@router.get("/categories/", response_model=List[Category])
async def get_categories():
    """Get list of all categories"""
    log_request("GET", "/api/v1/products/categories/")

    try:
        logger.info("Attempting to fetch categories from database")

        # Try to get categories with better error handling
        try:
            # Use the supabase client directly for better control
            from app.db.client import supabase
            response = supabase.table("categories").select("*").eq("is_active", True).execute()
            categories_data = response.data if response.data else []
        except Exception as db_error:
            logger.error(f"Database error: {str(db_error)}")

            # Check if table doesn't exist
            if "relation" in str(db_error).lower() and "does not exist" in str(db_error).lower():
                logger.warning("Categories table doesn't exist, returning empty list")
                return []

            # Try to get all categories without filter
            try:
                from app.db.client import supabase
                response = supabase.table("categories").select("*").execute()
                all_categories = response.data if response.data else []
                # Filter active categories in Python
                categories_data = [cat for cat in all_categories if cat.get("is_active", True)]
            except Exception:
                logger.error("Failed to fetch categories even without filter")
                return []

        logger.info(f"Found {len(categories_data)} categories")

        if not categories_data:
            return []

        # Convert to Category objects with error handling
        categories = []
        for category_data in categories_data:
            try:
                categories.append(Category(**category_data))
            except Exception as e:
                logger.warning(f"Failed to parse category {category_data.get('id', 'unknown')}: {e}")
                continue

        return categories

    except Exception as e:
        logger.error(f"Unexpected error fetching categories: {str(e)}")
        # Return empty list instead of failing
        return []

@router.post("/categories/", response_model=Category)
async def create_category(
    category_data: CategoryCreate,
    current_admin: dict = Depends(require_admin)
):
    """Create a new category (admin only)"""
    log_request("POST", "/api/v1/products/categories/", current_admin["email"])

    try:
        from app.db.client import db
        import uuid
        from datetime import datetime

        category_dict = category_data.dict()

        # Handle parent_id validation
        if category_dict.get("parent_id"):
            parent_id = category_dict["parent_id"]

            # Try to parse as UUID to validate format
            try:
                uuid.UUID(parent_id)
            except ValueError:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Invalid parent_id format. Must be a valid UUID or null for root categories."
                )

            # Verify parent category exists
            parent_category = await db.get_record("categories", parent_id)
            if not parent_category:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail="Parent category not found"
                )

            # Verify parent category is active
            if not parent_category.get("is_active", True):
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Cannot create subcategory under inactive parent category"
                )
        else:
            # Set parent_id to None for root categories
            category_dict["parent_id"] = None

        category_dict.update({
            "id": str(uuid.uuid4()),
            "created_at": datetime.utcnow().isoformat(),
            "is_active": True
            # Note: created_by column doesn't exist in current database schema
        })

        created_category = await db.create_record("categories", category_dict)
        logger.info(f"Category created by admin {current_admin['email']}: {created_category['name']}")
        return Category(**created_category)

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error creating category: {str(e)}")
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
        from app.core.logging import logger
        logger.error(f"Error fetching category {category_id}: {str(e)}")

        # Check if it's a table not found error
        if "relation" in str(e).lower() and "does not exist" in str(e).lower():
            raise HTTPException(
                status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
                detail="Categories table not found. Please run database setup."
            )

        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to retrieve category: {str(e)}"
        )

@router.patch("/categories/{category_id}", response_model=Category)
async def update_category(
    category_id: str,
    category_data: CategoryPatch,
    current_admin: dict = Depends(require_admin)
):
    """Partially update a category (admin only) - limited fields for safety"""
    log_request("PATCH", f"/api/v1/products/categories/{category_id}", current_admin["email"])
    
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
        # Note: updated_by column doesn't exist in current database schema

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
    current_admin: dict = Depends(require_admin)
):
    """Delete a category (admin only)"""
    log_request("DELETE", f"/api/v1/products/categories/{category_id}", current_admin["email"])
    
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
            "updated_at": datetime.utcnow().isoformat()
            # Note: updated_by column doesn't exist in current database schema
        })

        return {"message": "Category deleted successfully"}
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error deleting category {category_id}: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete category"
        )

# Image upload endpoints
@router.post("/upload-image", response_model=ImageUploadResponse)
async def upload_product_image(
    file: UploadFile = File(...),
    current_admin: dict = Depends(require_admin)
):
    """Upload a single product image (admin only)"""
    log_request("POST", "/api/v1/products/upload-image", current_admin["email"])

    try:
        image_response = await image_service.save_image(file)
        logger.info(f"Image uploaded by admin {current_admin['email']}: {image_response.filename}")
        return image_response
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Uploading image for admin {current_admin['email']}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to upload image"
        )

@router.post("/upload-images", response_model=List[ImageUploadResponse])
async def upload_product_images(
    files: List[UploadFile] = File(...),
    current_admin: dict = Depends(require_admin)
):
    """Upload multiple product images (admin only)"""
    log_request("POST", "/api/v1/products/upload-images", current_admin["email"])

    try:
        image_responses = await image_service.save_multiple_images(files)
        logger.info(f"Uploaded {len(image_responses)} images by admin {current_admin['email']}")
        return image_responses
    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Uploading images for admin {current_admin['email']}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to upload images"
        )

@router.post("/create-with-images", response_model=ProductWithImages)
async def create_product_with_images(
    name: str = Form(...),
    description: str = Form(...),
    price: float = Form(...),
    category_id: Optional[str] = Form(None),
    brand: Optional[str] = Form(None),
    stock_quantity: int = Form(0),
    images: List[UploadFile] = File([]),
    current_admin: dict = Depends(require_admin)
):
    """Create a product with images in one request (admin only)"""
    log_request("POST", "/api/v1/products/create-with-images", current_admin["email"])

    try:
        # Upload images first
        uploaded_images = []
        if images and images[0].filename:  # Check if files were actually uploaded
            uploaded_images = await image_service.save_multiple_images(images)

        # Create product data
        image_urls = [img.url for img in uploaded_images]
        product_data = ProductCreate(
            name=name,
            description=description,
            price=price,
            category_id=category_id,
            brand=brand,
            stock_quantity=stock_quantity,
            images=image_urls
        )

        # Create product (SKU will be auto-generated)
        product = await product_service.create_product(product_data, current_admin["user_id"])

        logger.info(f"Product with images created by admin {current_admin['email']}: {product.id}")

        return ProductWithImages(
            product=product,
            uploaded_images=uploaded_images
        )

    except HTTPException:
        raise
    except Exception as e:
        log_error(e, f"Creating product with images for admin {current_admin['email']}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to create product with images"
        )
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete category"
        )
