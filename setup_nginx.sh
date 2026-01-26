#!/bin/bash

# Configuration
PROJECT_CONFIG="host_nginx_proxy.conf"
PROJECT_PATH=$(pwd)
CONFIG_SOURCE="$PROJECT_PATH/$PROJECT_CONFIG"
LINK_NAME="school-erp.conf"

echo "Setup Nginx Host Configuration for School ERP"
echo "============================================"

# Detect Nginx Configuration Directory
NGINX_CONF_DIR=""
POSSIBLE_DIRS=(
    "/opt/homebrew/etc/nginx"
    "/usr/local/etc/nginx"
    "/etc/nginx"
)

for dir in "${POSSIBLE_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        NGINX_CONF_DIR="$dir"
        echo "Found Nginx at: $dir"
        break
    fi
done

if [ -z "$NGINX_CONF_DIR" ]; then
    echo "Could not auto-detect Nginx configuration directory."
    read -p "Please enter your Nginx configuration directory path: " NGINX_CONF_DIR
fi

# Determine target directory (servers or sites-enabled)
TARGET_DIR=""
if [ -d "$NGINX_CONF_DIR/servers" ]; then
    TARGET_DIR="$NGINX_CONF_DIR/servers"
elif [ -d "$NGINX_CONF_DIR/sites-enabled" ]; then
    TARGET_DIR="$NGINX_CONF_DIR/sites-enabled"
else
    echo "Could not find 'servers' or 'sites-enabled' directory in $NGINX_CONF_DIR"
    read -p "Please enter the directory where you want to link the config: " TARGET_DIR
fi

echo "Target directory: $TARGET_DIR"

# Create Symlink
TARGET_FILE="$TARGET_DIR/$LINK_NAME"

if [ -L "$TARGET_FILE" ]; then
    echo "Removing existing symlink..."
    rm "$TARGET_FILE"
fi

echo "Creating symlink..."
echo "ln -s \"$CONFIG_SOURCE\" \"$TARGET_FILE\""

# Check if we have write permission, otherwise use sudo
if [ -w "$TARGET_DIR" ]; then
    ln -s "$CONFIG_SOURCE" "$TARGET_FILE"
else
    echo "Need sudo permissions to write to $TARGET_DIR"
    sudo ln -s "$CONFIG_SOURCE" "$TARGET_FILE"
fi

if [ $? -eq 0 ]; then
    echo "✅ Successfully linked configuration file."
else
    echo "❌ Failed to link configuration file."
    exit 1
fi

# Test Configuration
echo "Testing Nginx configuration..."
if command -v nginx &> /dev/null; then
    sudo nginx -t
    if [ $? -eq 0 ]; then
        echo "✅ Configuration is valid."
        echo "Reloading Nginx..."
        sudo nginx -s reload
        echo "🚀 Nginx reloaded! Your app should be available at http://myschool.test"
    else
        echo "❌ Configuration test failed. Please check the error messages above."
    fi
else
    echo "⚠️ 'nginx' command not found in PATH. Please reload Nginx manually."
fi
