#!/bin/bash

# Detener todos los contenedores
docker-compose down

# Reconstruir los contenedores
docker-compose build

# Iniciar los contenedores
docker-compose up -d

# Instalar dependencias
docker-compose exec app composer install

# Generar key
docker-compose exec app php artisan key:generate

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Mostrar estado
docker-compose ps
