# Wood Inventory Management System

## Overview

Wood Inventory Management System is a comprehensive Laravel-based web application designed to manage the complete lifecycle of wood materials and products. It streamlines procurement, inventory tracking, sales operations, and manufacturing workflows for wood-based businesses.

## Project Summary

This system provides an integrated platform for:

- **Inventory Management**: Track wood materials and finished products with real-time inventory movements
- **Procurement**: Manage supplier relationships and purchase orders for raw materials
- **Sales Operations**: Handle customer orders and sales order management
- **Manufacturing**: Track work orders for material processing and product creation
- **Supplier & Customer Management**: Centralized database for business partners
- **Real-time Tracking**: Monitor inventory movements across different stages of operations

## Key Features

### Core Modules
- **Materials Management**: Track raw wood materials from suppliers
- **Products Management**: Manage finished products and their specifications
- **Purchase Orders**: Streamlined procurement workflow with supplier tracking
- **Sales Orders**: Efficient order management with item-level tracking
- **Work Orders**: Production tracking and manufacturing management
- **Inventory Movements**: Comprehensive logging of all inventory transactions
- **Supplier Management**: Maintain supplier information and relationships
- **Customer Management**: Track customer details and interactions
- **User Management**: Role-based access control

### Technology Stack
- **Backend**: Laravel Framework
- **Frontend**: Blade Templates with Tailwind CSS
- **Build Tool**: Vite
- **Database**: PostgreSQL/MySQL with migrations
- **Package Manager**: Composer (PHP), NPM (Node.js)
- **Testing**: PHPUnit

## Project Structure

```
wood-inventory-management/
├── app/
│   ├── Http/
│   │   └── Controllers/          # Application controllers
│   ├── Models/                   # Eloquent models for database entities
│   ├── Providers/                # Service providers
│   └── View/
│       └── Components/           # Reusable Blade components
├── resources/
│   ├── css/                      # Tailwind CSS stylesheets
│   ├── js/                       # JavaScript files
│   └── views/                    # Blade view templates
├── routes/                       # Application routing
├── database/
│   ├── migrations/               # Database schema migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories for testing
├── config/                       # Configuration files
├── tests/                        # Unit and feature tests
└── public/                       # Public assets and entry point
```

## Live Demo
[View Live Demo](https://rmwoodworks-1.onrender.com)

## Screenshots

### Login
![Login](login.png)

### Dashboard
![Dashboard](dashboard.png)

### Accounting
![Accounting](accounting.png)

### Inventory
![Inventory](inventory.png)

### Procurement
![Procurement](procurement.png)

### Production
![Production](production.png)

### Sales
![Sales](sales.png)

## Development Status

**Status**: In Progress

## Getting Started

### Requirements
- PHP 8.0+
- Node.js 16+
- Composer
- npm or yarn
- Database (PostgreSQL/MySQL)

### Installation

1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install Node.js dependencies: `npm install`
4. Copy `.env.example` to `.env`
5. Generate application key: `php artisan key:generate`
6. Configure database connection in `.env`
7. Run migrations: `php artisan migrate`
8. Seed database (optional): `php artisan db:seed`
9. Build frontend assets: `npm run build`
10. Start development server: `php artisan serve`

> ⚠️ **Important: `localhost` will NOT work!**
>
> This application uses [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) CAPTCHA on the login page to prevent bot attacks. Cloudflare Turnstile **does not support `localhost` or `127.0.0.1`** as valid hostnames — the CAPTCHA widget will fail to load or verify, making it impossible to log in.
>
> **You must use a `.test` domain** (e.g., `rmwoodworks.test`) via Laragon or a similar local development tool. See the [Running with Laragon](#running-with-laragon-test-domain) section below for setup instructions.

### Default Accounts (Seeder)

Run `php artisan db:seed` to create the following default user accounts:

| Role | Email | Password | Access |
|---|---|---|---|
| **Admin** | `admin@rmwoodworks.com` | `password` | Full access to all modules (Dashboard, Inventory, Procurement, Production, Sales, Accounting, Audit Trail) |
| **Inventory Clerk** | `inventory@rmwoodworks.com` | `password` | Inventory management |
| **Procurement Officer** | `procurement@rmwoodworks.com` | `password` | Procurement & purchase orders |
| **Workshop Staff** | `workshop@rmwoodworks.com` | `password` | Production & work orders |
| **Sales Clerk** | `sales@rmwoodworks.com` | `password` | Sales & customer orders |
| **Accounting Staff** | `accounting@rmwoodworks.com` | `password` | Accounting & financial records |

> 🔒 **Note:** Change default passwords immediately in a production environment.

### Running with Laragon (`.test` Domain)

If you're using [Laragon](https://laragon.org/) as your local development environment, you can access the project via a pretty URL like `rmwoodworks.test` instead of using `php artisan serve`.

#### Steps

1. **Place the project in Laragon's `www` directory**
   Move or clone the project folder into Laragon's web root:
   ```
   C:\laragon\www\rmwoodworks\
   ```
   > **Note:** The folder name determines the `.test` domain. A folder named `rmwoodworks` will be accessible at `rmwoodworks.test`.

2. **Start Laragon services**
   Open Laragon and click **"Start All"** to start Apache/Nginx and MySQL/PostgreSQL.

3. **Enable Auto Virtual Hosts (if not already enabled)**
   - In Laragon, go to **Menu → Preferences → General**
   - Ensure **"Auto create virtual hosts"** is checked
   - The default pattern is `{name}.test`, which maps each folder in `www/` to a `.test` domain

4. **Reload Apache & DNS**
   After placing the project folder, right-click the Laragon tray icon and select:
   - **Apache → Reload**
   - **Menu → Tools → Reload hosts file** (or restart Laragon)

5. **Update your `.env` file**
   Set the `APP_URL` to match your `.test` domain:
   ```env
   APP_URL=http://rmwoodworks.test
   ```

6. **Run migrations and build assets**
   Open Laragon's terminal (or any terminal in the project directory) and run:
   ```bash
   php artisan migrate
   php artisan db:seed       # optional
   npm install
   npm run build             # or `npm run dev` for development with hot reload
   ```

7. **Access the application**
   Open your browser and navigate to:
   ```
   http://rmwoodworks.test
   ```

#### Troubleshooting

| Issue | Solution |
|---|---|
| **Site not loading / DNS error** | Ensure Laragon is running and Apache has been reloaded. Check that the hosts file (`C:\Windows\System32\drivers\etc\hosts`) contains an entry like `127.0.0.1 rmwoodworks.test`. |
| **404 or blank page** | Make sure the project's `public/` folder is the document root. Laragon handles this automatically for Laravel projects. |
| **Assets not loading (CSS/JS)** | Run `npm run build` or `npm run dev` to compile frontend assets. |
| **Database connection error** | Verify your `.env` database credentials match Laragon's database settings (default user is usually `root` with no password). |
| **`.test` domain not resolving** | Restart Laragon completely, or manually add `127.0.0.1 rmwoodworks.test` to your hosts file. |

### Development

- **Frontend development**: `npm run dev`
- **Run tests**: `php artisan test` or `./vendor/bin/phpunit`
- **Database reset**: `php artisan migrate:refresh`

## Models & Relationships

The system includes the following main entities:
- **User**: System users with authentication
- **Customer**: Customer information and details
- **Supplier**: Supplier information and relationships
- **Product**: Finished product catalog
- **Material**: Raw material inventory
- **PurchaseOrder**: Purchase order management
- **PurchaseOrderItem**: Line items in purchase orders
- **SalesOrder**: Sales order management
- **SalesOrderItem**: Line items in sales orders
- **WorkOrder**: Production/manufacturing orders
- **InventoryMovement**: Audit trail of inventory transactions

## Contributing

Guidelines for contributing to this project should be added here.

## License

This project is proprietary software.

