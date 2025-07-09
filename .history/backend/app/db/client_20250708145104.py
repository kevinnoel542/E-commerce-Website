from typing import Optional, Dict, Any, List
import os

# Initialize variables
supabase = None
admin_supabase = None
SUPABASE_AVAILABLE = False

# Try to import configuration and logging
try:
    from app.core.config import SUPABASE_URL, SUPABASE_KEY, SUPABASE_SERVICE_KEY
    from app.core.logging import logger
except ImportError:
    # Fallback if config/logging not available
    SUPABASE_URL = os.getenv("SUPABASE_URL")
    SUPABASE_KEY = os.getenv("SUPABASE_KEY")
    SUPABASE_SERVICE_KEY = os.getenv("SUPABASE_SERVICE_KEY")

    # Simple logger fallback
    import logging
    logger = logging.getLogger(__name__)

# Try to import and initialize Supabase
try:
    from supabase import create_client
    SUPABASE_AVAILABLE = True

    # Only try to create clients if we have the required config
    if SUPABASE_URL and SUPABASE_KEY:
        try:
            supabase = create_client(SUPABASE_URL, SUPABASE_KEY)

            # Create admin client if service key is available
            if SUPABASE_SERVICE_KEY:
                admin_supabase = create_client(SUPABASE_URL, SUPABASE_SERVICE_KEY)
            else:
                admin_supabase = supabase

        except Exception:
            # Silently fail and use mock clients
            supabase = None
            admin_supabase = None
            SUPABASE_AVAILABLE = False

except ImportError:
    # Supabase not available, will use mock clients
    SUPABASE_AVAILABLE = False

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
            self._check_client()
            self.client.table(table).delete().eq(id_column, record_id).execute()
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
    from app.db.mock_auth import MockSupabase
    db = MockDatabaseClient()
    # Create mock supabase for auth routes
    supabase = MockSupabase()
