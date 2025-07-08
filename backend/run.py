#!/usr/bin/env python3
"""
E-Commerce Backend Application Runner

This script provides a convenient way to run the FastAPI application
with different configurations for development and production.
"""

import os
import sys
import uvicorn
from pathlib import Path

# Add the app directory to Python path
app_dir = Path(__file__).parent / "app"
sys.path.insert(0, str(app_dir))

def main():
    """Main entry point for running the application"""
    
    # Get environment variables
    host = os.getenv("HOST", "0.0.0.0")
    port = int(os.getenv("PORT", 8000))
    debug = os.getenv("DEBUG", "True").lower() == "true"
    
    # Configure uvicorn based on environment
    if debug:
        # Development configuration
        uvicorn.run(
            "app.main:app",
            host=host,
            port=port,
            reload=True,
            reload_dirs=["app"],
            log_level="info",
            access_log=True
        )
    else:
        # Production configuration
        uvicorn.run(
            "app.main:app",
            host=host,
            port=port,
            reload=False,
            log_level="warning",
            access_log=False,
            workers=1  # Adjust based on your server capacity
        )

if __name__ == "__main__":
    main()
