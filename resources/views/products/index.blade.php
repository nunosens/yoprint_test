@extends('layouts.app')

@section('title', 'Products - CSV Processor')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                Product Catalog
            </h1>
            <p class="text-gray-600 mt-2">Manage and view all your imported products</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="refreshProducts()" 
                    class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-2">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100/60 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <!-- Search -->
                <div class="relative flex-1 sm:flex-initial">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search products..." 
                           class="pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 transition-all duration-300">
                </div>
                
                <!-- Sort -->
                <select id="sortSelect" onchange="applySort()"
                        class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    <option value="id_desc">Newest First</option>
                    <option value="id_asc">Oldest First</option>
                    <option value="product_title_asc">Name A-Z</option>
                    <option value="product_title_desc">Name Z-A</option>
                    <option value="piece_price_desc">Price: High to Low</option>
                    <option value="piece_price_asc">Price: Low to High</option>
                </select>
            </div>
            
            <!-- Results Count -->
            <div class="text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg">
                <span id="resultsCount">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Products Table Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100/60 overflow-hidden">
        <!-- Loading State -->
        <div id="loadingState" class="p-12 text-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="loading-bar h-2 rounded-full max-w-md w-full"></div>
                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading products...</span>
                </div>
            </div>
        </div>

        <!-- Content Area (hidden by default) -->
        <div id="contentArea" class="hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors duration-200" onclick="sortProducts('unique_key')">
                                <div class="flex items-center space-x-1">
                                    <span>Unique Key</span>
                                    <i id="sort_unique_key" class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors duration-200" onclick="sortProducts('product_title')">
                                <div class="flex items-center space-x-1">
                                    <span>Product Title</span>
                                    <i id="sort_product_title" class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors duration-200" onclick="sortProducts('style_number')">
                                <div class="flex items-center space-x-1">
                                    <span>Style #</span>
                                    <i id="sort_style_number" class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Color</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors duration-200" onclick="sortProducts('piece_price')">
                                <div class="flex items-center space-x-1">
                                    <span>Price</span>
                                    <i id="sort_piece_price" class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="productsTable" class="bg-white divide-y divide-gray-200">
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="hidden p-12 text-center">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-500 mb-2">No products found</h3>
                    <p class="text-gray-400 mb-6">Upload a CSV file to see products here.</p>
                    <a href="/" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 inline-flex items-center space-x-2">
                        <i class="fas fa-upload"></i>
                        <span>Upload CSV</span>
                    </a>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="bg-white px-6 py-4 border-t border-gray-200">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let currentSearch = '';
    let sortField = 'id';
    let sortDirection = 'desc';
    let isLoading = false;

    // Load products on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadProducts();
        setupEventListeners();
    });

    // Setup event listeners
    function setupEventListeners() {
        // Search on enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });

        // Debounced search
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchProducts();
            }, 500);
        });
    }

    // Load products function
    async function loadProducts(page = 1) {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        
        showLoading();
        hideEmptyState();
        hideContent();
        
        try {
            const params = new URLSearchParams({
                page: page,
                search: currentSearch,
                sort_field: sortField,
                sort_direction: sortDirection
            });

            const response = await fetch(`/products/data?${params}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();

            // Simulate loading for better UX (bisa dihapus jika terlalu lama)
            await new Promise(resolve => setTimeout(resolve, 500));
            
            displayProducts(result.products);
            updatePagination(result);
            updateSortIndicators();
            updateResultsCount(result);
            
            hideLoading();
            showContent();
            
        } catch (error) {
            console.error('Error loading products:', error);
            hideLoading();
            showError('Failed to load products: ' + error.message);
        } finally {
            isLoading = false;
        }
    }

    // Display products in table
    function displayProducts(products) {
        const tableBody = document.getElementById('productsTable');
        
        if (products.length === 0) {
            showEmptyState();
            hideContent();
            return;
        }

        tableBody.innerHTML = products.map(product => `
            <tr class="hover:bg-gray-50/50 transition-colors duration-200 group">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 font-mono">${product.unique_key}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-gray-900 max-w-xs truncate group-hover:text-blue-600 transition-colors duration-200" title="${product.product_title}">
                        ${product.product_title}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-500 max-w-md truncate" title="${product.product_description}">
                        ${product.product_description}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium">${product.style_number}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 rounded-full border border-gray-300 shadow-sm" style="background-color: ${getColorHex(product.sanmar_mainframe_color)}"></div>
                        <span class="text-sm text-gray-900">${product.color_name}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${product.size}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-green-600">
                        $${parseFloat(product.piece_price).toFixed(2)}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Simple color mapping function
    function getColorHex(colorName) {
        const colorMap = {
            'black': '#000000', 'white': '#FFFFFF', 'red': '#DC2626',
            'blue': '#2563EB', 'green': '#059669', 'yellow': '#D97706',
            'purple': '#7C3AED', 'pink': '#DB2777', 'orange': '#EA580C',
            'gray': '#6B7280', 'navy': '#1E3A8A', 'brown': '#92400E',
        };
        
        const lowerColor = colorName.toLowerCase();
        return colorMap[lowerColor] || '#9CA3AF';
    }

    // Update pagination
    function updatePagination(data) {
        const paginationDiv = document.getElementById('pagination');
        
        if (data.last_page <= 1) {
            paginationDiv.innerHTML = '';
            return;
        }

        let paginationHtml = `
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    ${data.current_page > 1 ? 
                        `<button onclick="loadProducts(${data.current_page - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">Previous</button>` :
                        `<button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-300 bg-white">Previous</button>`
                    }
                    ${data.has_more ? 
                        `<button onclick="loadProducts(${data.current_page + 1})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">Next</button>` :
                        `<button disabled class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-300 bg-white">Next</button>`
                    }
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">${((data.current_page - 1) * 20) + 1}</span> to 
                            <span class="font-medium">${Math.min(data.current_page * 20, data.total)}</span> of 
                            <span class="font-medium">${data.total}</span> results
                        </p>
                    </div>
                    <div class="flex space-x-1">
                        ${data.current_page > 1 ? 
                            `<button onclick="loadProducts(${data.current_page - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg transition-colors duration-200">Previous</button>` :
                            `<button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300 rounded-lg">Previous</button>`
                        }
        `;

        // Page numbers - show limited pages
        const startPage = Math.max(1, data.current_page - 2);
        const endPage = Math.min(data.last_page, data.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === data.current_page) {
                paginationHtml += `<button class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600 rounded-lg">${i}</button>`;
            } else {
                paginationHtml += `<button onclick="loadProducts(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg transition-colors duration-200">${i}</button>`;
            }
        }

        paginationHtml += `
                        ${data.has_more ? 
                            `<button onclick="loadProducts(${data.current_page + 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg transition-colors duration-200">Next</button>` :
                            `<button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300 rounded-lg">Next</button>`
                        }
                    </div>
                </div>
            </div>
        `;

        paginationDiv.innerHTML = paginationHtml;
    }

    // Search products
    function searchProducts() {
        currentSearch = document.getElementById('searchInput').value;
        currentPage = 1;
        loadProducts();
    }

    // Apply sort from select
    function applySort() {
        const sortValue = document.getElementById('sortSelect').value;
        const [field, direction] = sortValue.split('_');
        sortField = field;
        sortDirection = direction;
        loadProducts(currentPage);
    }

    // Sort products by clicking header
    function sortProducts(field) {
        if (sortField === field) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'asc';
        }
        
        // Update select to match
        const sortSelect = document.getElementById('sortSelect');
        sortSelect.value = `${sortField}_${sortDirection}`;
        
        loadProducts(currentPage);
    }

    // Update sort indicators
    function updateSortIndicators() {
        // Reset all indicators
        document.querySelectorAll('[id^="sort_"]').forEach(indicator => {
            indicator.className = 'fas fa-sort text-gray-400';
        });
        
        // Set current sort indicator
        const currentIndicator = document.getElementById(`sort_${sortField}`);
        if (currentIndicator) {
            currentIndicator.className = sortDirection === 'asc' ? 
                'fas fa-sort-up text-blue-500' : 'fas fa-sort-down text-blue-500';
        }
    }

    // Update results count
    function updateResultsCount(data) {
        const resultsElement = document.getElementById('resultsCount');
        const start = ((data.current_page - 1) * 20) + 1;
        const end = Math.min(data.current_page * 20, data.total);
        
        resultsElement.innerHTML = `
            Showing <span class="font-semibold">${start}-${end}</span> of 
            <span class="font-semibold">${data.total}</span> products
        `;
    }

    // Refresh products
    function refreshProducts() {
        loadProducts(currentPage);
    }

    // UI helper functions
    function showLoading() {
        document.getElementById('loadingState').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingState').classList.add('hidden');
    }

    function showContent() {
        document.getElementById('contentArea').classList.remove('hidden');
    }

    function hideContent() {
        document.getElementById('contentArea').classList.add('hidden');
    }

    function showEmptyState() {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('productsTable').innerHTML = '';
    }

    function hideEmptyState() {
        document.getElementById('emptyState').classList.add('hidden');
    }

    function showError(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 fade-in';
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
</script>
@endsection