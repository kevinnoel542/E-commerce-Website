#!/usr/bin/env python3
"""
Simple test to verify Decimal fix
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from app.models.product import ProductCreate
from app.db.client import convert_for_json
from decimal import Decimal
import json

def test_product_model():
    """Test ProductCreate model with different price types"""
    print("🧪 Testing ProductCreate Model")
    print("=" * 40)
    
    test_cases = [
        {"name": "Test 1", "price": 230, "description": "Integer price"},
        {"name": "Test 2", "price": 230.50, "description": "Float price"},
        {"name": "Test 3", "price": "230", "description": "String price"},
        {"name": "Test 4", "price": Decimal("230.75"), "description": "Decimal price"},
    ]
    
    for i, case in enumerate(test_cases, 1):
        try:
            product = ProductCreate(
                name=case["name"],
                description=case["description"],
                price=case["price"],
                stock_quantity=10
            )
            print(f"✅ Test {i}: {case['description']} - Price: {product.price} ({type(product.price).__name__})")
        except Exception as e:
            print(f"❌ Test {i}: {case['description']} - Error: {e}")

def test_json_conversion():
    """Test convert_for_json function"""
    print(f"\n🔄 Testing JSON Conversion")
    print("=" * 40)
    
    test_data = {
        "name": "Test Product",
        "price": Decimal("230.50"),
        "stock": 10,
        "active": True,
        "tags": ["electronics", "gadget"],
        "metadata": {
            "weight": Decimal("1.5"),
            "dimensions": [Decimal("10.0"), Decimal("5.0")]
        }
    }
    
    print("Original data:")
    for key, value in test_data.items():
        print(f"  {key}: {value} ({type(value).__name__})")
    
    try:
        converted = convert_for_json(test_data)
        print(f"\nConverted data:")
        for key, value in converted.items():
            print(f"  {key}: {value} ({type(value).__name__})")
        
        # Try to serialize to JSON
        json_str = json.dumps(converted)
        print(f"\n✅ JSON serialization successful!")
        print(f"JSON: {json_str}")
        return True
        
    except Exception as e:
        print(f"\n❌ JSON conversion failed: {e}")
        return False

def test_product_dict():
    """Test creating product dict like in the service"""
    print(f"\n📦 Testing Product Dict Creation")
    print("=" * 40)
    
    try:
        # Simulate what happens in product service
        product_data = ProductCreate(
            name="colgate",
            description="toothpaste used for cleaning teeth",
            price=230,
            category_id=None,  # Will be None instead of "string"
            brand=None,        # Will be None instead of "string"
            sku="PRD-COLGATE-ABC123",
            stock_quantity=10,
            images=[]
        )
        
        print(f"ProductCreate object created successfully")
        print(f"Price: {product_data.price} ({type(product_data.price).__name__})")
        
        # Convert to dict like in the service
        product_dict = product_data.dict()
        product_dict.update({
            "id": "test-uuid",
            "created_by": "test-user-id",
            "created_at": "2025-07-10T08:00:00Z",
            "is_active": True
        })
        
        print(f"\nProduct dict:")
        for key, value in product_dict.items():
            print(f"  {key}: {value} ({type(value).__name__})")
        
        # Test JSON conversion
        converted = convert_for_json(product_dict)
        json_str = json.dumps(converted)
        
        print(f"\n✅ Complete product creation simulation successful!")
        return True
        
    except Exception as e:
        print(f"\n❌ Product dict creation failed: {e}")
        return False

def main():
    """Run all tests"""
    print("🚀 Decimal Fix Verification Tests")
    print("=" * 50)
    
    # Test 1: Product model
    test_product_model()
    
    # Test 2: JSON conversion
    json_ok = test_json_conversion()
    
    # Test 3: Product dict (full simulation)
    product_ok = test_product_dict()
    
    print(f"\n📊 Test Results")
    print("=" * 30)
    print(f"JSON Conversion: {'✅ PASS' if json_ok else '❌ FAIL'}")
    print(f"Product Creation: {'✅ PASS' if product_ok else '❌ FAIL'}")
    
    if json_ok and product_ok:
        print(f"\n🎉 All tests passed! The Decimal fix should work.")
        print(f"\nNow try creating a product via the API:")
        print(f"curl -X POST 'http://localhost:8000/api/v1/products/' \\")
        print(f"  -H 'Authorization: Bearer YOUR_TOKEN' \\")
        print(f"  -H 'Content-Type: application/json' \\")
        print(f"  -d '{{\"name\": \"Test\", \"description\": \"Test\", \"price\": 230, \"stock_quantity\": 10}}'")
    else:
        print(f"\n⚠️ Some tests failed. Check the errors above.")

if __name__ == "__main__":
    main()
