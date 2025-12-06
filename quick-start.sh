#!/bin/bash

# Barter Bay Quick Start Script
# This script helps you get started quickly

echo "🛒 Barter Bay - Quick Start Setup"
echo "=================================="
echo ""

# Check PHP installation
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
fi

echo "✅ PHP found: $(php -v | head -n 1)"

# Check SQLite extension
if ! php -m | grep -q sqlite3; then
    echo "❌ SQLite3 extension not found. Please enable it."
    exit 1
fi

echo "✅ SQLite3 extension found"

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo ""
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created"
    echo "⚠️  Please edit .env and add your Razorpay credentials"
else
    echo "✅ .env file already exists"
fi

# Set permissions
echo ""
echo "🔒 Setting file permissions..."
chmod 755 .
chmod 644 *.php
chmod 755 images/
echo "✅ Permissions set"

# Check if database exists
if [ ! -f barter_bay.db ]; then
    echo ""
    echo "📊 Database will be created on first run"
else
    echo "✅ Database found"
fi

echo ""
echo "🚀 Starting PHP development server..."
echo ""
echo "📍 Server will be available at: http://localhost:8000"
echo ""
echo "⚠️  IMPORTANT NEXT STEPS:"
echo "1. Visit http://localhost:8000/setup_admin.php to create admin accounts"
echo "2. DELETE setup_admin.php after running it"
echo "3. Change default admin passwords"
echo "4. Update .env with your Razorpay credentials"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start PHP server
php -S localhost:8000
