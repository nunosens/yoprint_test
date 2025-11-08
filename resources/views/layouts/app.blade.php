<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CSV Uploader')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-item {
            @apply px-4 py-3 rounded-xl transition-all duration-300 ease-in-out flex items-center space-x-3 w-full;
        }

        .nav-item.active {
            @apply bg-blue-500 text-white shadow-lg transform scale-[1.02];
        }

        .nav-item:not(.active) {
            @apply text-gray-600 hover:bg-gray-100 hover:text-gray-900;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-bar {
            background: linear-gradient(90deg, #3b82f6, #60a5fa, #3b82f6);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .sidebar {
            transition: all 0.3s ease-in-out;
        }

        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobileMenuButton"
            class="bg-white/90 backdrop-blur-lg rounded-xl p-3 shadow-lg border border-gray-200/60">
            <i class="fas fa-bars text-gray-700 text-lg"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <div
        class="sidebar fixed left-0 top-0 h-full w-80 bg-white/80 backdrop-blur-lg border-r border-gray-200/60 z-40 lg:transform-none">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-200/60">
            <div class="flex items-center space-x-3">
                <div
                    class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-csv text-white text-xl"></i>
                </div>
                <div>
                    <h1
                        class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                        CSV Processor
                    </h1>
                    <p class="text-xs text-gray-500">Professional Data Management</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="p-6 space-y-2">
            <ul>
                <li class="mb-2 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-2xl p-4">
                    <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                        <i class="fas fa-upload text-lg w-6 text-center"></i>
                        <span class="font-medium">Upload CSV</span>
                    </a>
                </li>
                <li class="mb-2 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-2xl p-4">
                    <a href="/products" class="nav-item {{ request()->is('products') ? 'active' : '' }}">
                        <i class="fas fa-list text-lg w-6 text-center"></i>
                        <span class="font-medium">View Products</span>
                    </a>
                </li>
            </ul>


        </div>

        <!-- Stats Section -->
        <div class="p-6 border-t border-gray-200/60 mt-4">
            <div class="space-y-4">
                <div
                    class="text-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100/60">
                    <div class="text-2xl font-bold text-gray-800" id="globalProductCount">0</div>
                    <div class="text-sm text-gray-600 mt-1">Total Products</div>
                </div>
                <div
                    class="text-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-100/60">
                    <div class="text-2xl font-bold text-gray-800" id="processedFiles">0</div>
                    <div class="text-sm text-gray-600 mt-1">Processed Files</div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="p-6 border-t border-gray-200/60 mt-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">System Online</span>
                </div>
                <i class="fas fa-server text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content min-h-screen">
        <main class="px-6 py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white/50 border-t border-gray-200/60 mt-16">
            <div class="px-6 py-6">
                <div class="text-center text-gray-500 text-sm">
                    <p>CSV Processor &copy; 2024 - Professional Data Management System</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-30 hidden"></div>

    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.querySelector('.sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');

        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            mobileOverlay.classList.toggle('hidden');
        });

        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.add('hidden');
        });

        // Load global stats
        async function loadGlobalStats() {
            try {
                const response = await fetch('/products/data?page=1&limit=1');
                const result = await response.json();
                document.getElementById('globalProductCount').textContent = result.total.toLocaleString();

                // Load processed files count
                const uploadsResponse = await fetch('/uploads');
                const uploadsResult = await uploadsResponse.json();
                document.getElementById('processedFiles').textContent = uploadsResult.uploads.length.toLocaleString();
            } catch (error) {
                console.error('Error loading global stats:', error);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadGlobalStats();

            // Add fade-in animation to all content
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.classList.add('fade-in');
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
