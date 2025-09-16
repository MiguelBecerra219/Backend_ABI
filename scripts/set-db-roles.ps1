# Cargar variables del archivo .env
$envContent = Get-Content .env -Raw | ConvertFrom-StringData

# Paso 1: Ejecutar migraciones
Write-Host "==> Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Paso 2: Leer y procesar el archivo SQL
Write-Host "==> Creando roles..."
$sqlContent = Get-Content "database/sql/roles.sql" -Raw

# Reemplazar placeholders
$sqlContent = $sqlContent -replace "{{DB_USER_PASS}}", $envContent.DB_USER_PASS
$sqlContent = $sqlContent -replace "{{DB_DATABASE}}", $envContent.DB_DATABASE
$sqlContent = $sqlContent -replace "{{DB_STUDENT_PASS}}", $envContent.DB_STUDENT_PASS
$sqlContent = $sqlContent -replace "{{DB_PROFESSOR_PASS}}", $envContent.DB_PROFESSOR_PASS
$sqlContent = $sqlContent -replace "{{DB_RESEARCH_PASS}}", $envContent.DB_RESEARCH_PASS

# Guardar temporalmente
$tempFile = "temp_roles.sql"
$sqlContent | Out-File $tempFile -Encoding UTF8

# Ejecutar con MySQL
$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$mysqlArgs = "-u$($envContent.DB_USERNAME) -p$($envContent.DB_PASSWORD) -h$($envContent.DB_HOST) -P$($envContent.DB_PORT) $($envContent.DB_DATABASE) -e `"source $tempFile`""

Write-Host "Ejecutando: $mysqlPath $mysqlArgs"
Start-Process -FilePath $mysqlPath -ArgumentList $mysqlArgs -Wait -NoNewWindow

# Limpiar archivo temporal
Remove-Item $tempFile -ErrorAction SilentlyContinue

Write-Host "==> Base de datos inicializada correctamente 🎉"