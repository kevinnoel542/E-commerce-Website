from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.api.v1.routes import auth, products, orders, payments
from app.core.logging import logger

app = FastAPI(
    title="E-Commerce API",
    description="Full-featured e-commerce backend with FastAPI, Supabase, and Flutterwave",
    version="1.0.0"
)

# CORS setup
origins = [
    "http://localhost:3000",
    "http://localhost:3001",
    "https://yourdomain.com",
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
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
app.include_router(payments.router, prefix="/api/v1/payments", tags=["Payments"])
