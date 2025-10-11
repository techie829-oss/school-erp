# School ERP Project Map

Complete documentation of all files and their functionality in the School ERP system.

---

## 📁 Project Structure Overview

```
school-erp/
├── src/                    # Main Laravel application
├── docs/                   # Development documentation
├── v1/                     # Previous version (reference)
└── [config files]          # Project configuration
```

---

## 🎯 Core Application Files (`src/`)

### 📝 Application Entry Points

**`public/index.php`**

- Application entry point
- Handles all HTTP requests
- Loads composer autoloader
- Bootstraps Laravel application

**`artisan`**

- CLI entry point
- Executes artisan commands
- Used for migrations, tinker, custom commands

**`bootstrap/app.php`**

- Creates Laravel application instance
- Binds kernel interfaces
- Configures middleware aliases
- Returns application instance

---

## 🔐 Authentication & Authorization

### Controllers

**`app/Http/Controllers/Auth/LoginController.php`**

- **Purpose:** Handle user authentication for both admin and tenant domains
- **Key Methods:**
  - `showLoginForm()` - Display login page (tenant or admin)
  - `login()` - Process login request
  - `logout()` - Handle logout
  - `isTenantDomain()` - Detect if request is from tenant domain
  - `resolveTenantFromSubdomain()` - Find tenant by subdomain
  - `getRedirectRoute()` - Determine post-login redirect
- **Functionality:**
  - Supports admin domain (`app.myschool.test`)
  - Supports tenant domains (`abc.myschool.test`)
  - Uses `TenantAuthenticationService` for tenant login
  - Rate limiting protection
  - Session regeneration for security

**`app/Http/Controllers/Auth/TenantCheckController.php`**

- **Purpose:** Check tenant existence and status
- **Functionality:**
  - Verify tenant exists before showing login
  - Check tenant is active
  - Display appropriate error messages

### Services

**`app/Services/TenantAuthenticationService.php`**

- **Purpose:** Unified authentication for all tenant types
- **Key Methods:**
  - `authenticateForTenant()` - Main authentication method
  - `authenticateUser()` - Unified user lookup and validation
  - `validateDomainAccess()` - Check if user belongs to tenant
  - `getUserForTenant()` - Retrieve user from correct database
- **Functionality:**
  - Works for shared AND separate databases
  - Single code path for both strategies
  - Loads env file automatically via TenantContextService
  - Creates authenticated user object for separate databases
  - Returns AdminUser model for shared databases

**`app/Services/TenantUserValidationService.php`**

- **Purpose:** Validate users for tenant access
- **Key Methods:**
  - `validateUserForTenant()` - Complete user validation
  - `findUserInTenant()` - Find user in correct database
  - `findUserInSeparateDatabase()` - Query separate tenant database
  - `findUserInSharedDatabase()` - Query shared database with tenant_id
  - `getAllowedDomainsForUser()` - Get all domains user can access
- **Functionality:**
  - Checks user exists in tenant
  - Verifies password
  - Checks user is active
  - Provides allowed domains for error messages

### Middleware

**`app/Http/Middleware/ValidateTenantDomain.php`**

- **Purpose:** Validate tenant domain access for authenticated users
- **Functionality:**
  - Only applies to tenant domains (not admin)
  - Checks tenant exists
  - User access already validated during authentication
  - Lightweight guard for invalid domains

**`app/Http/Middleware/TenantAuth.php`**

- **Purpose:** Ensure user is authenticated on tenant domain
- **Functionality:**
  - Redirects to tenant login if not authenticated
  - Uses admin guard for tenant users

**`app/Http/Middleware/InitializeTenantContext.php`**

- **Purpose:** Initialize tenant context for each request
- **Functionality:**
  - Detects tenant from subdomain
  - Calls TenantContextService to set up environment
  - Ensures correct database connection

### Policies

**`app/Policies/AdminAccessPolicy.php`**

- **Purpose:** Define access rules for admin users
- **Functionality:**
  - Determines which admin dashboard user can access
  - Super admin → Main admin panel
  - School admin → Specific tenant panel
  - Provides redirect URLs based on user type

---

## 🏢 Tenant Management

### Controllers

**`app/Http/Controllers/Admin/TenantController.php`** (1,729 lines)

- **Purpose:** Complete tenant CRUD and management
- **Key Methods:**
  - `index()` - List all tenants
  - `create()` - Show create form
  - `store()` - Create new tenant + auto-create env file
  - `show()` - View tenant details
  - `edit()` - Show edit form
  - `update()` - Update tenant + auto-update env file
  - `destroy()` - Delete tenant + auto-delete env file
  - `toggleStatus()` - Activate/deactivate tenant
  
  **Database Operations:**
  - `testDatabaseConnection()` - Test separate database connection
  - `createDatabase()` - Create tenant database
  - `runMigrations()` - Run migrations on tenant database
  - `getDatabaseTables()` - List tables in tenant database
  - `getDatabaseInfo()` - Get database statistics
  
  **User Management:**
  - `usersIndex()` - List tenant users
  - `usersCount()` - Get user count
  - `usersCreate()` - Show create user form
  - `usersStore()` - Create tenant user
  - `usersShow()` - View user details
  - `usersEdit()` - Show edit user form
  - `usersUpdate()` - Update tenant user
  - `usersDelete()` - Show delete confirmation
  - `usersDestroy()` - Delete tenant user
  - `usersChangePassword()` - Show change password form
  - `usersUpdatePassword()` - Update user password
  
  **Environment File Management:**
  - `getEnvFileStatus()` - Check if `.env.{domain}` exists
  - `viewEnvFile()` - Display env file contents
  - `downloadEnvFile()` - Download env file
  - `regenerateEnvFile()` - Recreate env file from main .env
  - `createTenantEnvironmentFile()` - Auto-create env file
  - `deleteTenantEnvironmentFile()` - Auto-delete env file
  
  **Herd Management:**
  - `cleanupHerdYml()` - Format Herd YAML file
  - `syncHerdYmlWithDatabase()` - Sync subdomains with database
  - `updateHerdConfiguration()` - Update Herd on subdomain change

**`app/Http/Controllers/Tenant/Admin/DashboardController.php`**

- **Purpose:** Tenant admin dashboard
- **Functionality:**
  - Display tenant statistics
  - Show student/teacher/class counts
  - Recent activities
  - Upcoming events
  - Works with separate and shared databases
  - Graceful fallback if tables don't exist

### Services

**`app/Services/TenantEnvironmentService.php`**

- **Purpose:** Manage domain-specific environment files
- **Key Methods:**
  - `loadTenantEnvironment()` - Load `.env.{domain}` if exists
  - `parseEnvFile()` - Parse environment file into array
  - `buildDatabaseConfig()` - Build database config from env vars
  - `createTenantEnvironmentFile()` - Create full copy of .env with DB changes
  - `deleteTenantEnvironmentFile()` - Delete domain env file
  - `hasTenantEnvironmentFile()` - Check if file exists
  - `getDefaultTenantConfig()` - Get config from tenant model
- **Functionality:**
  - Creates `.env.abc.myschool.test` files
  - Full copy of main .env
  - Only changes DB_* variables
  - Auto-converts localhost → 127.0.0.1
  - Forces empty unix_socket for TCP/IP

**`app/Services/TenantContextService.php`**

- **Purpose:** Manage tenant context and environment
- **Key Methods:**
  - `initializeContext()` - Set up tenant environment
  - `configureDatabase()` - Configure database connection
  - `configureCache()` - Set up tenant-specific cache
  - `configureSession()` - Configure session for tenant
  - `resetContext()` - Restore original configuration
  - `getDatabaseConnection()` - Get tenant database connection
  - `getCacheKey()` - Generate tenant-specific cache keys
- **Functionality:**
  - Loads env file on tenant domain (not admin)
  - Switches database connection
  - Maintains separate cache per tenant
  - Sessions stored in main database
  - Auto-detects admin vs tenant context

**`app/Services/TenantDatabaseService.php`**

- **Purpose:** Manage separate tenant databases
- **Key Methods:**
  - `switchToTenantDatabase()` - Switch to tenant DB connection
  - `getTenantConnection()` - Get tenant database connection
  - `testTenantConnection()` - Test database connectivity
  - `createTenantDatabase()` - Create tenant database
  - `runTenantMigrations()` - Run migrations on tenant DB
  - `getTenantTables()` - List tables in tenant database
  - `createPrimaryAdminUser()` - Create school_admin user
- **Functionality:**
  - Forces TCP/IP connection (127.0.0.1)
  - Empty unix_socket to avoid socket errors
  - Creates databases with proper charset/collation
  - Runs migrations on specific connections
  - Creates school_admin (not super_admin)

**`app/Services/TenantService.php`**

- **Purpose:** General tenant operations
- **Functionality:**
  - Tenant CRUD operations
  - Domain validation
  - Subdomain availability checks

**`app/Services/ColorPaletteService.php`**

- **Purpose:** Manage tenant color schemes
- **Functionality:**
  - Apply custom colors per tenant
  - Theme customization

**`app/Services/VhostService.php`**

- **Purpose:** Manage virtual host configurations
- **Functionality:**
  - Update Herd .herd.yml file
  - Manage subdomain routing
  - Server configuration

---

## 🗄️ Database Layer

### Models

**`app/Models/Tenant.php`**

- **Purpose:** Tenant model and configuration
- **Key Methods:**
  - `usesSeparateDatabase()` - Check database strategy
  - `getDatabaseConfig()` - Get database connection array
  - `getConnectionName()` - Get connection name (tenant_{id})
  - `getConnection()` - Resolve database connection
  - `colorPalettes()` - Relationship to color palettes
- **Attributes:**
  - `id` - UUID tenant identifier
  - `data` - JSON with name, email, subdomain, strategy, etc.
  - `database_name` - Separate database name
  - `database_host` - Database host (127.0.0.1)
  - `database_port` - Database port (3306)
  - `database_username` - Database user
  - `database_password` - Database password
- **Functionality:**
  - Forces TCP/IP with 127.0.0.1 (not localhost)
  - Empty unix_socket
  - Supports shared and separate database strategies

**`app/Models/AdminUser.php`**

- **Purpose:** Admin users in main database
- **Functionality:**
  - Authenticatable model
  - Belongs to tenant (for shared database)
  - Role-based permissions
  - Password hashing

**`app/Models/User.php`**

- **Purpose:** Regular users (students, parents, etc.)
- **Functionality:**
  - Standard Laravel user model
  - Belongs to tenant

**`app/Models/TenantColorPalette.php`**

- **Purpose:** Store custom color schemes per tenant
- **Functionality:**
  - Belongs to tenant
  - Store primary, secondary, accent colors

**`app/Models/SuperAdmin.php`**

- **Purpose:** Super admin users for main admin panel
- **Functionality:**
  - Full system access
  - Manage all tenants

### Migrations

**Tenant Management:**

- `2024_01_01_000000_create_tenants_table.php` - Main tenants table
- `2024_01_01_000001_create_domains_table.php` - Domain management
- `2024_01_01_000002_create_tenant_color_palettes_table.php` - Custom colors
- `2025_09_04_111123_add_database_config_to_tenants_table.php` - Add database fields

**Admin Users:**

- `2024_01_01_000003_create_admin_users_table.php` - Admin users table
- `2024_01_01_000004_create_admin_password_reset_tokens_table.php` - Password resets

**Permissions:**

- `2024_01_01_000005_create_spatie_permission_tables.php` - Role-based permissions

**Core:**

- `0001_01_01_000000_create_users_table.php` - Users table
- `0001_01_01_000001_create_cache_table.php` - Cache table
- `0001_01_01_000002_create_jobs_table.php` - Queue jobs

**Relationships:**

- `2025_09_07_052803_add_tenant_id_to_users_table.php` - Link users to tenants

### Seeders

**`database/seeders/TenantSeeder.php`**

- Seeds sample tenants
- Creates demo schools

**`database/seeders/AdminUserSeeder.php`**

- Seeds admin users
- Creates super admin accounts

**`database/seeders/ColorPaletteSeeder.php`**

- Seeds default color schemes

**`database/seeders/DatabaseSeeder.php`**

- Main seeder
- Calls all other seeders

---

## 🌐 Routing & Middleware

### Route Files

**`routes/web.php`**

- **Purpose:** Main application routes
- **Sections:**
  - Landing page routes
  - Admin panel routes (with auth middleware)
  - Tenant management routes
  - Tenant user management routes
  - Tenant database operations
  - Environment file management
  - Vhost management
  - Tenant domain routes (with tenant middleware)
  - School pages (for each tenant)

**`routes/super-admin.php`**

- **Purpose:** Super admin specific routes
- **Functionality:**
  - Tenant management
  - User management
  - System management

**`routes/tenant.php`**

- **Purpose:** Tenant-specific routes
- **Functionality:**
  - Tenant admin dashboard
  - Student management
  - Teacher management
  - Class management
  - Attendance
  - Grades
  - Reports

**`routes/console.php`**

- **Purpose:** Artisan console routes
- **Functionality:**
  - Custom scheduled commands

### Middleware

**`app/Http/Middleware/Authenticate.php`**

- Standard Laravel authentication
- Redirects to appropriate login page

**`app/Http/Middleware/InitializeTenantContext.php`**

- **Purpose:** Initialize tenant context for each request
- **Functionality:**
  - Detects tenant from subdomain
  - Calls TenantContextService
  - Sets up database connection
  - Loads environment configuration

**`app/Http/Middleware/SwitchTenantDatabase.php`**

- **Purpose:** Switch to tenant database connection
- **Functionality:**
  - Changes default database connection
  - Handles separate database tenants

**`app/Http/Middleware/ValidateTenantDomain.php`**

- **Purpose:** Validate tenant domain and existence
- **Functionality:**
  - Checks tenant exists
  - Validates domain is active
  - Simplified (no redundant user checks)

**`app/Http/Middleware/TenantAuth.php`**

- **Purpose:** Ensure authentication on tenant domains
- **Functionality:**
  - Redirects to tenant login if not authenticated
  - Uses admin guard

**`app/Http/Middleware/EnforceAdminAccessPolicy.php`**

- **Purpose:** Enforce admin access policies
- **Functionality:**
  - Checks user type (super_admin, school_admin, etc.)
  - Redirects based on access level

**`app/Http/Middleware/RedirectSchoolAdminToTenant.php`**

- **Purpose:** Redirect school admins to their tenant
- **Functionality:**
  - Prevents school admins from accessing main admin panel
  - Redirects to their specific tenant domain

---

## 🛠️ Services Layer

### Tenant Services

**`app/Services/TenantService.php`**

- General tenant operations
- Tenant creation/update helpers
- Domain management

**`app/Services/TenantContextService.php`**

- **Core tenant environment management**
- Loads domain-specific .env files
- Configures database connections
- Manages cache and sessions
- Context switching and reset

**`app/Services/TenantAuthenticationService.php`**

- **Unified authentication**
- Single code path for shared/separate
- Automatic env file loading
- User validation

**`app/Services/TenantDatabaseService.php`**

- **Separate database operations**
- Connection management
- Database creation
- Migration execution
- Table management
- Primary user creation (school_admin)

**`app/Services/TenantEnvironmentService.php`**

- **Environment file management**
- Creates `.env.{domain}` files
- Full copy of main .env
- Only changes DB_* variables
- Auto-converts localhost → 127.0.0.1
- Forces TCP/IP connection

**`app/Services/TenantUserValidationService.php`**

- **User validation for tenants**
- Domain access validation
- User lookup in correct database

### Other Services

**`app/Services/ColorPaletteService.php`**

- Manage tenant color schemes
- Apply custom branding

**`app/Services/VhostService.php`**

- Virtual host configuration
- Herd integration
- Subdomain management

---

## 🎨 Views & Frontend

### Layouts

**`resources/views/layouts/admin.blade.php`**

- Admin panel layout
- Navigation, header, footer
- Shared across all admin pages

**`resources/views/layouts/app.blade.php`**

- General application layout

**`resources/views/layouts/guest.blade.php`**

- Guest/public pages layout

### Admin Views

**`resources/views/admin/`**

- `dashboard.blade.php` - Main admin dashboard
- `tenants/` - Tenant management views
  - `index.blade.php` - List tenants
  - `create.blade.php` - Create tenant form
  - `show.blade.php` - View tenant details + env file UI
  - `edit.blade.php` - Edit tenant form
  - `users/` - Tenant user management views
    - `index.blade.php` - List users
    - `create.blade.php` - Create user
    - `show.blade.php` - View user
    - `edit.blade.php` - Edit user
    - `delete.blade.php` - Delete confirmation
    - `change-password.blade.php` - Change password

### Tenant Views

**`resources/views/tenant/`**

- `auth/login.blade.php` - Tenant login page
- `admin/dashboard.blade.php` - Tenant admin dashboard

### School Views

**`resources/views/school/`**

- School public pages
- Student portal
- Parent portal

### Landing Pages

**`resources/views/landing/`**

- Homepage
- Features
- Pricing
- Contact
- About

---

## 🔧 Console Commands

**`app/Console/Commands/TestTenantDatabaseCommand.php`**

- **Command:** `php artisan tenant:test-db {subdomain}`
- **Purpose:** Test tenant database connection
- **Functionality:**
  - Finds tenant by subdomain
  - Checks database strategy
  - Tests connection
  - Lists tables
  - Shows admin users
  - Comprehensive diagnostics

**`app/Console/Commands/SetupTenantDatabaseCommand.php`**

- **Command:** `php artisan tenant:setup-db {subdomain}`
- **Purpose:** Complete tenant database setup wizard
- **Options:**
  - `--create-db` - Create database
  - `--run-migrations` - Run migrations
  - `--host` - Database host
  - `--port` - Database port
  - `--username` - Database username
  - `--password` - Database password
- **Functionality:**
  - Creates database
  - Creates env file
  - Updates tenant model
  - Runs migrations
  - Interactive setup

---

## ⚙️ Configuration Files

**`config/all.php`**

- **Purpose:** Custom School ERP configuration
- **Contains:**
  - Domain configuration (primary, admin, tenant pattern)
  - Company information
  - Color palette defaults
  - Hosting configuration (Herd, Apache, Nginx)
  - SSL settings
  - Feature toggles

**`config/database.php`**

- **Purpose:** Database connections
- **Modifications:**
  - MySQL connection uses empty unix_socket
  - Forces TCP/IP connection
  - Prevents socket file errors

**`config/auth.php`**

- **Purpose:** Authentication configuration
- **Guards:**
  - `web` - Default guard for users
  - `admin` - Admin guard for AdminUser model

**`config/session.php`**

- Session configuration
- Database driver for sessions
- Domain-based session handling

**`config/tenancy.php`**

- Tenancy-specific configuration
- Database strategies
- Cache prefixes

---

## 📊 Database Structure

### Main Database (school_erp)

**Tables:**

- `tenants` - All tenants (shared and separate)
- `domains` - Custom domain mappings
- `admin_users` - Admin users (for shared database tenants)
- `super_admins` - Super admin accounts
- `tenant_color_palettes` - Custom color schemes
- `users` - Application users
- `sessions` - User sessions
- `cache` - Application cache
- `jobs` - Queue jobs
- `migrations` - Migration history
- Spatie permission tables (roles, permissions, etc.)

### Tenant Databases (school_erp_{subdomain})

**Tables (for separate database tenants):**

- `admin_users` - Tenant admin users
- `students` - Student records
- `teachers` - Teacher records
- `classes` - Class information
- `attendance` - Attendance records
- `grades` - Student grades
- `sessions` - NOT here (stored in main DB)
- All other tenant-specific tables

---

## 🎯 Key Features & Functionality

### 1. Multi-Tenancy

**Strategies:**

- **Shared Database:** All tenants in one database (tenant_id column)
- **Separate Database:** Each tenant has own database

**Domain-Based Routing:**

- `myschool.test` - Landing page
- `app.myschool.test` - Admin panel
- `{subdomain}.myschool.test` - Tenant sites

### 2. Domain-Based Environment Files

**Naming:** `.env.{full-domain}`

- `.env.abc.myschool.test`
- `.env.lps.myschool.test`
- `.env.school1.myschool.test`

**Content:**

- Full copy of main .env
- Only DB_* variables changed
- All other variables identical

**Loading:**

- Loaded by TenantEnvironmentService
- Only on tenant domain (not admin)
- Falls back to tenant model if missing

**Management:**

- Auto-created when creating tenant
- Auto-updated when editing tenant
- Auto-deleted when switching to shared or deleting tenant
- UI in admin panel to view/download/regenerate

### 3. Database Connection Management

**Admin Panel Context:**

- Uses tenant model database config directly
- No env file loading
- Works immediately for testing/setup

**Tenant Domain Context:**

- Loads `.env.{domain}` if exists
- Falls back to tenant model
- Automatic connection switching

**Connection Strategy:**

- Always use 127.0.0.1 (not localhost)
- Empty unix_socket to force TCP/IP
- Prevents "No such file or directory" errors

### 4. Authentication Flow

**Tenant Login:**

1. Detect tenant from subdomain
2. Initialize tenant context (loads env if needed)
3. Authenticate user against correct database
4. Validate user is active
5. Create session (stored in main database)
6. Redirect to tenant dashboard

**Admin Login:**

1. Use admin domain
2. Authenticate against main database
3. Check admin type
4. Redirect based on policy

### 5. Admin Operations

**For Separate Database Tenants:**

- Test Connection
- Create Database
- Run Migrations
- View Database Info
- Manage Users
- View/Download/Regenerate Env File

**All operations use tenant model config, not env files**

---

## 📦 Dependencies

**Key Packages:**

- Laravel 11
- Livewire 3
- Spatie Permissions
- Stancl Tenancy
- Tailwind CSS
- Alpine.js

---

## 🔒 Security

**Environment Files:**

- `.env.*` protected by .gitignore
- Only `.env.example` in version control
- Credentials not in code

**Database:**

- TCP/IP connection (127.0.0.1:3306)
- No socket file dependency
- Secure password hashing

**Sessions:**

- Always stored in main database
- Prevents cross-tenant session issues

**Access Control:**

- Role-based permissions
- Domain validation
- Admin type checking (school_admin, super_admin)

---

## 🚀 Deployment

**Hosting:**

- Laravel Herd (development)
- Apache/Nginx (production options)

**Environment Files:**

- Main: `.env`
- Domains: `.env.{subdomain}.{primary_domain}`
- Example: `.env.example`

**Database Setup:**

1. Main database for central data
2. Separate databases per tenant (optional)
3. Auto-creation via admin panel

---

## 📝 Key Implementation Details

### Environment File System

**File Creation:**

- Auto-created in `TenantController::createTenantEnvironmentFile()`
- Triggered when creating/editing tenant with separate database
- Uses `TenantEnvironmentService::createTenantEnvironmentFile()`

**File Loading:**

- Loaded in `TenantEnvironmentService::loadTenantEnvironment()`
- Called by `TenantContextService::configureDatabase()`
- Only on tenant domain, not admin panel

**File Format:**

- Full copy of `.env`
- Only DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD changed
- Forces 127.0.0.1 instead of localhost
- Forces empty DB_SOCKET

### Database Connection

**Shared Database:**

- Connection: `mysql` (main database)
- Filter by `tenant_id` in queries
- Simple and fast

**Separate Database:**

- Connection: `tenant_{tenant_id}`
- Dynamic configuration
- Loaded from env file or tenant model
- No `tenant_id` filter needed
- Forces TCP/IP (127.0.0.1)

### Authentication

**Process:**

1. `LoginController::login()` - Entry point
2. `TenantAuthenticationService::authenticateForTenant()` - Main auth
3. `TenantContextService::initializeContext()` - Setup environment
4. `TenantAuthenticationService::authenticateUser()` - Unified user lookup
5. `Auth::guard('admin')->login()` - Create session
6. Redirect to dashboard

**No Redundant Validations:**

- Authentication validates user belongs to tenant
- No additional checks in middleware
- No double validation after login

---

## 🎨 UI Components

### Admin Panel Features

**Tenant Management:**

- List tenants with pagination
- Create/Edit/Delete tenants
- Toggle active status
- Database operations panel
- Environment file management UI
- Live database monitoring

**Environment File UI:**

- Status indicator (exists/not found)
- View file button
- Download file button
- Regenerate file button
- Edit settings link

**User Management:**

- List tenant users
- Create/Edit/Delete users
- Change passwords
- Role assignment

### Tenant Dashboard

**Statistics:**

- Total students/teachers/classes
- Active students
- Today's attendance
- Recent enrollments

**Features:**

- Recent activities
- Upcoming events
- Quick actions

---

## 🔍 Debugging & Testing

**Test Commands:**

```bash
# Test tenant database
php artisan tenant:test-db {subdomain}

# Setup tenant database
php artisan tenant:setup-db {subdomain} --create-db --run-migrations

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Check Logs:**

```bash
tail -f src/storage/logs/laravel.log | grep tenant
```

**Test Database:**

```bash
mysql -h 127.0.0.1 -P 3306 -u root {database_name}
```

---

## 📋 Summary

**Total Files:**

- PHP Files: ~100+
- Blade Templates: ~70+
- JavaScript: 2 main files
- CSS: Tailwind + custom
- Config: 15+ files
- Migrations: 12 files
- Commands: 2 custom commands

**Key Technologies:**

- Laravel 11
- MySQL (via DBngin/Herd)
- Multi-tenancy (shared + separate databases)
- Domain-based environment loading
- Role-based access control
- Livewire for reactive components

**Main Innovations:**

- ✅ Auto-create `.env.{domain}` files
- ✅ Domain-based environment selection
- ✅ Unified authentication (no duplicate code)
- ✅ TCP/IP connection (avoids socket errors)
- ✅ Admin panel env file management UI
- ✅ Graceful fallbacks (env file optional)

---

**Last Updated:** October 11, 2025  
**Version:** 2.0 - Domain-Based Environment System
