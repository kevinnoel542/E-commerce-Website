from typing import Optional, Dict, Any, List
import os
import json
from decimal import Decimal
from datetime import datetime

# Initialize variables
supabase = None
admin_supabase = None

def convert_for_json(data):
    """Convert data to be JSON serializable"""
    if isinstance(data, dict):
        return {key: convert_for_json(value) for key, value in data.items()}
    elif isinstance(data, list):
        return [convert_for_json(item) for item in data]
    elif isinstance(data, Decimal):
        return float(data)
    elif isinstance(data, datetime):
        return data.isoformat()
    elif hasattr(data, '__dict__'):
        # Handle any object with attributes
        return convert_for_json(data.__dict__)
    else:
        # Try to convert to basic types
        try:
            import json
            json.dumps(data)  # Test if it's already serializable
            return data
        except TypeError:
            # If not serializable, convert to string as fallback
            return str(data)

# Try to import configuration
try:
    from app.core.config import SUPABASE_URL, SUPABASE_KEY, SUPABASE_SERVICE_KEY, validate_config
    from app.core.logging import logger

    # Validate configuration
    validate_config()
except Exception:
    # Fallback configuration
    SUPABASE_URL = os.getenv("SUPABASE_URL")
    SUPABASE_KEY = os.getenv("SUPABASE_KEY")
    SUPABASE_SERVICE_KEY = os.getenv("SUPABASE_SERVICE_KEY")

    import logging
    logger = logging.getLogger(__name__)

# Try to create Supabase clients with error handling
try:
    from supabase import create_client, Client

    if SUPABASE_URL and SUPABASE_KEY:
        # Create Supabase clients
        supabase: Client = create_client(SUPABASE_URL, SUPABASE_KEY)

        # Create admin client for operations that require elevated permissions
        admin_supabase: Client = create_client(SUPABASE_URL, SUPABASE_SERVICE_KEY) if SUPABASE_SERVICE_KEY else supabase

        logger.info("Supabase clients initialized successfully")
    else:
        logger.warning("Supabase configuration missing")

except Exception as e:
    logger.error(f"Failed to initialize Supabase: {str(e)}")
    logger.error("This is likely due to compatibility issues with Python 3.13")
    logger.error("Consider using Python 3.11 or 3.12 for better compatibility")
    raise

class DatabaseClient:
    """Database client wrapper for Supabase operations"""

    def __init__(self):
        self.client = supabase
        self.admin_client = admin_supabase
    
    async def create_record(self, table: str, data: Dict[str, Any]) -> Optional[Dict[str, Any]]:
        """Create a new record in the specified table"""
        try:
            # Convert data to be JSON serializable
            logger.info(f"Original data types: {[(k, type(v).__name__) for k, v in data.items()]}")
            serializable_data = convert_for_json(data)
            logger.info(f"Converted data types: {[(k, type(v).__name__) for k, v in serializable_data.items()]}")

            response = self.client.table(table).insert(serializable_data).execute()
            if response.data:
                logger.info(f"Created record in {table}: {response.data[0].get('id', 'unknown')}")
                return response.data[0]
            return None
        except Exception as e:
            logger.error(f"Error creating record in {table}: {str(e)}")
            raise

    async def create_record_admin(self, table: str, data: Dict[str, Any]) -> Optional[Dict[str, Any]]:
        """Create a new record using admin client (bypasses RLS)"""
        try:
            # Convert data to be JSON serializable
            logger.info(f"Creating record in {table} with admin client")
            serializable_data = convert_for_json(data)

            response = self.admin_client.table(table).insert(serializable_data).execute()
            if response.data:
                logger.info(f"Created record in {table} (admin): {response.data[0].get('id', 'unknown')}")
                return response.data[0]
            return None
        except Exception as e:
            logger.error(f"Error creating record in {table} (admin): {str(e)}")
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

    async def get_record_admin(self, table: str, record_id: str, id_column: str = "id") -> Optional[Dict[str, Any]]:
        """Get a single record by ID using admin client (bypasses RLS)"""
        try:
            response = self.admin_client.table(table).select("*").eq(id_column, record_id).execute()
            if response.data:
                logger.info(f"Retrieved record from {table} (admin): {record_id}")
                return response.data[0]
            return None
        except Exception as e:
            logger.error(f"Error getting record from {table} (admin): {str(e)}")
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

    async def get_records_admin(self, table: str, filters: Optional[Dict[str, Any]] = None,
                               limit: Optional[int] = None, offset: Optional[int] = None) -> List[Dict[str, Any]]:
        """Get multiple records with optional filtering using admin client (bypasses RLS)"""
        try:
            query = self.admin_client.table(table).select("*")

            if filters:
                for key, value in filters.items():
                    query = query.eq(key, value)

            if limit:
                query = query.limit(limit)

            if offset:
                query = query.offset(offset)

            response = query.execute()
            logger.info(f"Retrieved {len(response.data or [])} records from {table} (admin)")
            return response.data or []
        except Exception as e:
            logger.error(f"Error getting records from {table} (admin): {str(e)}")
            raise

    async def update_record(self, table: str, record_id: str, data: Dict[str, Any],
                           id_column: str = "id") -> Optional[Dict[str, Any]]:
        """Update a record by ID"""
        try:
            # Convert data to be JSON serializable
            serializable_data = convert_for_json(data)
            response = self.client.table(table).update(serializable_data).eq(id_column, record_id).execute()
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
db = DatabaseClient()
