#!/bin/bash

# School ERP VHost Deployment Script
# Usage: ./deploy-vhost.sh /path/to/your/project

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if project path is provided
if [ -z "$1" ]; then
    echo -e "${RED}Error: Project path is required${NC}"
    echo "Usage: $0 /path/to/your/laravel/project"
    exit 1
fi

PROJECT_PATH="$1"
VHOST_TEMPLATE="school-erp.vhost.conf"
VHOST_NAME="school-erp"
NGINX_SITES_AVAILABLE="/etc/nginx/sites-available"
NGINX_SITES_ENABLED="/etc/nginx/sites-enabled"

# Validate project path
if [ ! -d "$PROJECT_PATH" ]; then
    echo -e "${RED}Error: Project path '$PROJECT_PATH' does not exist${NC}"
    exit 1
fi

if [ ! -f "$PROJECT_PATH/artisan" ]; then
    echo -e "${RED}Error: '$PROJECT_PATH' does not appear to be a Laravel project${NC}"
    exit 1
fi

# Check if template exists
if [ ! -f "$VHOST_TEMPLATE" ]; then
    echo -e "${RED}Error: VHost template '$VHOST_TEMPLATE' not found${NC}"
    exit 1
fi

echo -e "${YELLOW}Deploying School ERP VHost Configuration...${NC}"
echo "Project Path: $PROJECT_PATH"
echo "VHost Name: $VHOST_NAME"

# Create the actual vhost file by replacing placeholder
echo -e "${YELLOW}Creating vhost configuration...${NC}"
sed "s|{{PROJECT_PATH}}|$PROJECT_PATH|g" "$VHOST_TEMPLATE" > "${VHOST_NAME}.conf"

# Copy to nginx sites-available
echo -e "${YELLOW}Copying to nginx sites-available...${NC}"
sudo cp "${VHOST_NAME}.conf" "$NGINX_SITES_AVAILABLE/"

# Create symlink in sites-enabled
echo -e "${YELLOW}Creating symlink in sites-enabled...${NC}"
if [ -L "$NGINX_SITES_ENABLED/$VHOST_NAME.conf" ]; then
    echo -e "${YELLOW}Removing existing symlink...${NC}"
    sudo rm "$NGINX_SITES_ENABLED/$VHOST_NAME.conf"
fi

sudo ln -s "$NGINX_SITES_AVAILABLE/$VHOST_NAME.conf" "$NGINX_SITES_ENABLED/"

# Test nginx configuration
echo -e "${YELLOW}Testing nginx configuration...${NC}"
if sudo nginx -t; then
    echo -e "${GREEN}✓ Nginx configuration test passed${NC}"
    
    # Reload nginx
    echo -e "${YELLOW}Reloading nginx...${NC}"
    sudo systemctl reload nginx
    echo -e "${GREEN}✓ Nginx reloaded successfully${NC}"
else
    echo -e "${RED}✗ Nginx configuration test failed${NC}"
    echo -e "${YELLOW}Removing symlink...${NC}"
    sudo rm "$NGINX_SITES_ENABLED/$VHOST_NAME.conf"
    exit 1
fi

# Set proper permissions for Laravel
echo -e "${YELLOW}Setting Laravel permissions...${NC}"
sudo chown -R www-data:www-data "$PROJECT_PATH/storage"
sudo chown -R www-data:www-data "$PROJECT_PATH/bootstrap/cache"
sudo chmod -R 775 "$PROJECT_PATH/storage"
sudo chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

echo -e "${GREEN}✓ VHost deployment completed successfully!${NC}"
echo
echo "Next steps:"
echo "1. Update your DNS records:"
echo "   - myschool.com          A    → Your server IP"
echo "   - app.myschool.com      A    → Your server IP"
echo "   - *.myschool.com        A    → Your server IP"
echo
echo "2. Obtain SSL certificates:"
echo "   - For myschool.com and app.myschool.com"
echo "   - Wildcard cert for *.myschool.com"
echo "   - Update certificate paths in the vhost file"
echo
echo "3. Configure your Laravel environment:"
echo "   - Set APP_ENV=production"
echo "   - Configure database connections"
echo "   - Set up queue workers"
echo
echo "4. Test the configuration:"
echo "   - Visit https://myschool.com (landing page)"
echo "   - Visit https://app.myschool.com (admin)"
echo "   - Visit https://tenant1.myschool.com (test tenant)"

# Clean up temporary file
rm "${VHOST_NAME}.conf"
