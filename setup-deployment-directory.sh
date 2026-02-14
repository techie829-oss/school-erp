#!/bin/bash
# One-time setup script for production deployment directory
# Run this on srv1275352 as jadmin user

set -e

echo "========================================="
echo "School ERP Production Setup"
echo "========================================="
echo ""

DEPLOY_DIR="/opt/deployments/school-erp"
GIT_REPO="git@github.com:techie829-oss/school-erp.git"

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo "This script needs sudo privileges to create /opt/deployments"
    echo "Please run: sudo $0"
    exit 1
fi

echo "Step 1: Creating deployment directory..."
mkdir -p "$DEPLOY_DIR"
chown -R jadmin:jadmin "$DEPLOY_DIR"
echo "✓ Created $DEPLOY_DIR"

echo ""
echo "Step 2: Cloning repository..."
if [ -d "$DEPLOY_DIR/.git" ]; then
    echo "Repository already exists, pulling latest..."
    cd "$DEPLOY_DIR"
    sudo -u jadmin git pull origin main
else
    echo "Cloning fresh repository..."
    sudo -u jadmin git clone "$GIT_REPO" "$DEPLOY_DIR"
fi
echo "✓ Repository ready"

echo ""
echo "Step 3: Setting up SSH keys for Jenkins access..."
if [ ! -d "/var/jenkins_home/.ssh" ]; then
    echo "Creating Jenkins SSH directory..."
    docker exec jenkins mkdir -p /var/jenkins_home/.ssh
    docker exec jenkins chmod 700 /var/jenkins_home/.ssh
fi

# Copy SSH keys from jadmin to Jenkins container (if needed)
if [ -f "/home/jadmin/.ssh/id_rsa" ]; then
    echo "Copying SSH keys to Jenkins..."
    docker cp /home/jadmin/.ssh/id_rsa jenkins:/var/jenkins_home/.ssh/
    docker cp /home/jadmin/.ssh/id_rsa.pub jenkins:/var/jenkins_home/.ssh/
    docker exec jenkins chown -R jenkins:jenkins /var/jenkins_home/.ssh
    docker exec jenkins chmod 600 /var/jenkins_home/.ssh/id_rsa
    echo "✓ SSH keys configured"
else
    echo "⚠ Warning: No SSH keys found in /home/jadmin/.ssh/"
    echo "You may need to configure SSH keys for Jenkins"
fi

echo ""
echo "Step 4: Verifying deployment structure..."
cd "$DEPLOY_DIR"
echo "Files in deployment directory:"
ls -la

echo ""
echo "Checking critical files:"
[ -f "docker/nginx/default.conf" ] && echo "✓ docker/nginx/default.conf" || echo "✗ docker/nginx/default.conf MISSING"
[ -f "docker/Dockerfile" ] && echo "✓ docker/Dockerfile" || echo "✗ docker/Dockerfile MISSING"
[ -f "docker-compose.yml" ] && echo "✓ docker-compose.yml" || echo "✗ docker-compose.yml MISSING"

echo ""
echo "========================================="
echo "✓ Setup Complete!"
echo "========================================="
echo ""
echo "Deployment directory: $DEPLOY_DIR"
echo ""
echo "Next steps:"
echo "1. Update your Jenkinsfile in the repository"
echo "2. Ensure your Jenkins job uses the 'school-erp-env' credential for .env file"
echo "3. Trigger a Jenkins build"
echo ""
echo "The application will be deployed from $DEPLOY_DIR"
echo "NOT from the Jenkins workspace!"