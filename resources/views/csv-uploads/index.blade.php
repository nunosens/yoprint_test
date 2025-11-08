@extends('layouts.app')

@section('title', 'Upload CSV - CSV Processor')

@section('content')
<div class="fade-in">

    <!-- Upload Card -->
    <div class="mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100/60 overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-cloud-upload-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Upload Your CSV</h2>
                        <p class="text-gray-600 text-sm">Supported format: .csv, .txt (Max: 100MB)</p>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="p-8">
                <form id="uploadForm" class="space-y-6">
                    @csrf
                    
                    <!-- Drag & Drop Area -->
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center transition-all duration-300 hover:border-blue-400 hover:bg-blue-50/30"
                         id="dropZone">
                        <i class="fas fa-file-csv text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">Drag & drop your CSV file here</p>
                        <p class="text-gray-400 text-sm mb-4">or</p>
                        
                        <label for="csv_file" class="cursor-pointer">
                            <span class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl inline-flex items-center space-x-2">
                                <i class="fas fa-folder-open"></i>
                                <span>Browse Files</span>
                            </span>
                            <input type="file" name="csv_file" id="csv_file" 
                                   accept=".csv,.txt" 
                                   class="hidden"
                                   required>
                        </label>
                        
                        <p class="text-gray-400 text-xs mt-4" id="fileInfo">
                            No file selected
                        </p>
                    </div>

                    <!-- Upload Button -->
                    <button type="submit" 
                            id="uploadButton"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                        <i class="fas fa-upload"></i>
                        <span>Upload & Process File</span>
                    </button>
                </form>

                <!-- Upload Message -->
                <div id="uploadMessage" class="mt-6"></div>
            </div>
        </div>
    </div>

    <!-- Recent Uploads -->
    <div class="mx-auto mt-16">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100/60 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center space-x-3">
                    <i class="fas fa-history text-blue-500"></i>
                    <span>Recent Uploads</span>
                </h2>
            </div>
            <div class="p-6">
                <div id="uploadsList" class="space-y-4">
                    <!-- Uploads will be loaded here -->
                </div>
                
                <!-- Loading State -->
                <div id="uploadsLoading" class="text-center py-8">
                    <div class="loading-bar h-2 rounded-full max-w-md mx-auto mb-4"></div>
                    <p class="text-gray-500">Loading upload history...</p>
                </div>
                
                <!-- Empty State -->
                <div id="uploadsEmpty" class="hidden text-center py-12">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No uploads yet</p>
                    <p class="text-gray-400">Your upload history will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentFile = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadUploads();
        setupDragAndDrop();
        setupFileInput();
    });

    // Drag and drop functionality
    function setupDragAndDrop() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('csv_file');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        }

        function unhighlight() {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updateFileInfo(files[0]);
        }
    }

    // File input change handler
    function setupFileInput() {
        const fileInput = document.getElementById('csv_file');
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                updateFileInfo(this.files[0]);
            }
        });
    }

    // Update file info display
    function updateFileInfo(file) {
        const fileInfo = document.getElementById('fileInfo');
        if (file) {
            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.innerHTML = `
                <div class="flex items-center justify-center space-x-2 text-green-600">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">${file.name}</span>
                    <span class="text-gray-500">(${fileSize} MB)</span>
                </div>
            `;
            currentFile = file;
        } else {
            fileInfo.textContent = 'No file selected';
            currentFile = null;
        }
    }

    // Handle form submission
    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!currentFile) {
            showMessage('Please select a file first.', 'error');
            return;
        }

        const formData = new FormData(this);
        const uploadButton = document.getElementById('uploadButton');
        const messageDiv = document.getElementById('uploadMessage');
        
        // Client-side file size validation
        if (currentFile.size > 100 * 1024 * 1024) {
            showMessage('File size must not exceed 100MB.', 'error');
            return;
        }
        
        uploadButton.disabled = true;
        uploadButton.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>Uploading...</span>
            </div>
        `;
        
        try {
            const response = await fetch('/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, 'success');
                this.reset();
                document.getElementById('fileInfo').textContent = 'No file selected';
                currentFile = null;
                loadUploads();
                loadGlobalStats(); // Refresh global count
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('Error uploading file: ' + error.message, 'error');
        } finally {
            uploadButton.disabled = false;
            uploadButton.innerHTML = `
                <i class="fas fa-upload"></i>
                <span>Upload & Process File</span>
            `;
        }
    });

    // Load and refresh uploads list
    async function loadUploads() {
        // showUploadsLoading();
        
        try {
            const response = await fetch('/uploads');
            const result = await response.json();
            
            const uploadsList = document.getElementById('uploadsList');
            const emptyState = document.getElementById('uploadsEmpty');
            
            hideUploadsLoading();
            
            if (result.uploads.length === 0) {
                emptyState.classList.remove('hidden');
                uploadsList.innerHTML = '';
                return;
            }
            
            emptyState.classList.add('hidden');
            uploadsList.innerHTML = result.uploads.map(upload => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200/60 hover:border-gray-300 transition-all duration-300">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                            <i class="fas fa-file-csv text-blue-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 truncate">${upload.original_name}</h3>
                            <p class="text-sm text-gray-500">${upload.created_at}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="font-medium text-gray-800">
                                ${upload.processed_rows}/${upload.total_rows} rows
                            </div>
                            <div class="text-sm text-gray-500">
                                ${upload.status === 'processing' ? 
                                    `${Math.round((upload.processed_rows / upload.total_rows) * 100)}% complete` : 
                                    upload.status
                                }
                            </div>
                        </div>
                        
                        <div class="w-16">
                            ${getStatusBadge(upload.status)}
                        </div>
                    </div>
                </div>
            `).join('');
            
        } catch (error) {
            console.error('Error loading uploads:', error);
            hideUploadsLoading();
        }
    }

    // Get status badge
    function getStatusBadge(status) {
        const statusConfig = {
            'pending': { color: 'bg-yellow-100 text-yellow-800', icon: 'clock' },
            'processing': { color: 'bg-blue-100 text-blue-800', icon: 'sync-alt' },
            'completed': { color: 'bg-green-100 text-green-800', icon: 'check-circle' },
            'failed': { color: 'bg-red-100 text-red-800', icon: 'exclamation-circle' }
        };
        
        const config = statusConfig[status] || statusConfig.pending;
        
        return `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${config.color}">
                <i class="fas fa-${config.icon} mr-1"></i>
                ${status.charAt(0).toUpperCase() + status.slice(1)}
            </span>
        `;
    }

    // Show message
    function showMessage(message, type) {
        const messageDiv = document.getElementById('uploadMessage');
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
                       type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 
                       'bg-blue-50 border-blue-200 text-blue-800';
        
        messageDiv.innerHTML = `
            <div class="p-4 rounded-xl border ${bgColor} flex items-center space-x-3 fade-in">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            messageDiv.innerHTML = '';
        }, 5000);
    }

    // Uploads loading state
    function showUploadsLoading() {
        document.getElementById('uploadsLoading').classList.remove('hidden');
        document.getElementById('uploadsEmpty').classList.add('hidden');
    }

    function hideUploadsLoading() {
        document.getElementById('uploadsLoading').classList.add('hidden');
    }

    // Auto-refresh every 3 seconds
    setInterval(loadUploads, 3000);
</script>
@endsection