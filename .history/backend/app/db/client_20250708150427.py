from typing import Optional, Dict, Any, List
from supabase import create_client, Client
from app.core.config import SUPABASE_URL, SUPABASE_KEY, SUPABASE_SERVICE_KEY, validate_config
from app.core.logging import logger

# Validate configuration
validate_config()

# Create Supabase clients
supabase: Client = create_client(SUPABASE_URL, SUPABASE_KEY)

# Create admin client for operations that require elevated permissions
admin_supabase: Client = create_client(SUPABASE_URL, SUPABASE_SERVICE_KEY) if SUPABASE_SERVICE_KEY else supabase

logger.info("✅ Supabase clients initialized successfully")

class DatabaseClient:
    """Database client wrapper for Supabase operations"""

    def __init__(self):
        self.client = supabase
        self.admin_client = admin_supabase
    
    async def create_record(self, table: str, data: Dict[str, Any]) -> Optional[Dict[str, Any]]:
        """Create a new record in the specified table"""
        try:
            self._check_client()
            response = self.client.table(table).insert(data).execute()
            if response.data:
                return response.data[0]
            return None
        except Exception:
            raise Exception(f"Failed to create record in {table}")

    async def get_record(self, table: str, record_id: str, id_column: str = "id") -> Optional[Dict[str, Any]]:
        """Get a single record by ID"""
        try:
            self._check_client()
            response = self.client.table(table).select("*").eq(id_column, record_id).execute()
            if response.data:
                return response.data[0]
            return None
        except Exception:
            raise Exception(f"Failed to get record from {table}")

    async def get_records(self, table: str, filters: Optional[Dict[str, Any]] = None,
                         limit: Optional[int] = None, offset: Optional[int] = None) -> List[Dict[str, Any]]:
        """Get multiple records with optional filtering"""
        try:
            self._check_client()
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
        except Exception:
            raise Exception(f"Failed to get records from {table}")

    async def update_record(self, table: str, record_id: str, data: Dict[str, Any],
                           id_column: str = "id") -> Optional[Dict[str, Any]]:
        """Update a record by ID"""
        try:
            self._check_client()
            response = self.client.table(table).update(data).eq(id_column, record_id).execute()
            if response.data:
                return response.data[0]
            return None
        except Exception:
            raise Exception(f"Failed to update record in {table}")

    async def delete_record(self, table: str, record_id: str, id_column: str = "id") -> bool:
        """Delete a record by ID"""
        try:
            self._check_client()
            self.client.table(table).delete().eq(id_column, record_id).execute()
            return True
        except Exception:
            raise Exception(f"Failed to delete record from {table}")

    async def search_records(self, table: str, column: str, search_term: str,
                            limit: Optional[int] = None) -> List[Dict[str, Any]]:
        """Search records using text search"""
        try:
            self._check_client()
            query = self.client.table(table).select("*").ilike(column, f"%{search_term}%")

            if limit:
                query = query.limit(limit)

            response = query.execute()
            return response.data or []
        except Exception:
            raise Exception(f"Failed to search records in {table}")

# Create global database client instance
try:
    if SUPABASE_AVAILABLE and supabase:
        db = DatabaseClient()
    else:
        # Use mock client if Supabase is not available
        from app.db.mock_client import MockDatabaseClient
        from app.db.mock_auth import MockSupabase
        db = MockDatabaseClient()
        # Create mock supabase for auth routes if not already available
        if not supabase:
            supabase = MockSupabase()
except Exception:
    # Fallback to mock client
    from app.db.mock_client import MockDatabaseClient
    from app.db.mock_auth import MockSupabase
    db = MockDatabaseClient()
    supabase = MockSupabase()
