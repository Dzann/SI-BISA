#!/usr/bin/env pwsh
# Skrip untuk menjalankan PHPUnit test SI-BISA
# Gunakan: .\run-tests.ps1 [opsi artisan test]
# Contoh:  .\run-tests.ps1 --filter AuthTest
#          .\run-tests.ps1 --testsuite Unit
#          .\run-tests.ps1 --coverage-text

$php = "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"

Write-Host "`n🧪 Menjalankan PHPUnit Test SI-BISA..." -ForegroundColor Cyan
Write-Host "   Database : SQLite in-memory (.env.testing)" -ForegroundColor Gray
Write-Host "   PHP      : $php`n" -ForegroundColor Gray

& $php artisan test @args
