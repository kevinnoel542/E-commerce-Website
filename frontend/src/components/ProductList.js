import React, { useState, useEffect } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { productsAPI } from "../services/api";
import { useApp } from "../context/AppContext";
import SearchBar from "./SearchBar";

export default function ProductList() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [searchQuery, setSearchQuery] = useState("");
  const { addToCart } = useApp();
  const [searchParams] = useSearchParams();

  useEffect(() => {
    // Get search query from URL params
    const urlSearchQuery = searchParams.get("search") || "";
    setSearchQuery(urlSearchQuery);
  }, [searchParams]);

  useEffect(() => {
    fetchProducts();
    fetchCategories();
  }, [page, selectedCategory, searchQuery]);

  const fetchProducts = async () => {
    try {
      setLoading(true);

      if (searchQuery) {
        // Use search API
        const response = await productsAPI.searchProducts({
          query: searchQuery,
          category_id: selectedCategory,
          page,
          per_page: 20,
        });
        setProducts(response.data?.products || []);
        setTotalPages(response.data?.total_pages || 1);
      } else {
        // Use regular products API
        const response = await productsAPI.getProducts(
          page,
          20,
          selectedCategory
        );
        setProducts(response.data?.products || []);
        setTotalPages(response.data?.total_pages || 1);
      }

      setError(null);
    } catch (error) {
      console.error("Error fetching products:", error);
      // Provide fallback data when API is not available
      setProducts([
        {
          id: 1,
          name: "Sample Product 1",
          description: "This is a sample product for demonstration",
          price: 29.99,
          images: ["https://via.placeholder.com/300x200?text=Product+1"],
          stock_quantity: 10,
        },
        {
          id: 2,
          name: "Sample Product 2",
          description: "Another sample product",
          price: 49.99,
          images: ["https://via.placeholder.com/300x200?text=Product+2"],
          stock_quantity: 5,
        },
        {
          id: 3,
          name: "Sample Product 3",
          description: "Third sample product",
          price: 19.99,
          images: ["https://via.placeholder.com/300x200?text=Product+3"],
          stock_quantity: 15,
        },
      ]);
      setError("Using sample data - backend not available");
    } finally {
      setLoading(false);
    }
  };

  const fetchCategories = async () => {
    try {
      const response = await productsAPI.getCategories();
      setCategories(response.data || []);
    } catch (error) {
      console.error("Error fetching categories:", error);
      // Provide fallback categories
      setCategories([
        { id: 1, name: "Electronics" },
        { id: 2, name: "Clothing" },
        { id: 3, name: "Books" },
        { id: 4, name: "Home" },
      ]);
    }
  };

  const handleCategoryChange = (categoryId) => {
    setSelectedCategory(categoryId);
    setPage(1);
  };

  const handleSearch = (query) => {
    setSearchQuery(query);
    setPage(1);
  };

  const handleAddToCart = (product) => {
    try {
      addToCart(product, 1);
      alert("Product added to cart!");
    } catch (error) {
      console.error("Error adding to cart:", error);
      alert("Failed to add product to cart.");
    }
  };

  if (loading) {
    return (
      <div className="p-6 max-w-7xl mx-auto">
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-indigo-600"></div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-6 max-w-7xl mx-auto">
        <div className="text-center text-red-600 text-xl">{error}</div>
      </div>
    );
  }

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <h2 className="text-3xl font-extrabold mb-8 text-center text-gray-900">
        Our Products
      </h2>

      {/* Search Bar */}
      <div className="mb-6 max-w-md mx-auto">
        <SearchBar onSearch={handleSearch} placeholder="Search products..." />
      </div>

      {/* Search Results Info */}
      {searchQuery && (
        <div className="mb-4 text-center text-gray-600">
          {loading ? "Searching..." : `Search results for "${searchQuery}"`}
          {!loading && products.length === 0 && " - No products found"}
        </div>
      )}

      {/* Category Filter */}
      <div className="mb-8 flex flex-wrap gap-2 justify-center">
        <button
          onClick={() => handleCategoryChange(null)}
          className={`px-4 py-2 rounded-lg font-medium transition ${
            selectedCategory === null
              ? "bg-indigo-600 text-white"
              : "bg-gray-200 text-gray-700 hover:bg-gray-300"
          }`}
        >
          All Categories
        </button>
        {categories &&
          categories.map((category) => (
            <button
              key={category.id}
              onClick={() => handleCategoryChange(category.id)}
              className={`px-4 py-2 rounded-lg font-medium transition ${
                selectedCategory === category.id
                  ? "bg-indigo-600 text-white"
                  : "bg-gray-200 text-gray-700 hover:bg-gray-300"
              }`}
            >
              {category.name}
            </button>
          ))}
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        {products && products.length > 0 ? (
          products.map((product) => (
            <div
              key={product.id}
              className="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 ease-in-out"
            >
              <img
                src={
                  product.images[0] ||
                  "https://via.placeholder.com/300x200?text=No+Image"
                }
                alt={product.name}
                className="rounded-t-lg w-full h-48 object-cover"
              />
              <div className="p-6">
                <h3 className="text-xl font-semibold mb-2 text-gray-800">
                  {product.name}
                </h3>
                <p className="text-gray-600 text-sm mb-2 line-clamp-2">
                  {product.description}
                </p>
                <p className="text-indigo-600 font-bold text-lg mb-4">
                  ${parseFloat(product.price).toFixed(2)}
                </p>
                <div className="flex gap-2">
                  <Link to={`/product/${product.id}`} className="flex-1">
                    <button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md font-semibold transition-colors">
                      View Details
                    </button>
                  </Link>
                  <button
                    onClick={() => handleAddToCart(product)}
                    className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold transition-colors"
                    disabled={product.stock_quantity === 0}
                  >
                    {product.stock_quantity === 0
                      ? "Out of Stock"
                      : "Add to Cart"}
                  </button>
                </div>
                {product.stock_quantity > 0 && product.stock_quantity <= 5 && (
                  <p className="text-orange-600 text-sm mt-2">
                    Only {product.stock_quantity} left in stock!
                  </p>
                )}
              </div>
            </div>
          ))
        ) : (
          <div className="col-span-full text-center py-12">
            <div className="text-gray-500 text-lg mb-4">
              {loading ? "Loading products..." : "No products found"}
            </div>
            {!loading && (
              <p className="text-gray-400">
                Try adjusting your search or check back later.
              </p>
            )}
          </div>
        )}
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="flex justify-center mt-8 space-x-2">
          <button
            onClick={() => setPage(page - 1)}
            disabled={page === 1}
            className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300 transition"
          >
            Previous
          </button>
          <span className="px-4 py-2 bg-indigo-600 text-white rounded-lg">
            Page {page} of {totalPages}
          </span>
          <button
            onClick={() => setPage(page + 1)}
            disabled={page === totalPages}
            className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300 transition"
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
}
