CSV Processor - Laravel Data Management System
<p align="center"> <a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a> </p><p align="center"> <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a> <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a> <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a> <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP Version"> <img src="https://img.shields.io/badge/SQLite-3.x-003B57?style=flat&logo=sqlite&logoColor=white" alt="SQLite"> </p>
About CSV Processor
CSV Processor is a professional data management system built with Laravel that provides efficient CSV file processing, real-time upload tracking, and comprehensive product catalog management. This system is designed to handle large datasets with background processing and offers an elegant, responsive admin interface.

🚀 Key Features
📤 Smart CSV Upload - Drag & drop interface with real-time progress tracking

🔄 Background Processing - Queue-based file processing using Laravel Jobs

📊 Product Management - Advanced catalog with search, sort, and pagination

🎨 Modern UI - Elegant sidebar navigation with Tailwind CSS

🔄 UPSERT Operations - Intelligent data updates based on UNIQUE_KEY

📱 Responsive Design - Works seamlessly on all devices

⚡ Real-time Updates - Live status updates without page refresh

Quick Start
Prerequisites
PHP 8.1 or higher

Composer

SQLite (recommended) or MySQL

Installation
Clone the repository

bash
git clone https://github.com/yourusername/csv-processor.git
cd csv-processor
Install dependencies

bash
composer install
Setup environment

bash
cp .env.example .env
php artisan key:generate
Configure database

bash
# For SQLite (recommended)
touch database/database.sqlite

# Or update .env for MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=csv_processor
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
Run migrations

bash
php artisan migrate
php artisan queue:table
php artisan migrate
Running the Application
Start the development server

bash
php artisan serve
Start queue worker (in a new terminal)

bash
php artisan queue:work
Access the application

text
http://localhost:8000
Using with Laragon (Windows)
Place project in C:/laragon/www/csv-processor/

Start Laragon services

Run the commands above in Laragon terminal

CSV File Format
The system expects CSV files with the following structure:

Column	Description	Required
UNIQUE_KEY	Unique identifier for each product	✅
PRODUCT_TITLE	Product name	✅
PRODUCT_DESCRIPTION	Product description	✅
STYLE#	Style number	✅
SANMAR_MAINFRAME_COLOR	Main color code	✅
SIZE	Product size	✅
COLOR_NAME	Color description	✅
PIECE_PRICE	Product price	✅
Example CSV:

csv
UNIQUE_KEY,PRODUCT_TITLE,PRODUCT_DESCRIPTION,STYLE#,SANMAR_MAINFRAME_COLOR,SIZE,COLOR_NAME,PIECE_PRICE
KEY001,Classic T-Shirt,Comfortable cotton t-shirt,STYLE123,Black,L,Black,19.99
KEY002,Premium Polo,High-quality polo shirt,STYLE456,Navy,M,Navy,29.99
Project Structure
text
csv-processor/
├── app/
│   ├── Models/
│   │   ├── CsvUpload.php      # File upload tracking
│   │   └── Product.php        # Product data model
│   ├── Jobs/
│   │   └── ProcessCsvUpload.php  # Background CSV processing
│   └── Http/Controllers/
│       ├── CsvUploadController.php  # File upload handling
│       └── ProductController.php    # Product management
├── database/
│   ├── migrations/            # Database schema
│   └── database.sqlite       # SQLite database
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php     # Main layout with sidebar
│   ├── csv-upload.blade.php  # File upload interface
│   └── products/
│       └── index.blade.php   # Product catalog
└── routes/
    └── web.php               # Application routes
API Endpoints
Method	Endpoint	Description
GET	/	CSV upload interface
POST	/upload	Process CSV file upload
GET	/uploads	Get upload history (JSON)
GET	/products	Product catalog view
GET	/products/data	Product data API (JSON)
Features in Detail
🎯 Intelligent CSV Processing
Background Jobs: Files processed asynchronously using Laravel Queue

UPSERT Logic: Updates existing records or creates new ones based on UNIQUE_KEY

UTF-8 Cleaning: Automatic character encoding validation

Duplicate Prevention: Idempotent uploads prevent data duplication

Progress Tracking: Real-time upload status and row processing updates

🎨 User Experience
Drag & Drop: Intuitive file upload interface

Real-time Updates: Live progress without page refresh

Advanced Search: Filter products by multiple criteria

Column Sorting: Click any column to sort results

Responsive Design: Optimized for desktop, tablet, and mobile

⚙️ Technical Excellence
Queue System: Handles large files without timeout issues

SQLite Support: Easy setup with file-based database

RESTful API: Clean JSON endpoints for data access

Error Handling: Comprehensive validation and error messages

Security: Laravel's built-in security features

Troubleshooting
Common Issues
Queue worker not processing jobs:

bash
# Ensure queue worker is running
php artisan queue:work
File upload fails:

Check file size (max 100MB)

Verify CSV format and headers

Ensure storage directory is writable

Database issues:

bash
# Reset database
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate
Cache problems:

bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
Contributing
We welcome contributions! Please feel free to submit pull requests, report bugs, or suggest new features.

Fork the project

Create your feature branch (git checkout -b feature/AmazingFeature)

Commit your changes (git commit -m 'Add some AmazingFeature')

Push to the branch (git push origin feature/AmazingFeature)

Open a Pull Request

Security
If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker.

License
This project is open-sourced software licensed under the MIT license.

Acknowledgments
Laravel Framework

Tailwind CSS

Font Awesome

League CSV

Built with ❤️ using Laravel
