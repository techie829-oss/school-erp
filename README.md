# 🎓 School ERP System

A comprehensive, multi-tenant School Management System built with Laravel 11, Livewire, and Tailwind CSS.

## 📋 Quick Links

- **[Current Features](CURRENT_FEATURES.md)** - Complete list of implemented features
- **[User Guide](USER_GUIDE.md)** - How to use the system
- **[Requirements](requirements_document.md)** - Original project requirements
- **[Teacher Management Plan](TEACHER_MANAGEMENT_PLAN.md)** - Next feature implementation

## 🚀 Features

### ✅ Implemented

- **Multi-Tenancy** - Subdomain-based school isolation
- **Authentication** - Two-guard system (Super Admin + School Users)
- **Student Management** - Complete CRUD, promotions, academic history
- **Class & Section Management** - Full organization structure
- **Settings System** - Tenant-specific configuration
- **Super Admin Panel** - Tenant management, users, system tools

### 🔄 In Progress

- **Teacher Management** - Profiles, qualifications, attendance (Planned)

### 📝 Planned

- Attendance System
- Fee Management
- Exams & Grades
- Reports & Analytics
- Communication System

## 🛠️ Tech Stack

- **Backend:** Laravel 11.x
- **Frontend:** Livewire 3.x, Tailwind CSS
- **Database:** MySQL (shared with tenant isolation)
- **Server:** Laravel Herd (local development)
- **Authentication:** Laravel Breeze + Custom Guards

## 📁 Project Structure

```
school-erp/
├── src/                    # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/           # Super admin controllers
│   │   │   │   └── Tenant/Admin/    # School admin controllers
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   │   ├── Student.php
│   │   │   ├── SchoolClass.php
│   │   │   ├── Section.php
│   │   │   ├── ClassEnrollment.php
│   │   │   └── Traits/ForTenant.php
│   │   └── Services/
│   ├── database/
│   │   └── migrations/
│   ├── resources/
│   │   └── views/
│   │       ├── admin/               # Super admin views
│   │       ├── tenant/admin/        # School admin views
│   │       └── school/              # Public school pages
│   └── routes/
│       └── web.php                  # All routes (domain-based)
├── docs/                   # Legacy documentation
├── CURRENT_FEATURES.md     # ✅ Feature status
├── USER_GUIDE.md           # 📚 User documentation
├── TEACHER_MANAGEMENT_PLAN.md  # 📋 Next implementation
└── README.md               # This file
```

## 🚦 Getting Started

### Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Laravel Herd (recommended)

### Installation

1. **Clone the repository**

```bash
git clone https://github.com/techie829-oss/school-erp.git
cd school-erp
```

2. **Install dependencies**

```bash
cd src
composer install
npm install
```

3. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Update `.env` with your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**

```bash
php artisan migrate
```

6. **Seed database**

```bash
php artisan db:seed
```

7. **Build assets**

```bash
npm run build
```

8. **Start development server**

```bash
php artisan serve
# or if using Herd, access via:
# http://app.myschool.test (Super Admin)
# http://[tenant].myschool.test (School)
```

### Default Login Credentials

**Super Admin:**

- URL: `http://app.myschool.test/admin/login`
- Email: Check seeder for credentials

**School Admin:**

- URL: `http://[tenant].myschool.test/login`
- Email: Check tenant users seeder

## 🏗️ Development

### Adding a New Feature

Follow the complete feature implementation approach:

1. ✅ Database migrations & seeders
2. ✅ Models with relationships
3. ✅ Controllers with full CRUD logic
4. ✅ Routes (in web.php)
5. ✅ Views (all pages: index, create, edit, show)
6. ✅ Middleware/Policies for authorization
7. ✅ Tests (Feature & Unit)
8. ✅ Documentation updates

**See:** `TEACHER_MANAGEMENT_PLAN.md` for detailed example

### Code Style

- Follow PSR-12 coding standards
- Use Laravel best practices
- Add PHPDoc type hints in Blade views
- Use Tailwind CSS utility classes

### Testing

```bash
php artisan test
```

## 📚 Documentation

- **[CURRENT_FEATURES.md](CURRENT_FEATURES.md)** - What's built and what's planned
- **[USER_GUIDE.md](USER_GUIDE.md)** - How to use the system
- **[requirements_document.md](requirements_document.md)** - Original requirements
- **[TEACHER_MANAGEMENT_PLAN.md](TEACHER_MANAGEMENT_PLAN.md)** - Next feature specs
- **[herd-setup.md](herd-setup.md)** - Local development setup

## 🤝 Contributing

1. Create a feature branch
2. Make your changes
3. Write/update tests
4. Update documentation
5. Submit a pull request

## 📄 License

This project is private and proprietary.

## 👥 Team

- **Developer:** [Your Name]
- **Project:** School ERP System
- **Started:** August 2025

## 📞 Support

For support and queries:

- Create an issue in the repository
- Contact the development team

---

**Version:** 1.0.0  
**Last Updated:** October 14, 2025  
**Status:** Active Development
