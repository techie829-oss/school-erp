# School ERP Multi-Tenancy System

## Overview

This School ERP system implements a sophisticated multi-tenancy architecture using the **Stancl Tenancy** package for Laravel. The system supports both **shared database** and **separate database** strategies, with automatic tenant detection based on domain names.

## 🏗️ Architecture

### Tenant Types

1. **Internal Admin** (`app.myschool.test`)
   - Database Strategy: Shared
   - Purpose: Super admin and internal management
   - Color Theme: Professional Blue

2. **Landing Page** (`myschool.test`)
   - Database Strategy: Shared
   - Purpose: Marketing and public information
   - Color Theme: Default Blue

3. **School A - Delhi Public School** (`schoola.myschool.test`)
   - Database Strategy: Shared
   - Purpose: School management
   - Color Theme: Green
   - Students: 1,200

4. **School B - Mumbai International** (`schoolb.myschool.test`)
   - Database Strategy: Separate Database
   - Purpose: International school management
   - Color Theme: Purple
   - Students: 800

5. **School C - Bangalore Tech Academy** (`schoolc.myschool.test`)
   - Database Strategy: Separate Database
   - Purpose: Technology academy
   - Color Theme: Red
   - Students: 600

## 🗄️ Database Strategies

### Shared Database Strategy

- **Use Case**: Smaller schools, internal admin, landing pages
- **Benefits**: Lower cost, easier maintenance, simpler backups
- **Implementation**: All data stored in one database with `tenant_id` field
- **Data Isolation**: Achieved through `tenant_id` foreign key relationships

### Separate Database Strategy

- **Use Case**: Large schools, enterprises, high-security requirements
- **Benefits**: Complete data isolation, better performance, compliance
- **Implementation**: Each tenant gets their own database
- **Data Portability**: Still maintains `tenant_id` for easy migration between strategies

## 🎨 Color Palette System

### Database-Driven Colors

- Each tenant has their own color palette stored in `tenant_color_palettes` table
- Colors automatically apply based on the current domain
- CSS custom properties generated dynamically for each tenant
- Support for primary, secondary, accent, and status colors

### Color Palette Structure

```sql
tenant_color_palettes:
├── tenant_id (string)
├── name (string)
├── is_active (boolean)
├── primary_50 to primary_900 (hex colors)
├── secondary_50 to secondary_900 (hex colors)
├── accent_50 to accent_900 (hex colors)
├── success, warning, error, info (hex colors)
└── timestamps
```

## 🔧 How It Works

### 1. Tenant Detection

```php
// TenantService automatically detects tenant based on domain
$tenant = $tenantService->getCurrentTenant($request);
$tenantId = $tenant->id; // e.g., 'school-a', 'internal', 'landing'
```

### 2. Color Palette Application

```php
// ColorPaletteService generates CSS for current tenant
$css = $colorService->generateCSSVariables($request);
// Automatically applies tenant-specific colors
```

### 3. Database Strategy Selection

```php
// System automatically uses appropriate database strategy
if ($tenantService->isSharedDatabase($request)) {
    // Use shared database with tenant_id filtering
} else {
    // Use separate database for tenant
}
```

## 🚀 Getting Started

### 1. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed tenants and color palettes
php artisan db:seed --class=TenantSeeder
php artisan db:seed --class=ColorPaletteSeeder
```

### 2. Local Development with Herd

```bash
# Link project to Herd
herd link src

# Access different tenants:
# - http://myschool.test (Landing)
# - http://app.myschool.test (Internal Admin)
# - http://schoola.myschool.test (Delhi Public School)
# - http://schoolb.myschool.test (Mumbai International)
# - http://schoolc.myschool.test (Bangalore Tech Academy)
```

### 3. Test Multi-Tenancy

Visit `/multi-tenancy-demo` to see:
- Current tenant information
- All tenants overview
- Database strategy explanations
- Links to test different tenants

## 📁 File Structure

```
src/
├── app/
│   ├── Models/
│   │   ├── Tenant.php                    # Tenant model
│   │   └── TenantColorPalette.php        # Color palette model
│   ├── Services/
│   │   ├── TenantService.php             # Tenant management
│   │   └── ColorPaletteService.php       # Color palette management
│   └── Http/Controllers/
│       ├── LandingController.php         # Landing pages
│       └── ColorPaletteController.php    # Admin color management
├── database/
│   ├── migrations/
│   │   └── create_tenant_color_palettes_table.php
│   └── seeders/
│       ├── TenantSeeder.php              # Tenant data
│       └── ColorPaletteSeeder.php        # Color palette data
└── resources/views/landing/
    ├── layout.blade.php                  # Base layout with dynamic colors
    ├── home.blade.php                    # Landing page
    ├── color-palette.blade.php           # Color showcase
    └── multi-tenancy-demo.blade.php      # Multi-tenancy demo
```

## 🔄 Adding New Tenants

### 1. Create Tenant

```php
// In TenantSeeder or via admin panel
Tenant::create([
    'id' => 'new-school',
    'data' => [
        'name' => 'New School Name',
        'domain' => 'newschool.myschool.test',
        'database_strategy' => 'shared', // or 'separate'
        'type' => 'school',
        'status' => 'active',
        'description' => 'School description',
        'student_count' => 500,
        'location' => 'City, Country',
    ],
]);
```

### 2. Create Color Palette

```php
TenantColorPalette::create([
    'tenant_id' => 'new-school',
    'name' => 'Custom Theme',
    'is_active' => true,
    'primary_500' => '#your-color',
    'primary_600' => '#your-color',
    // ... other colors
]);
```

## 🎯 Key Features

- ✅ **Automatic Tenant Detection** based on domain
- ✅ **Flexible Database Strategies** (shared/separate)
- ✅ **Dynamic Color Palettes** per tenant
- ✅ **CSS Custom Properties** for easy theming
- ✅ **Caching** for performance optimization
- ✅ **Admin Interface** for color management
- ✅ **Predefined Color Schemes** for quick setup
- ✅ **Data Portability** between strategies

## 🔒 Security & Isolation

- **Shared Database**: Data isolated via `tenant_id` field
- **Separate Database**: Complete physical separation
- **Tenant Context**: All operations scoped to current tenant
- **Access Control**: Role-based permissions per tenant

## 📊 Performance Considerations

- **Caching**: Tenant and color palette data cached for 1 hour
- **Database Connections**: Optimized connection pooling
- **CSS Generation**: Inline CSS for immediate color application
- **Lazy Loading**: Services loaded only when needed

## 🚀 Production Deployment

### 1. Domain Configuration

```nginx
# NGINX configuration for multiple domains
server {
    listen 443 ssl http2;
    server_name myschool.com *.myschool.com;
    
    # Pass tenant information to PHP
    fastcgi_param HTTP_TENANT_HOST $host;
    fastcgi_param HTTP_APP_TYPE tenant;
}
```

### 2. Database Setup

```bash
# For separate databases, create databases per tenant
mysql -e "CREATE DATABASE school_erp_school_b;"
mysql -e "CREATE DATABASE school_erp_school_c;"
```

### 3. SSL Certificates

```bash
# Wildcard certificate for *.myschool.com
# Individual certificates for custom domains
```

## 🤝 Contributing

1. Follow Laravel coding standards
2. Add tests for new tenant functionality
3. Update documentation for new features
4. Test with both database strategies

## 📞 Support

For questions about the multi-tenancy system:
- Check the demo page: `/multi-tenancy-demo`
- Review the color palette page: `/colors`
- Examine the database structure
- Check the service classes for implementation details

---

**Note**: This system is designed to be flexible and scalable. The same codebase can handle both small schools with shared databases and large enterprises with separate databases, all while maintaining consistent data structure and easy migration between strategies.
