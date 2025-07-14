import os
import uuid
import shutil
from typing import List, Optional
from fastapi import UploadFile, HTTPException, status
from pathlib import Path
from PIL import Image
import aiofiles
from app.core.logging import logger
from app.models.product import ImageUploadResponse

class ImageService:
    """Service for handling image uploads and processing"""
    
    def __init__(self):
        # Create uploads directory if it doesn't exist
        self.upload_dir = Path("uploads/products")
        self.upload_dir.mkdir(parents=True, exist_ok=True)
        
        # Allowed image types
        self.allowed_types = {
            "image/jpeg", "image/jpg", "image/png", "image/webp", "image/gif"
        }
        
        # Max file size (5MB)
        self.max_file_size = 5 * 1024 * 1024
    
    def validate_image(self, file: UploadFile) -> bool:
        """Validate uploaded image file"""
        # Check content type
        if file.content_type not in self.allowed_types:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Invalid file type. Allowed types: {', '.join(self.allowed_types)}"
            )
        
        # Check file size (this is approximate, actual size checked during upload)
        if hasattr(file, 'size') and file.size > self.max_file_size:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"File too large. Maximum size: {self.max_file_size // (1024*1024)}MB"
            )
        
        return True
    
    def generate_filename(self, original_filename: str) -> str:
        """Generate unique filename"""
        # Get file extension
        ext = Path(original_filename).suffix.lower()
        if not ext:
            ext = '.jpg'  # Default extension
        
        # Generate unique filename
        unique_id = str(uuid.uuid4())
        return f"{unique_id}{ext}"
    
    async def save_image(self, file: UploadFile) -> ImageUploadResponse:
        """Save uploaded image to disk"""
        try:
            # Validate the image
            self.validate_image(file)
            
            # Generate unique filename
            filename = self.generate_filename(file.filename)
            file_path = self.upload_dir / filename
            
            # Save file
            async with aiofiles.open(file_path, 'wb') as f:
                content = await file.read()
                
                # Check actual file size
                if len(content) > self.max_file_size:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"File too large. Maximum size: {self.max_file_size // (1024*1024)}MB"
                    )
                
                await f.write(content)
            
            # Generate URL (you might want to use a CDN or cloud storage in production)
            image_url = f"/uploads/products/{filename}"
            
            logger.info(f"Image saved successfully: {filename}")
            
            return ImageUploadResponse(
                url=image_url,
                filename=filename,
                size=len(content),
                content_type=file.content_type
            )
            
        except HTTPException:
            raise
        except Exception as e:
            logger.error(f"Error saving image: {str(e)}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Failed to save image"
            )
    
    async def save_multiple_images(self, files: List[UploadFile]) -> List[ImageUploadResponse]:
        """Save multiple images"""
        if len(files) > 10:  # Limit to 10 images per product
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Maximum 10 images allowed per product"
            )
        
        uploaded_images = []
        for file in files:
            if file.filename:  # Skip empty files
                image_response = await self.save_image(file)
                uploaded_images.append(image_response)
        
        return uploaded_images
    
    def delete_image(self, filename: str) -> bool:
        """Delete image from disk"""
        try:
            file_path = self.upload_dir / filename
            if file_path.exists():
                file_path.unlink()
                logger.info(f"Image deleted: {filename}")
                return True
            return False
        except Exception as e:
            logger.error(f"Error deleting image {filename}: {str(e)}")
            return False
    
    def resize_image(self, file_path: Path, max_width: int = 1200, max_height: int = 1200) -> bool:
        """Resize image to maximum dimensions (optional optimization)"""
        try:
            with Image.open(file_path) as img:
                # Calculate new size maintaining aspect ratio
                img.thumbnail((max_width, max_height), Image.Resampling.LANCZOS)
                
                # Save optimized image
                img.save(file_path, optimize=True, quality=85)
                
            return True
        except Exception as e:
            logger.error(f"Error resizing image {file_path}: {str(e)}")
            return False

# Global instance
image_service = ImageService()
