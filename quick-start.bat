@echo off
REM Barter Bay Quick Start Script for Windows
REM This script helps you get started quickly

echo ========================================
echo Barter Bay - Quick Start Setup
echo ========================================
echo.

REM Check PHP installation
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP is not installed or not in PATH
    echo Please install PHP 7.4 or higher
    pause
    exit /b 1
)

echo [OK] PHP found
php -v | findstr /C:"PHP"
echo.

REM Create .env file if it doesn't exist
if not exist .env (
    echo Creating .env file...
    copy .env.example .env >nul
    echo [OK] .env file created
    echo [WARNING] Please edit .env and add your Razorpay credentials
) else (
    echo [OK] .env file already exists
)

echo.
echo Starting PHP development server...
echo.
echo Server will be available at: http://localhost:8000
echo.
echo IMPORTANT NEXT STEPS:
echo 1. Visit http://localhost:8000/setup_admin.php to create admin accounts
echo 2. DELETE setup_admin.php after running it
echo 3. Change default admin passwords
echo 4. Update .env with your Razorpay credentials
echo.
echo Press Ctrl+C to stop the server
echo.

REM Start PHP server
php -S localhost:8000
