from typing import Optional, Dict, Any, List
from app.core.config import SUPABASE_URL, SUPABASE_KEY, SUPABASE_SERVICE_KEY, validate_config
from app.core.logging import logger
import os

# Validate configuration on import
try:
    validate_config()
except ValueError as e:
    logger.warning(f"Configuration validation failed: {e}")
    logger.warning("Some features may not work properly without proper configuration")

# Initialize Supabase clients with error handling
supabase = None
admin_supabase = None

try:
    from supabase import create_client, Client

    # Create Supabase client with error handling
    if SUPABASE_URL and SUPABASE_KEY:
        supabase: Client = create_client(SUPABASE_URL, SUPABASE_KEY)
        logger.info("✅ Supabase client initialized successfully")

        # Create admin client for operations that require elevated permissions
        if SUPABASE_SERVICE_KEY:
            admin_supabase: Client = create_client(SUPABASE_URL, SUPABASE_SERVICE_KEY)
            logger.info("✅ Supabase admin client initialized successfully")
        else:
            admin_supabase = supabase
            logger.warning("⚠️ Using regular client as admin client (SUPABASE_SERVICE_KEY not provided)")
    else:
        logger.error("❌ Supabase configuration missing")

except ImportError as e:
    logger.error(f"❌ Failed to import Supabase: {e}")
    logger.error("Please install supabase: pip install supabase")
except Exception as e:
    logger.error(f"❌ Failed to initialize Supabase client: {e}")
    logger.error("This might be due to compatibility issues with Python 3.13")
    logger.error("Consider using Python 3.11 or 3.12 for better compatibility")

class DatabaseClient:
    """Database client wrapper for Supabase operations"""

    def __init__(self):
        self.client = supabase
        self.admin_client = admin_supabase

        if not self.client:
            logger.warning("⚠️ Database client not available - some features will not work")

    def _check_client(self):
        """Check if client is available"""
        if not self.client:
            raise Exception("Database client not initialized. Please check your Supabase configuration.")
    
    async def create_record(self, table: str, data: Dict[str, Any]) -> Optional[Dict[str, Any]]:
        """Create a new record in the specified table"""
        try:
            self._check_client()
            response = self.client.table(table).insert(data).execute()
            if response.data:
                logger.info(f"Created record in {table}: {response.data[0].get('id', 'unknown')}")
                return response.data[0]
            return None
        except Exception as e:
            logger.error(f"Error creating record in {table}: {str(e)}")
            raise
    
    async def get_record(self, table: str, record_id: str, id_column: str = "id") -> Optional[Dict[str, Any]]:
        """Get a single record by ID"""
        try:
            response = self.client.table(table).select("*").eq(id_column, record_id).execute()
            if response.data:
                return response.data[0]
            return None
        except Exception as e:
            logger.error(f"Error getting record from {table}: {str(e)}")
            raise
    
    async def get_records(self, table: str, filters: Optional[Dict[str, Any]] = None, 
                         limit: Optional[int] = None, offset: Optional[int] = None) -> List[Dict[str, Any]]:
        """Get multiple records with optional filtering"""
        try:
            query = self.client.table(table).select("*")
            
            if filters:
                for key, value in filters.items():
                    query = query.eq(key, value)
            
            if limit:
                query = query.limit(limit)
            
            if offset:
                query = query.offset(offset)
            
            response = query.execute()
            return response.data or []
        except Exception as e:
            logger.error(f"Error getting records from {table}: {str(e)}")
            raise
    
    async def update_record(self, table: str, record_id: str, data: Dict[str, Any], 
                           id_column: str = "id") -> Optional[Dict[str, Any]]:
        """Update a record by ID"""
        try:
            response = self.client.table(table).update(data).eq(id_column, record_id).execute()
            if response.data:
                logger.info(f"Updated record in {table}: {record_id}")
                return response.data[0]
            return None
        except Exception as e:
            logger.error(f"Error updating record in {table}: {str(e)}")
            raise
    
    async def delete_record(self, table: str, record_id: str, id_column: str = "id") -> bool:
        """Delete a record by ID"""
        try:
            response = self.client.table(table).delete().eq(id_column, record_id).execute()
            logger.info(f"Deleted record from {table}: {record_id}")
            return True
        except Exception as e:
            logger.error(f"Error deleting record from {table}: {str(e)}")
            raise
    
    async def search_records(self, table: str, column: str, search_term: str, 
                            limit: Optional[int] = None) -> List[Dict[str, Any]]:
        """Search records using text search"""
        try:
            query = self.client.table(table).select("*").ilike(column, f"%{search_term}%")
            
            if limit:
                query = query.limit(limit)
            
            response = query.execute()
            return response.data or []
        except Exception as e:
            logger.error(f"Error searching records in {table}: {str(e)}")
            raise

# Create global database client instance
if supabase:
    db = DatabaseClient()
else:
    # Use mock client if Supabase is not available
    logger.warning("🔧 Supabase not available, using mock database client")
    from app.db.mock_client import MockDatabaseClient
    db = MockDatabaseClient()
