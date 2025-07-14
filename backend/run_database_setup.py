#!/usr/bin/env python3
"""
Database Setup Script for E-Commerce FastAPI Backend
This script runs all necessary SQL files to set up the database schema and RLS policies.
"""

import os
import sys
from pathlib import Path
import psycopg2
from psycopg2 import sql
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

def get_database_url():
    """Get database URL from environment variables"""
    # Try different environment variable names
    db_url = os.getenv('DATABASE_URL')
    if not db_url:
        # Construct from Supabase variables
        supabase_url = os.getenv('SUPABASE_URL', '')
        supabase_key = os.getenv('SUPABASE_SERVICE_KEY', '')
        
        if 'supabase.co' in supabase_url:
            # Extract project ID from Supabase URL
            project_id = supabase_url.replace('https://', '').replace('.supabase.co', '')
            db_url = f"postgresql://postgres:[YOUR-PASSWORD]@db.{project_id}.supabase.co:5432/postgres"
    
    return db_url

def run_sql_file(cursor, file_path):
    """Run a SQL file"""
    print(f"📄 Running SQL file: {file_path}")
    
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            sql_content = file.read()
        
        # Execute the SQL content
        cursor.execute(sql_content)
        print(f"✅ Successfully executed: {file_path}")
        return True
        
    except Exception as e:
        print(f"❌ Error executing {file_path}: {str(e)}")
        return False

def main():
    """Main function to run database setup"""
    print("🚀 Starting Database Setup for E-Commerce FastAPI Backend")
    print("=" * 60)
    
    # Get database URL
    db_url = get_database_url()
    if not db_url or '[YOUR-PASSWORD]' in db_url:
        print("❌ Database URL not properly configured.")
        print("Please set DATABASE_URL in your .env file or configure Supabase credentials.")
        print("\nFor Supabase, you can run these SQL files manually in the Supabase SQL Editor:")
        print("1. database_setup.sql")
        print("2. add_role_migration.sql") 
        print("3. comprehensive_rls_fix.sql")
        return False
    
    # SQL files to run in order
    sql_files = [
        'database_setup.sql',
        'add_role_migration.sql',
        'comprehensive_rls_fix.sql'
    ]
    
    try:
        # Connect to database
        print(f"🔌 Connecting to database...")
        conn = psycopg2.connect(db_url)
        conn.autocommit = True
        cursor = conn.cursor()
        
        print("✅ Connected to database successfully!")
        print()
        
        # Run each SQL file
        success_count = 0
        for sql_file in sql_files:
            file_path = Path(__file__).parent / sql_file
            
            if not file_path.exists():
                print(f"⚠️  SQL file not found: {sql_file}")
                continue
                
            if run_sql_file(cursor, file_path):
                success_count += 1
            print()
        
        # Close connection
        cursor.close()
        conn.close()
        
        print("=" * 60)
        print(f"🎉 Database setup completed!")
        print(f"✅ Successfully executed {success_count}/{len(sql_files)} SQL files")
        
        if success_count == len(sql_files):
            print("\n🎯 All database tables and policies are now set up!")
            print("You can now start using the FastAPI backend.")
        else:
            print(f"\n⚠️  {len(sql_files) - success_count} files had errors.")
            print("Please check the error messages above and fix any issues.")
        
        return success_count == len(sql_files)
        
    except psycopg2.Error as e:
        print(f"❌ Database connection error: {str(e)}")
        print("\nIf you're using Supabase, please run the SQL files manually:")
        print("1. Go to your Supabase project dashboard")
        print("2. Navigate to SQL Editor")
        print("3. Run each SQL file in this order:")
        for i, sql_file in enumerate(sql_files, 1):
            print(f"   {i}. {sql_file}")
        return False
        
    except Exception as e:
        print(f"❌ Unexpected error: {str(e)}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
