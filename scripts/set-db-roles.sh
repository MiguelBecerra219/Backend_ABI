#!/bin/bash
set -e  # Detener el script si algo falla

# Cargar variables del archivo .env
export $(grep -v '^#' .env | xargs)

# Paso 1: Ejecutar migraciones
echo "==> Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Paso 2: Leer y procesar el archivo SQL
echo "==> Creando roles..."
sql_content=$(cat database/sql/roles.sql)

# Reemplazar placeholders
sql_content=${sql_content//'{{DB_DATABASE}}'/$DB_DATABASE}
sql_content=${sql_content//'{{DB_STUDENT_PASS}}'/$DB_STUDENT_PASS}
sql_content=${sql_content//'{{DB_PROFESSOR_PASS}}'/$DB_PROFESSOR_PASS}
sql_content=${sql_content//'{{DB_RESEARCH_PASS}}'/$DB_RESEARCH_PASS}

# Guardar temporalmente
temp_file="temp_roles.sql"
echo "$sql_content" > "$temp_file"

# Ejecutar con MySQL
echo "Ejecutando: mysql -u$DB_USERNAME -p[password] -h$DB_HOST -P$DB_PORT $DB_DATABASE < $temp_file"
mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" -h"$DB_HOST" -P"$DB_PORT" "$DB_DATABASE" < "$temp_file"

# Limpiar archivo temporal
rm -f "$temp_file"

echo "==> Base de datos inicializada correctamente 🎉"
