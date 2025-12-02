# 📝 School ERP - CMS (Content Management System) Implementation Plan

**Created:** December 2025  
**Status:** Planning Phase  
**Priority:** High  
**Document Purpose:** Comprehensive implementation plan for CMS features

---

## 📊 EXECUTIVE SUMMARY

### CMS Overview

The CMS module will enable schools to manage their website content, pages, blog posts, media library, and other public-facing content directly from the admin panel. This will allow schools to maintain their own website without requiring technical knowledge.

### Current State Analysis

**Existing Static Pages:**
- ✅ Home (`/`) - `school/home.blade.php`
- ✅ About (`/about`) - `school/about.blade.php`
- ✅ Programs (`/programs`) - `school/programs.blade.php`
- ✅ Facilities (`/facilities`) - `school/facilities.blade.php`
- ✅ Admission (`/admission`) - `school/admission.blade.php`
- ✅ Contact (`/contact`) - `school/contact.blade.php`
- ✅ Layout - `school/layout.blade.php` (uses ColorPaletteService)

**Current Implementation:**
- Pages are static Blade templates
- Served by `SchoolController` with hardcoded routes
- Uses `ColorPaletteService` for dynamic colors
- Navigation is hardcoded in layout
- No CMS backend exists yet

**Migration Strategy:**
1. **Phase 1:** Make existing static pages editable via CMS (keep same structure, add CMS backend)
2. **Phase 2:** Add dynamic content features (blog, gallery, etc.)
3. **Phase 3:** Add advanced features (menus, widgets, SEO)

### CMS Features Overview

| Feature | Priority | Status | Estimated Time |
|---------|----------|--------|----------------|
| CMS Settings & Theme (SCSS) | High | Not Started | 3 days |
| Pages Management (Migrate Static) | High | Not Started | 1 week |
| Media Library | High | Not Started | 1 week |
| Blog/News Management | High | Not Started | 1 week |
| Menu Management | Medium | Not Started | 3 days |
| Slider/Banner Management | Medium | Not Started | 3 days |
| Gallery Management | Medium | Not Started | 3 days |
| FAQ Management | Low | Not Started | 2 days |
| Testimonials | Low | Not Started | 2 days |
| SEO Management | Medium | Not Started | 3 days |
| Widget Management | Low | Not Started | 1 week |

**Total Estimated Time:** 6-7 weeks (including static page migration)

---

## 🎯 PHASE 0: CMS Settings & Theme Configuration (Week 1)

### 0. CMS Settings & Theme Management

**Status:** Not Started  
**Priority:** High  
**Estimated Time:** 3 days

#### Database Schema

- `cms_settings` table
  - id, tenant_id, site_name, site_tagline, logo, favicon, footer_text, contact_email, contact_phone, contact_address, social_facebook, social_twitter, social_instagram, social_linkedin, created_at, updated_at

- `cms_theme_settings` table
  - id, tenant_id, primary_color_50, primary_color_100, primary_color_500, primary_color_600, primary_color_700, primary_color_900, secondary_color_50, secondary_color_100, secondary_color_500, secondary_color_600, secondary_color_700, secondary_color_900, accent_color_50, accent_color_100, accent_color_500, accent_color_600, accent_color_700, accent_color_900, success_color, warning_color, error_color, info_color, custom_css, created_at, updated_at

#### Controllers

- `CmsSettingsController.php` - CMS settings management
- `CmsThemeController.php` - Theme/SCSS settings management

#### Views

- `cms/settings/index.blade.php` - Settings dashboard
- `cms/settings/general.blade.php` - General settings form
- `cms/settings/theme.blade.php` - Theme/SCSS settings form (color picker, CSS editor)
- `cms/settings/social.blade.php` - Social media settings

#### Routes

- `/admin/cms/settings` - Settings routes
- `/admin/cms/settings/theme` - Theme settings route

#### Features

- General site settings (name, tagline, logo, favicon)
- Contact information
- Social media links
- **Theme/SCSS Settings:**
  - Color palette management (Primary, Secondary, Accent)
  - Individual color shade pickers (50, 100, 500, 600, 700, 900)
  - Success, Warning, Error, Info colors
  - Custom CSS editor
  - Live preview
  - Export/Import theme settings
- Settings are stored in database and override ColorPaletteService defaults

#### Integration

- **View Composer:** Create `CmsThemeViewComposer` to inject theme settings into all views
- **ColorPaletteService Integration:**
  - Modify `ColorPaletteService::getAllColors()` to check CMS theme settings first
  - Fallback to `TenantColorPalette` if CMS settings not found
  - Maintain backward compatibility
- **Layout Updates:**
  - Update `school/layout.blade.php` to use CMS theme settings (lines 35-86)
  - Update `tenant/layouts/admin.blade.php` to use CMS theme settings (lines 18-131)
  - Both layouts will use same CSS variable structure
- **Admin Interface:**
  - Color picker for each shade (50, 100, 500, 600, 700, 900)
  - Live preview of color changes
  - Custom CSS editor with syntax highlighting
  - Export/Import theme settings as JSON

---

## 🎯 PHASE 1: Static Pages to CMS (Weeks 1-2)

### 1. Pages Management (Migrate Existing Static Pages)

**Status:** Not Started  
**Priority:** High  
**Estimated Time:** 1 week

#### Database Schema

- `cms_pages` table
  - id, tenant_id, title, slug, content, excerpt, template (home, about, programs, facilities, admission, contact, custom), status, published_at, author_id, meta_title, meta_description, meta_keywords, featured_image, parent_id, order, created_at, updated_at

#### Controllers

- `CmsPageController.php` - Page management with full CRUD
- Update `SchoolController.php` to fetch pages from CMS instead of static views

#### Admin Views

- `cms/pages/index.blade.php` - Page list with filters
- `cms/pages/create.blade.php` - Create page form
- `cms/pages/edit.blade.php` - Edit page form (with template selector)
- `cms/pages/show.blade.php` - Page preview

#### Public Views (Update Existing)

- Keep existing `school/*.blade.php` files but make them dynamic
- `school/home.blade.php` - Fetch from CMS with template="home"
- `school/about.blade.php` - Fetch from CMS with template="about"
- `school/programs.blade.php` - Fetch from CMS with template="programs"
- `school/facilities.blade.php` - Fetch from CMS with template="facilities"
- `school/admission.blade.php` - Fetch from CMS with template="admission"
- `school/contact.blade.php` - Fetch from CMS with template="contact"
- `school/page.blade.php` - Generic page template for custom pages

#### Routes

- `/admin/cms/pages` - All CRUD routes
- Update public routes to use CMS pages dynamically

#### Features

- **Migrate existing static pages:**
  - Home, About, Programs, Facilities, Admission, Contact
  - Each page becomes editable via CMS
  - Preserve existing design and structure
- Page creation & management
- Rich text editor (TinyMCE or similar)
- Page templates (predefined + custom)
- SEO fields (meta title, description, keywords)
- Featured image support
- Slug generation
- Page status (Draft, Published, Archived)
- Page preview
- Page hierarchy (parent/child pages)
- Template-specific fields (e.g., Contact form fields for contact page)

#### Migration Steps

1. Create `cms_pages` table
2. Seed existing static page content into database
3. Update `SchoolController` to fetch from database
4. Create admin interface for editing pages
5. Test all existing pages work with CMS backend

---

### 2. Media Library

**Status:** Not Started  
**Priority:** High  
**Estimated Time:** 1 week

**Note:** Media Library is needed before Blog/News to support image uploads

#### Database Schema

- `cms_posts` table
  - id, tenant_id, title, slug, content, excerpt, featured_image, author_id, category_id, status, published_at, views_count, created_at, updated_at

- `cms_post_categories` table
  - id, tenant_id, name, slug, description, parent_id, created_at, updated_at

- `cms_post_tags` table
  - id, tenant_id, name, slug, created_at, updated_at

- `cms_post_tag_pivot` table
  - post_id, tag_id

#### Controllers

- `CmsPostController.php` - Post management
- `CmsPostCategoryController.php` - Category management
- `CmsPostTagController.php` - Tag management

#### Views

- `cms/posts/index.blade.php` - Post list
- `cms/posts/create.blade.php` - Create post
- `cms/posts/edit.blade.php` - Edit post
- `cms/posts/show.blade.php` - Post preview
- `cms/posts/categories/*` - Category management
- `cms/posts/tags/*` - Tag management

#### Routes

- `/admin/cms/posts` - Post routes
- `/admin/cms/posts/categories` - Category routes
- `/admin/cms/posts/tags` - Tag routes

#### Features

- Blog post creation & management
- Rich text editor
- Featured image
- Categories and tags
- Post scheduling
- View counter
- Related posts
- Post excerpts
- SEO optimization
- Social sharing meta tags
- Comments system (optional)

---

### 3. Blog/News Management

**Status:** Not Started  
**Priority:** High  
**Estimated Time:** 1 week

**Note:** Implemented after Media Library is ready

#### Database Schema

- `cms_media` table
  - id, tenant_id, name, file_name, file_path, file_type, file_size, mime_type, alt_text, caption, uploaded_by, created_at, updated_at

#### Controllers

- `CmsMediaController.php` - Media management

#### Views

- `cms/media/index.blade.php` - Media library (grid/list view)
- `cms/media/upload.blade.php` - Upload interface
- `cms/media/show.blade.php` - Media details

#### Routes

- `/admin/cms/media` - Media routes
- `/admin/cms/media/upload` - Upload route
- `/admin/cms/media/{id}` - Media details

#### Features

- File upload (images, documents, videos)
- Media library with grid/list view
- Image cropping/resizing
- File type filtering
- Search functionality
- Bulk operations (delete, move)
- Media folders/categories
- Alt text and captions
- File size display
- Preview functionality
- Drag & drop upload
- Multiple file upload

---

## 🎯 PHASE 2: Navigation & Display Features (Week 4)

### 4. Menu Management

**Status:** Not Started  
**Priority:** Medium  
**Estimated Time:** 3 days

#### Database Schema

- `cms_menus` table
  - id, tenant_id, name, location, status, created_at, updated_at

- `cms_menu_items` table
  - id, menu_id, parent_id, title, url, type (page, post, custom, category), target_id, order, icon, css_class, status, created_at, updated_at

#### Controllers

- `CmsMenuController.php` - Menu management
- `CmsMenuItemController.php` - Menu item management

#### Views

- `cms/menus/index.blade.php` - Menu list
- `cms/menus/create.blade.php` - Create menu
- `cms/menus/edit.blade.php` - Edit menu with drag-drop items
- `cms/menus/items/*` - Menu item management

#### Routes

- `/admin/cms/menus` - Menu routes
- `/admin/cms/menus/{id}/items` - Menu item routes

#### Features

- Multiple menu locations (Header, Footer, Sidebar)
- Drag & drop menu item ordering
- Nested menu items (submenus)
- Menu item types (Page, Post, Custom URL, Category)
- Menu item icons
- Menu item CSS classes
- Menu visibility settings

---

### 5. Slider/Banner Management

**Status:** Not Started  
**Priority:** Medium  
**Estimated Time:** 3 days

#### Database Schema

- `cms_sliders` table
  - id, tenant_id, name, location, autoplay, autoplay_speed, status, created_at, updated_at

- `cms_slider_items` table
  - id, slider_id, title, description, image, link, link_text, order, status, created_at, updated_at

#### Controllers

- `CmsSliderController.php` - Slider management
- `CmsSliderItemController.php` - Slider item management

#### Views

- `cms/sliders/index.blade.php` - Slider list
- `cms/sliders/create.blade.php` - Create slider
- `cms/sliders/edit.blade.php` - Edit slider with items
- `cms/sliders/items/*` - Slider item management

#### Routes

- `/admin/cms/sliders` - Slider routes

#### Features

- Multiple sliders
- Slider locations (Homepage, About, etc.)
- Slider items with images
- Link and call-to-action buttons
- Autoplay settings
- Slide ordering
- Responsive images

---

### 6. Gallery Management

**Status:** Not Started  
**Priority:** Medium  
**Estimated Time:** 3 days

#### Database Schema

- `cms_galleries` table
  - id, tenant_id, name, description, cover_image, status, created_at, updated_at

- `cms_gallery_images` table
  - id, gallery_id, image, title, description, order, created_at, updated_at

#### Controllers

- `CmsGalleryController.php` - Gallery management
- `CmsGalleryImageController.php` - Gallery image management

#### Views

- `cms/galleries/index.blade.php` - Gallery list
- `cms/galleries/create.blade.php` - Create gallery
- `cms/galleries/edit.blade.php` - Edit gallery
- `cms/galleries/show.blade.php` - Gallery view with images

#### Routes

- `/admin/cms/galleries` - Gallery routes

#### Features

- Multiple galleries
- Gallery cover image
- Image upload and management
- Image ordering
- Gallery descriptions
- Lightbox preview
- Gallery categories

---

## 🎯 PHASE 3: Additional Features (Week 5)

### 7. FAQ Management

**Status:** Not Started  
**Priority:** Low  
**Estimated Time:** 2 days

#### Database Schema

- `cms_faqs` table
  - id, tenant_id, question, answer, category, order, status, created_at, updated_at

- `cms_faq_categories` table
  - id, tenant_id, name, description, created_at, updated_at

#### Controllers

- `CmsFaqController.php` - FAQ management
- `CmsFaqCategoryController.php` - FAQ category management

#### Views

- `cms/faqs/index.blade.php` - FAQ list
- `cms/faqs/create.blade.php` - Create FAQ
- `cms/faqs/edit.blade.php` - Edit FAQ

#### Routes

- `/admin/cms/faqs` - FAQ routes

#### Features

- FAQ creation & management
- FAQ categories
- FAQ ordering
- Accordion display
- Search functionality

---

### 8. Testimonials Management

**Status:** Not Started  
**Priority:** Low  
**Estimated Time:** 2 days

#### Database Schema

- `cms_testimonials` table
  - id, tenant_id, name, designation, company, content, image, rating, status, order, created_at, updated_at

#### Controllers

- `CmsTestimonialController.php` - Testimonial management

#### Views

- `cms/testimonials/index.blade.php` - Testimonial list
- `cms/testimonials/create.blade.php` - Create testimonial
- `cms/testimonials/edit.blade.php` - Edit testimonial

#### Routes

- `/admin/cms/testimonials` - Testimonial routes

#### Features

- Testimonial creation
- Author information
- Image upload
- Rating system
- Testimonial ordering
- Display on homepage

---

### 9. SEO Management

**Status:** Not Started  
**Priority:** Medium  
**Estimated Time:** 3 days

#### Database Schema

- `cms_seo_settings` table
  - id, tenant_id, meta_title, meta_description, meta_keywords, og_image, twitter_card, robots, canonical_url, created_at, updated_at

#### Controllers

- `CmsSeoController.php` - SEO settings management

#### Views

- `cms/seo/settings.blade.php` - SEO settings form
- `cms/seo/sitemap.blade.php` - Sitemap generator

#### Routes

- `/admin/cms/seo` - SEO routes
- `/admin/cms/seo/sitemap` - Sitemap route

#### Features

- Global SEO settings
- Per-page SEO settings
- Sitemap generation
- Robots.txt management
- Open Graph tags
- Twitter Card tags
- Schema markup (optional)

---

## 🎯 PHASE 4: Advanced Features (Week 6)

### 10. Widget Management

**Status:** Not Started  
**Priority:** Low  
**Estimated Time:** 1 week

#### Database Schema

- `cms_widgets` table
  - id, tenant_id, name, type, location, content, settings (JSON), order, status, created_at, updated_at

#### Controllers

- `CmsWidgetController.php` - Widget management

#### Views

- `cms/widgets/index.blade.php` - Widget list
- `cms/widgets/create.blade.php` - Create widget
- `cms/widgets/edit.blade.php` - Edit widget

#### Routes

- `/admin/cms/widgets` - Widget routes

#### Features

- Widget types (Text, HTML, Recent Posts, Categories, Tags, Custom)
- Widget locations (Sidebar, Footer, etc.)
- Widget ordering
- Widget settings
- Custom HTML widgets
- Dynamic content widgets

---

## 📋 IMPLEMENTATION CHECKLIST

### Database & Models

- [ ] Create CMS settings migrations (`cms_settings`, `cms_theme_settings`)
- [ ] Create CMS pages migration (`cms_pages`)
- [ ] Create all other CMS migrations
- [ ] Create all CMS models with relationships
- [ ] Add ForTenant trait to all models
- [ ] Add proper indexes and foreign keys
- [ ] Seed existing static page content into `cms_pages`

### Controllers

- [ ] Create `CmsSettingsController` and `CmsThemeController`
- [ ] Create all other CMS controllers
- [ ] Implement CRUD operations
- [ ] Add validation
- [ ] Add file upload handling
- [ ] Add image processing
- [ ] Implement search and filtering
- [ ] Update `SchoolController` to use CMS pages

### Views

- [ ] Create all CMS views
- [ ] Implement rich text editor
- [ ] Add media picker integration
- [ ] Create responsive layouts
- [ ] Add drag & drop functionality
- [ ] Implement preview functionality

### Routes

- [ ] Add all CMS routes
- [ ] Add route groups
- [ ] Add middleware
- [ ] Add route names

### Frontend Integration

- [ ] Create View Composer for theme settings
- [ ] Update `school/layout.blade.php` to use CMS theme settings
- [ ] Update `tenant/layouts/admin.blade.php` to use CMS theme settings
- [ ] Update existing `school/*.blade.php` views to use dynamic CMS content
- [ ] Create public-facing routes (update existing)
- [ ] Implement menu rendering
- [ ] Implement slider rendering
- [ ] Implement gallery display
- [ ] Add SEO meta tags
- [ ] Create sitemap.xml generator

### Features

- [ ] Rich text editor integration
- [ ] Image upload and processing
- [ ] File management
- [ ] Search functionality
- [ ] SEO optimization
- [ ] Preview functionality
- [ ] Bulk operations

### Testing

- [ ] Test all CRUD operations
- [ ] Test file uploads
- [ ] Test image processing
- [ ] Test search functionality
- [ ] Test SEO features
- [ ] Test public-facing pages

### Documentation

- [ ] Update feature list
- [ ] Add CMS to navigation
- [ ] Create user guide
- [ ] Document API endpoints (if any)

---

## 🎯 SUCCESS CRITERIA

Each CMS feature is considered complete when:

1. ✅ All database tables and models are created
2. ✅ All controllers have full CRUD functionality
3. ✅ All views are created and responsive
4. ✅ All routes are properly configured
5. ✅ File upload and media management works
6. ✅ Rich text editor is integrated
7. ✅ SEO features are working
8. ✅ Public-facing pages render correctly
9. ✅ Navigation menus work
10. ✅ Search functionality works

---

## 📝 TECHNICAL NOTES

### Rich Text Editor

- Recommended: TinyMCE or CKEditor
- Should support image upload
- Should support media library integration
- Should support custom formatting

### Image Processing

- Use Intervention Image or similar
- Support multiple image sizes
- Automatic thumbnail generation
- Image optimization

### File Storage

- Use Laravel Storage facade
- Support multiple storage drivers (local, S3, etc.)
- Organize files by tenant
- Implement file cleanup

### Theme/SCSS Settings

- Store color palette in `cms_theme_settings` table
- Support all color shades (50, 100, 500, 600, 700, 900)
- Custom CSS editor for advanced styling
- Live preview functionality
- Export/Import theme settings
- **Integration with ColorPaletteService:**
  - CMS theme settings will override `TenantColorPalette` defaults
  - Create View Composer to inject CMS theme settings
  - Update `ColorPaletteService` to check CMS settings first, then fallback to `TenantColorPalette`
  - Apply to both admin (`tenant/layouts/admin.blade.php`) and public (`school/layout.blade.php`) layouts
  - Same CSS variable structure as current implementation
  - Support for all existing color classes (primary, secondary, accent, success, warning, error, info)

### SEO

- Generate sitemap.xml automatically
- Support robots.txt customization
- Add Open Graph and Twitter Card tags
- Support schema markup

### Performance

- Implement caching for public pages
- Optimize image loading
- Lazy load images
- Cache menu structures

---

## 🔄 INTEGRATION POINTS

### With Existing Modules

- **Media Library** - Can be used by all modules
- **Pages** - Can link to student/teacher portals
- **Blog** - Can feature school news and events
- **Gallery** - Can showcase school activities
- **Events** - Can integrate with Events & Calendar module

### Public Website

- Create separate public routes
- Create public templates
- Implement caching
- Add CDN support (optional)

---

## 📊 PRIORITY MATRIX

### Phase 0: Foundation (Week 1)

0. CMS Settings & Theme Management (SCSS/Color Settings)

### Must Have (Phase 1)

1. Pages Management (Migrate existing static pages)
2. Media Library (Needed for images)
3. Blog/News Management

### Should Have (Phase 2)

4. Menu Management
5. Slider/Banner Management
6. Gallery Management

### Nice to Have (Phase 3-4)

7. FAQ Management
8. Testimonials
9. SEO Management
10. Widget Management

---

## 🚀 GETTING STARTED

### Step 0: CMS Settings & Theme (Week 1)

1. Create `cms_settings` and `cms_theme_settings` migrations
2. Create `CmsSettings` and `CmsThemeSettings` models
3. Create `CmsSettingsController` and `CmsThemeController`
4. Create settings views with color pickers and CSS editor
5. Create View Composer to inject theme settings
6. Update `school/layout.blade.php` to use CMS theme settings
7. Update `tenant/layouts/admin.blade.php` to use CMS theme settings
8. Test theme customization

### Step 1: Migrate Static Pages (Week 1-2)

1. Create `cms_pages` migration
2. Create `CmsPage` model
3. Seed existing static page content into database
4. Create `CmsPageController` with CRUD
5. Create admin views for page management
6. Update `SchoolController` to fetch pages from CMS
7. Update existing `school/*.blade.php` views to use dynamic content
8. Test all existing pages work with CMS backend
9. Add rich text editor integration

### Step 2: Media Library (Week 2)

1. Create `cms_media` migration
2. Create `CmsMedia` model
3. Create `CmsMediaController`
4. Create media library views (grid/list)
5. Implement file upload with drag & drop
6. Add image processing (thumbnails, resizing)
7. Integrate media picker into page editor

### Step 3: Blog/News (Week 3)

1. Create blog migrations (posts, categories, tags)
2. Create blog models
3. Create blog controllers
4. Create blog admin views
5. Create public blog views
6. Add blog routes (admin + public)

### Step 4: Navigation & Display (Week 4)

1. Implement Menu Management
2. Implement Slider Management
3. Implement Gallery Management
4. Update public layout to use dynamic menus

### Step 5: Enhancements (Week 5)

1. Add FAQ Management
2. Add Testimonials
3. Add SEO Management
4. Add Widget Management

### Step 6: Public Website Polish

1. Implement dynamic menu rendering
2. Add SEO meta tags
3. Create sitemap.xml generator
4. Add caching for performance

---

---

## 📝 IMPLEMENTATION ORDER SUMMARY

### Phase 0: Foundation (Week 1) - START HERE
1. **CMS Settings & Theme Management**
   - Create `cms_settings` and `cms_theme_settings` tables
   - Build admin interface for theme/SCSS settings
   - Integrate with existing ColorPaletteService
   - Update both admin and public layouts to use CMS theme settings

### Phase 1: Static Pages Migration (Weeks 1-2)
2. **Pages Management**
   - Create `cms_pages` table
   - Seed existing static pages (home, about, programs, facilities, admission, contact)
   - Build admin interface for editing pages
   - Update `SchoolController` to fetch from CMS
   - Make existing static pages dynamic

3. **Media Library**
   - Create `cms_media` table
   - Build media library with upload functionality
   - Integrate media picker into page editor

### Phase 2: Dynamic Content (Week 3)
4. **Blog/News Management**
   - Create blog tables (posts, categories, tags)
   - Build blog admin interface
   - Create public blog views

### Phase 3: Navigation & Display (Week 4)
5. **Menu Management** - Dynamic navigation
6. **Slider/Banner Management** - Homepage sliders
7. **Gallery Management** - Image galleries

### Phase 4: Enhancements (Week 5-6)
8. **FAQ Management**
9. **Testimonials**
10. **SEO Management**
11. **Widget Management**

---

## 🎯 KEY DECISIONS

### Static Pages First
- **Why:** Existing pages are already working, just need to make them editable
- **Approach:** Migrate content to database, keep same templates, add CMS backend
- **Benefit:** Schools can immediately start editing their existing pages

### Theme Settings in CMS
- **Why:** SCSS settings in admin layout should be manageable via CMS
- **Approach:** Create CMS theme settings that override ColorPaletteService
- **Benefit:** Schools can customize their brand colors without code changes

### Media Library Before Blog
- **Why:** Blog needs images, so media library must be ready first
- **Approach:** Build media library, then integrate into blog
- **Benefit:** Consistent media management across all CMS features

---

**Document Version:** 2.0  
**Last Updated:** December 2025  
**Next Review:** After Phase 0 completion
