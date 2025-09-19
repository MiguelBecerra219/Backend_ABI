#!/bin/bash
set -euo pipefail  # Mejor control de errores

# Cargar variables del .env (maneja espacios y caracteres especiales)
export $(grep -v '^#' .env | xargs -d '\n')

# Paso 1: Ejecutar migraciones y seeders (agregué --seed para consistencia con Windows)
echo "==> Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Paso 2: Procesar el SQL con reemplazos seguros
echo "==> Creando roles..."
temp_file="temp_roles.sql"

# Usamos sed para reemplazar placeholders (maneja caracteres especiales)
sed -e "s|{{DB_USER_PASS}}|$DB_USER_PASS|g" \
    -e "s|{{DB_DATABASE}}|$DB_DATABASE|g" \
    -e "s|{{DB_STUDENT_PASS}}|$DB_STUDENT_PASS|g" \
    -e "s|{{DB_PROFESSOR_PASS}}|$DB_PROFESSOR_PASS|g" \
    -e "s|{{DB_RESEARCH_PASS}}|$DB_RESEARCH_PASS|g" \
    "database/sql/roles.sql" > "$temp_file"

# Ejecutar con MySQL + captura de errores
echo "==> Ejecutando SQL en MySQL..."
if ! mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" -h"$DB_HOST" -P"$DB_PORT" "$DB_DATABASE" < "$temp_file"; then
    echo "❌ Error al ejecutar el archivo SQL. Contenido del archivo temporal:"
    cat "$temp_file"
    exit 1
fi

# Limpiar
rm -f "$temp_file"
echo "==> Base de datos inicializada correctamente 🎉"