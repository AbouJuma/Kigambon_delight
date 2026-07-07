@echo off
echo Starting parallel sync for large tables...
echo.

start "Sync Sales" cmd /k "php artisan db:sync-online --table=sales --limit=500"
timeout /t 2 /nobreak

start "Sync Sale Details" cmd /k "php artisan db:sync-online --table=sale_details --limit=500"
timeout /t 2 /nobreak

start "Sync Purchase Details" cmd /k "php artisan db:sync-online --table=purchase_details --limit=500"
timeout /t 2 /nobreak

start "Sync Products" cmd /k "php artisan db:sync-online --table=products --limit=500"
timeout /t 2 /nobreak

start "Sync Product Warehouse" cmd /k "php artisan db:sync-online --table=product_warehouse --limit=500"

echo All sync windows launched. Close this window to continue.
pause
