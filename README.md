===========================================
  WMS PRO - Warehouse Management System
===========================================

Thank you for purchasing WMS Pro!

VERSION: 1.0.0
RELEASE DATE: November 2025
AUTHOR: Bintang Wijaya
EMAIL: bintangwijaya18@gmail.com
GITHUB: https://github.com/rhecustein
LICENSE: Envato Regular/Extended License

===========================================
  QUICK START GUIDE
===========================================

🚀 INSTALLATION IN 5 MINUTES:

1. REQUIREMENTS
   - PHP 8.2 or higher
   - MySQL 8.0+ or PostgreSQL 13+
   - Composer 2.5+
   - Node.js 18+ & NPM
   - Apache/Nginx web server

2. EXTRACT FILES
   Extract the zip file to your web server directory
   (e.g., /var/www/html/wms-pro or htdocs/wms-pro)

3. INSTALL DEPENDENCIES
   Open terminal/command prompt:
   
   cd /path/to/wms-pro
   composer install
   npm install

4. CONFIGURE DATABASE
   
   a) Create database:
      mysql -u root -p
      CREATE DATABASE wms_pro_db;
      exit;
   
   b) Import SQL file:
      mysql -u root -p wms_pro_db < database/wms_database.sql
   
   c) Configure .env file:
      cp .env.example .env
      
      Edit .env and update:
      DB_DATABASE=wms_pro_db
      DB_USERNAME=your_username
      DB_PASSWORD=your_password

5. GENERATE KEY & BUILD ASSETS
   
   php artisan key:generate
   php artisan storage:link
   npm run build

6. SET PERMISSIONS (Linux/Mac)
   
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache

7. ACCESS YOUR APPLICATION
   
   http://yourdomain.com
   
   or for development:
   php artisan serve
   http://localhost:8000

===========================================
  DEFAULT LOGIN CREDENTIALS
===========================================

⚠️  IMPORTANT: Change these passwords immediately after first login!

SUPER ADMIN:
Email: superadmin@wms.com
Password: password

WAREHOUSE MANAGER:
Email: manager@wms.com
Password: password

WAREHOUSE OPERATOR:
Email: operator@wms.com
Password: password

===========================================
  FOLDER STRUCTURE
===========================================

wms-pro/
├── app/
│   ├── Http/Controllers/      → All controllers
│   ├── Models/                → Eloquent models
│   └── Services/              → Business logic
├── database/
│   ├── migrations/            → Database migrations
│   ├── seeders/               → Database seeders
│   └── wms_database.sql       → SQL dump file
├── resources/
│   ├── views/                 → Blade templates
│   └── js/                    → JavaScript files
├── public/
│   ├── uploads/               → User uploads
│   └── build/                 → Compiled assets
├── routes/
│   ├── web.php                → Web routes
│   └── api.php                → API routes
├── storage/                   → Storage files
├── documentation/
│   └── index.html             → Full documentation
├── .env.example               → Environment template
├── composer.json              → PHP dependencies
└── package.json               → Node dependencies

===========================================
  DOCUMENTATION
===========================================

📖 Complete documentation available in:
   documentation/index.html

Topics covered:
- Detailed installation guide
- Configuration options
- User roles & permissions
- Feature usage guides
- API documentation
- Troubleshooting
- FAQ

===========================================
  KEY FEATURES
===========================================

✅ Warehouse Management
   - Multiple warehouses support
   - Hierarchical location structure
   - Storage area & bin management

✅ Inbound Operations
   - Purchase order management
   - Good receiving
   - Smart putaway system

✅ Outbound Operations
   - Sales order processing
   - Intelligent picking (FEFO/FIFO)
   - Packing & delivery

✅ Inventory Control
   - Real-time stock tracking
   - Batch & serial number tracking
   - Expiry date management
   - Cycle counting

✅ Auto Replenishment
   - Automatic trigger logic
   - Pick face optimization
   - FEFO/FIFO prioritization

✅ User Management
   - 6 distinct user roles
   - Role-based access control
   - Activity logging

✅ Reports & Analytics
   - Order fulfillment rate
   - Picking accuracy
   - Inventory accuracy
   - Space utilization

✅ Modern Technology
   - Laravel 12 (PHP 8.2+)
   - Tailwind CSS v4
   - Responsive design
   - Dark mode support
   - RESTful API ready

===========================================
  SUPPORT
===========================================

📧 Email: bintangwijaya18@gmail.com
🐙 GitHub: https://github.com/rhecustein

SUPPORT INCLUDES:
✓ Bug fixes
✓ Installation assistance
✓ Minor updates
✓ Technical questions

SUPPORT DOES NOT INCLUDE:
✗ Customization
✗ Server configuration
✗ Feature development
✗ Third-party integrations

RESPONSE TIME:
- Critical issues: 24-48 hours
- General questions: 48-72 hours

===========================================
  TROUBLESHOOTING
===========================================

COMMON ISSUES:

1. "500 Internal Server Error"
   → Check file permissions
   → Clear cache: php artisan optimize:clear
   → Check .env configuration

2. "Database connection error"
   → Verify database credentials in .env
   → Ensure database server is running
   → Check if database exists

3. "CSS/JS not loading"
   → Run: npm run build
   → Clear browser cache
   → Check public/build folder exists

4. "Permission denied"
   → Run: chmod -R 755 storage bootstrap/cache
   → Ensure web server has write permissions

For more troubleshooting tips, see documentation/index.html

===========================================
  UPDATES
===========================================

Stay updated with the latest version:
1. Check CodeCanyon for updates
2. Backup your database and files
3. Replace files (keep your .env)
4. Run: php artisan migrate
5. Clear cache: php artisan optimize:clear

===========================================
  SECURITY BEST PRACTICES
===========================================

⚠️  IMPORTANT SECURITY STEPS:

1. Change all default passwords immediately
2. Update APP_KEY in .env (php artisan key:generate)
3. Set APP_DEBUG=false in production
4. Use strong database passwords
5. Keep Laravel and dependencies updated
6. Enable HTTPS/SSL
7. Regular database backups

===========================================
  CUSTOMIZATION
===========================================

This is a fully customizable Laravel application.

To customize:
- Views: resources/views/
- Styles: resources/css/
- Routes: routes/web.php
- Controllers: app/Http/Controllers/
- Models: app/Models/

Refer to Laravel documentation: https://laravel.com/docs

===========================================
  TECHNOLOGY STACK
===========================================

Backend:
- Laravel 12 (PHP 8.2+)
- MySQL 5.7+ / PostgreSQL 10+
- Laravel Sanctum (API Authentication)
- Spatie Laravel Permission (RBAC)

Frontend:
- Tailwind CSS v4
- Alpine.js
- Heroicons
- Chart.js for analytics

Tools:
- Composer for PHP dependencies
- NPM for JavaScript dependencies
- Vite for asset bundling

===========================================
  RATE & REVIEW
===========================================

If you're happy with WMS Pro, please consider:

⭐ Leaving a 5-star review on CodeCanyon
💬 Sharing your experience
🎯 Recommending to colleagues

Your feedback helps us improve!

===========================================
  CHANGELOG
===========================================

Version 1.0.0 (November 2025)
- Initial release
- Multi-warehouse support
- Real-time inventory tracking
- Batch & serial tracking
- FIFO/FEFO picking
- Auto replenishment
- Advanced reporting
- RESTful API
- Role-based access control
- Responsive design with dark mode

See documentation/index.html for detailed changelog

===========================================
  LICENSE
===========================================

This product is licensed under the Envato Market License.
See LICENSE.txt for complete terms and conditions.

One license = One installation/domain

===========================================
  THANK YOU!
===========================================

Thank you for choosing WMS Pro!

We're committed to providing excellent support and
continuous improvements to help your warehouse operations.

Happy warehouse management! 📦🚀

---

Developed by: Bintang Wijaya
Email: bintangwijaya18@gmail.com
GitHub: https://github.com/rhecustein

===========================================
END OF README
===========================================