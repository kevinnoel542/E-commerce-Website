from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from fastapi.encoders import jsonable_encoder
from pathlib import Path
import json
from decimal import Decimal
from app.api.v1.routes import auth, products, orders, payments, stripe_payments, stripe_webhooks
from app.core.logging import logger

# Custom JSON encoder for Decimal
class DecimalEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, Decimal):
            return float(obj)
        return super().default(obj)

app = FastAPI(
    title="E-Commerce API",
    description="Full-featured e-commerce backend with FastAPI, Supabase, and Stripe",
    version="1.0.0"
)

# CORS setup
#allow_origins=["*"]


app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.on_event("startup")
def startup_event():
    logger.info("🚀 FastAPI E-Commerce application starting up...")

@app.on_event("shutdown")
def shutdown_event():
    logger.info("🛑 FastAPI E-Commerce application shutting down...")

@app.get("/")
def root():
    return {"message": "E-Commerce API is running!", "version": "1.0.0"}

@app.get("/health")
def health_check():
    return {"status": "healthy", "service": "e-commerce-api"}

# Register API routers
app.include_router(auth.router, prefix="/api/v1/auth", tags=["Authentication"])
app.include_router(products.router, prefix="/api/v1/products", tags=["Products"])
app.include_router(orders.router, prefix="/api/v1/orders", tags=["Orders"])
app.include_router(payments.router, prefix="/api/v1/payments", tags=["Payments (Legacy)"])
app.include_router(stripe_payments.router, prefix="/api/v1/stripe", tags=["Stripe Payments"])
app.include_router(stripe_webhooks.router, prefix="/api/v1/stripe", tags=["Stripe Webhooks"])

# Mount static files for image serving
uploads_dir = Path("uploads")
uploads_dir.mkdir(exist_ok=True)
app.mount("/uploads", StaticFiles(directory="uploads"), name="uploads")
