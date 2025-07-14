#!/bin/bash

# Start script for E-Commerce API on Render

echo "🚀 Starting E-Commerce API..."

# Set Python path
export PYTHONPATH=/opt/render/project/src/backend:$PYTHONPATH

# Change to backend directory
cd /opt/render/project/src/backend

# Start the application
echo "📡 Starting uvicorn server..."
uvicorn app.main:app --host 0.0.0.0 --port ${PORT:-8000} --workers 1
