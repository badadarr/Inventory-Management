# ✨ Inventory and Sales Management System (SPA)

A robust Inventory Management System built with **Laravel 10**, **MySQL**, **Inertia.js** and **Vue.js**, designed to streamline your inventory, sales, and purchasing processes. It's a Single Page Application (SPA)

> **Note:** This project is cloned and extended from [Inventory-Management-System-Laravel-SPA](https://github.com/mamun724682/Inventory-Management-System-Laravel-SPA)

## 🌟 Key Features

### 📊 Core Modules
- **Dashboard Insight** - Real-time business metrics and analytics
- **POS (Point of Sale)** - Fast and intuitive sales interface
- **Orders Management**
    - Create and manage orders
    - Due payments tracking
    - Settle outstanding amounts
    - Order details (PO date, delivery date, material specs)
- **Transactions** - Complete transaction history

### 📦 Master Data
- **Categories** - Product categorization
- **Unit Types** - Measurement units management
- **Products** - Comprehensive product information
    - Material specifications (bahan, gramatur, ukuran)
    - Cutting and plano sizes
    - Delivery addresses
    - Additional notes
- **Customers** - Customer relationship management
    - Customer profiles with sales assignment
    - New/Repeat customer status (auto-update)
    - Commission tracking
    - Join date tracking
- **Suppliers** - Supplier information management
- **Sales** - Sales team management
    - Sales profiles with photo
    - Contact information
    - Active/Inactive status

### 👥 Human Resources
- **Employee** - Employee data management
- **Salary** - Payroll and salary processing
- **Expenses** - Business expense tracking

### 📈 Reports & Analytics
- **Sales Points** - Sales performance tracking
    - Points calculation per product type
    - Box and Kertas Nasi Padang tracking
    - Sales recap and leaderboard
- **Outstanding Report** - Accounts receivable monitoring
    - Filter by date range
    - Detailed order information
    - Payment status tracking
- **Top Customers** - Customer ranking by sales volume
    - Configurable top N customers (5/10/20/50)
    - Total sales and quantity metrics
    - Medal badges for top performers

### ⚙️ System
- **Settings** - Application configuration
- **User Management** - User accounts and permissions with multi-tenancy
    - Company-based user isolation
    - Role-based access restrictions
    - Automatic company assignment for Admin users
- **RBAC (Role-Based Access Control)** - Advanced role management with 5 roles:
    - **Super Admin** - Full system access across all companies
    - **Admin** - Full access within their company only
        - Can manage users (Admin, Sales, Warehouse, Finance) in their company
        - Cannot edit/delete Super Admin or users from other companies
        - Company name auto-filled and read-only
    - **Sales** - Customers, POS, orders, sales points, top customers reports
    - **Warehouse** - Products, orders management
    - **Finance** - Transactions, salaries, expenses, outstanding reports
        - Can access employee data for salary payments

## 🚀 Quick Start

Follow these steps to set up the project locally:

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL >= 5.7

### Installation Steps

1. **Clone the repository:**
    ```bash
    git clone https://github.com/mamun724682/Inventory-Management-System-Laravel-SPA
    ```

2. **Navigate to the project folder:**
    ```bash
    cd Inventory-Management-System-Laravel-SPA-2.x
    ```

3. **Install PHP dependencies:**
    ```bash
    composer install
    ```

4. **Copy `.env` configuration:**
    ```bash
    cp .env.example .env
    ```

5. **Generate application key:**
    ```bash
    php artisan key:generate
    ```

6. **Configure the database in the `.env` file** with your local credentials:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

7. **Run database migrations and seed sample data:**
    ```bash
    php artisan migrate:fresh --seed
    ```

8. **Link storage for media files:**
    ```bash
    php artisan storage:link
    ```

9. **Install JavaScript and CSS dependencies:**
    ```bash
    npm install
    ```

10. **Build frontend assets:**
    ```bash
    npm run dev
    ```
    For production:
    ```bash
    npm run build
    ```

11. **Start the Laravel development server:**
    ```bash
    php artisan serve
    ```

12. **Access the application:**
    Open your browser and visit: `http://localhost:8000`

## 📚 Documentation

Detailed documentation is available in the `app/Documentation/` folder:
- `COMPLETE_FEATURES_LIST.md` - Complete list of all features
- `QUICK_START_GUIDE.md` - Step-by-step usage guide
- `IMPLEMENTATION_SUMMARY.md` - Backend implementation details
- `FRONTEND_IMPLEMENTATION.md` - Frontend implementation details
- `RBAC_IMPLEMENTATION.md` - Role-Based Access Control documentation

Quick guides:
- `RBAC_QUICK_GUIDE.md` - Quick start guide for RBAC

## 🛠️ Tech Stack

**Backend:**
- Laravel 10
- MySQL
- Repository Pattern
- Service Layer Architecture

**Frontend:**
- Vue.js 3
- Inertia.js
- Tailwind CSS
- Vite

**Additional Tools:**
- Laravel Breeze (Authentication)
- Laravel Sanctum (API Authentication)
- Ziggy (Route Helper)

## 🎯 Key Highlights

- ✅ **Single Page Application (SPA)** - Seamless user experience
- ✅ **Responsive Design** - Works on desktop, tablet, and mobile
- ✅ **Real-time Updates** - Instant data synchronization
- ✅ **Advanced RBAC** - 5 roles with granular permissions and multi-tenancy
- ✅ **Multi-Company Support** - Complete company isolation with access control
    - Admin can only manage users within their company
    - Automatic company_id assignment for Admin-created users
    - Cross-company data visibility with restricted edit/delete access
- ✅ **Secure User Management**
    - Frontend and backend validation layers
    - Role-based UI rendering (hide/show fields based on role)
    - Company-based access restrictions
- ✅ **Auto Customer Status** - Automatically tracks new/repeat customers
- ✅ **Comprehensive Reports** - Outstanding, Top Customers, Sales Points
- ✅ **Photo Upload** - Support for product and sales images
- ✅ **Currency Formatting** - Indonesian Rupiah (IDR) format
- ✅ **Date Localization** - Indonesian date format

## 🔐 Default Login Credentials

After running seeders, you can login with:

| Role | Email | Password | Company | Access Level |
|------|-------|----------|---------|-------------|
| Super Admin | superadmin@example.com | password | Head Office | Full access to all companies |
| Admin PT A | admin.pta@example.com | password | PT. Company A | Full access to PT A users only |
| Admin PT B | admin.ptb@example.com | password | PT. Company B | Full access to PT B users only |
| Sales | sales@example.com | password | PT. Company A | Limited to sales functions |
| Warehouse | warehouse@example.com | password | PT. Company A | Limited to warehouse functions |
| Finance | finance@example.com | password | PT. Company A | Limited to finance functions |

### RBAC & Multi-Tenancy Features:

**Super Admin:**
- View, create, edit, delete all users across all companies
- Can set any role including Super Admin
- Can freely assign company_id and company_name

**Admin (Per Company):**
- View all users (including other companies and Super Admin) - read-only
- Create, edit, delete users only within their own company
- Cannot select Super Admin role (hidden from dropdown)
- Cannot edit/delete Super Admin users ("No Access" badge shown)
- Cannot edit/delete users from other companies ("No Access" badge shown)
- Company name auto-filled and read-only when creating users
- Company_id automatically set to their own company

**Sales, Warehouse, Finance:**
- Access limited to their specific functional areas
- Finance role has access to employee data for salary payments

See `RBAC_QUICK_GUIDE.md` for complete role permissions and detailed examples.
