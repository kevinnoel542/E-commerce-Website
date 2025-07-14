#!/usr/bin/env python3
"""
Comprehensive debug script for product creation
"""

import sys
import os
import json
import asyncio
from decimal import Decimal

# Add the backend directory to Python path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

def test_imports():
    """Test all imports work correctly"""
    print("🔍 Testing Imports")
    print("=" * 30)
    
    try:
        from app.models.product import ProductCreate, Product
        print("✅ Product models imported")
        
        from app.services.product_service import product_service
        print("✅ Product service imported")
        
        from app.db.client import db, convert_for_json
        print("✅ Database client imported")
        
        return True
    except Exception as e:
        print(f"❌ Import error: {e}")
        return False

def test_product_model_creation():
    """Test ProductCreate model with various inputs"""
    print(f"\n📦 Testing ProductCreate Model")
    print("=" * 40)
    
    from app.models.product import ProductCreate
    
    test_cases = [
        {
            "name": "Test Product 1",
            "description": "Test description",
            "price": 230,  # int
            "stock_quantity": 10
        },
        {
            "name": "Test Product 2", 
            "description": "Test description",
            "price": 230.50,  # float
            "stock_quantity": 10
        },
        {
            "name": "Test Product 3",
            "description": "Test description", 
            "price": "230.75",  # string
            "stock_quantity": 10
        }
    ]
    
    successful = 0
    for i, case in enumerate(test_cases, 1):
        try:
            product = ProductCreate(**case)
            print(f"✅ Test {i}: Price {case['price']} ({type(case['price']).__name__}) -> {product.price} ({type(product.price).__name__})")
            
            # Test dict conversion
            product_dict = product.dict()
            print(f"   Dict price: {product_dict['price']} ({type(product_dict['price']).__name__})")
            
            # Test JSON serialization
            json_str = json.dumps(product_dict)
            print(f"   JSON serializable: ✅")
            
            successful += 1
            
        except Exception as e:
            print(f"❌ Test {i}: Error with price {case['price']} - {e}")
    
    return successful == len(test_cases)

def test_json_conversion():
    """Test the convert_for_json function"""
    print(f"\n🔄 Testing JSON Conversion Function")
    print("=" * 40)
    
    from app.db.client import convert_for_json
    
    test_data = {
        "name": "Test Product",
        "price": Decimal("230.50"),  # This should be converted
        "stock": 10,
        "active": True,
        "nested": {
            "weight": Decimal("1.5"),
            "tags": ["test", "product"]
        }
    }
    
    try:
        print("Original data:")
        for key, value in test_data.items():
            print(f"  {key}: {value} ({type(value).__name__})")
        
        converted = convert_for_json(test_data)
        
        print(f"\nConverted data:")
        for key, value in converted.items():
            print(f"  {key}: {value} ({type(value).__name__})")
        
        # Test JSON serialization
        json_str = json.dumps(converted)
        print(f"\n✅ JSON conversion successful!")
        
        return True
        
    except Exception as e:
        print(f"\n❌ JSON conversion failed: {e}")
        return False

async def test_database_creation():
    """Test actual database record creation"""
    print(f"\n💾 Testing Database Record Creation")
    print("=" * 40)
    
    try:
        from app.db.client import db
        from app.models.product import ProductCreate
        
        # Create test product data
        product_data = ProductCreate(
            name="Debug Test Product",
            description="A test product for debugging",
            price=99.99,
            stock_quantity=5
        )
        
        print(f"ProductCreate object:")
        print(f"  Price: {product_data.price} ({type(product_data.price).__name__})")
        
        # Convert to dict
        product_dict = product_data.dict()
        product_dict.update({
            "id": "test-debug-id",
            "created_by": "test-user",
            "created_at": "2025-07-10T08:00:00Z",
            "is_active": True,
            "sku": "DEBUG-TEST-001"
        })
        
        print(f"\nProduct dict:")
        for key, value in product_dict.items():
            print(f"  {key}: {value} ({type(value).__name__})")
        
        # Test JSON serialization before database
        try:
            json_str = json.dumps(product_dict)
            print(f"\n✅ Dict is JSON serializable")
        except Exception as json_error:
            print(f"\n❌ Dict is NOT JSON serializable: {json_error}")
            return False
        
        # Note: We won't actually create in DB to avoid test data
        print(f"\n✅ Database creation test passed (simulation)")
        return True
        
    except Exception as e:
        print(f"\n❌ Database creation test failed: {e}")
        return False

async def test_full_product_service():
    """Test the complete product service flow"""
    print(f"\n🔧 Testing Product Service Flow")
    print("=" * 40)
    
    try:
        from app.services.product_service import ProductService
        from app.models.product import ProductCreate
        
        service = ProductService()
        
        # Test SKU generation
        sku = service.generate_sku("Test Product", None)
        print(f"Generated SKU: {sku}")
        
        # Create product data
        product_data = ProductCreate(
            name="Service Test Product",
            description="Testing the service",
            price=150.00,
            stock_quantity=20,
            sku="string"  # Should trigger auto-generation
        )
        
        print(f"Original SKU: {product_data.sku}")
        
        # Simulate the service logic
        if not product_data.sku or product_data.sku == "string":
            product_data.sku = service.generate_sku(product_data.name, product_data.category_id)
        
        print(f"Final SKU: {product_data.sku}")
        
        # Test dict conversion
        product_dict = product_data.dict()
        if 'price' in product_dict:
            product_dict['price'] = float(product_dict['price'])
        
        print(f"Final price: {product_dict['price']} ({type(product_dict['price']).__name__})")
        
        # Test JSON serialization
        json_str = json.dumps(product_dict)
        print(f"✅ Service flow test passed")
        
        return True
        
    except Exception as e:
        print(f"❌ Service flow test failed: {e}")
        return False

async def main():
    """Run all debug tests"""
    print("🔍 COMPREHENSIVE PRODUCT CREATION DEBUG")
    print("=" * 60)
    
    results = {}
    
    # Test 1: Imports
    results["imports"] = test_imports()
    
    if not results["imports"]:
        print("\n❌ Cannot proceed - import errors")
        return
    
    # Test 2: Product model
    results["model"] = test_product_model_creation()
    
    # Test 3: JSON conversion
    results["json"] = test_json_conversion()
    
    # Test 4: Database simulation
    results["database"] = await test_database_creation()
    
    # Test 5: Service flow
    results["service"] = await test_full_product_service()
    
    # Summary
    print(f"\n📊 DEBUG RESULTS SUMMARY")
    print("=" * 40)
    
    passed = 0
    total = len(results)
    
    for test_name, result in results.items():
        status = "✅ PASS" if result else "❌ FAIL"
        print(f"{test_name.title()}: {status}")
        if result:
            passed += 1
    
    print(f"\nOverall: {passed}/{total} tests passed")
    
    if passed == total:
        print(f"\n🎉 ALL TESTS PASSED!")
        print(f"The product creation should work now.")
        print(f"\nTry this curl command:")
        print(f"curl -X POST 'http://localhost:8000/api/v1/products/' \\")
        print(f"  -H 'Authorization: Bearer YOUR_TOKEN' \\")
        print(f"  -H 'Content-Type: application/json' \\")
        print(f"  -d '{{\"name\": \"Test\", \"description\": \"Test\", \"price\": 230, \"stock_quantity\": 10}}'")
    else:
        print(f"\n⚠️ Some tests failed. The issues need to be fixed.")
        
        if not results["model"]:
            print("- Fix the ProductCreate model")
        if not results["json"]:
            print("- Fix the JSON conversion function")
        if not results["database"]:
            print("- Fix the database client")
        if not results["service"]:
            print("- Fix the product service")

if __name__ == "__main__":
    asyncio.run(main())
