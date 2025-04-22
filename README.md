<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax.

## BaseSSO - Sistema de Autenticación LDAP

## Descripción
BaseSSO es un sistema de autenticación basado en Laravel que proporciona integración con Windows Active Directory (LDAP) a través de una API RESTful. El sistema permite la autenticación de usuarios y la verificación del estado de autenticación mediante tokens.

## Estructura del Proyecto

### Componentes Principales

1. **LdapAuthController**
   - Maneja las solicitudes de autenticación
   - Implementa los endpoints de la API
   - Gestiona la respuesta de éxito/error

2. **LdapService**
   - Encapsula la lógica de conexión LDAP
   - Maneja la autenticación contra el servidor LDAP
   - Recupera información del usuario

3. **LdapUser**
   - Modelo que representa un usuario LDAP
   - Implementa la interfaz Authenticatable
   - Gestiona el almacenamiento de tokens en caché

### Flujo de Autenticación
1. El usuario envía credenciales (username/password)
2. El sistema valida las credenciales contra el servidor LDAP
3. Si es exitoso, genera un token de API
4. El token se almacena en caché para futuras validaciones

## Configuración

1. Configure su archivo `.env` con los datos de conexión LDAP:
```env
LDAP_HOST=10.128.225.9
LDAP_PORT=389
LDAP_USERNAME=usuario@dominio.com
LDAP_PASSWORD=contraseña
LDAP_BASE_DN="DC=dominio,DC=com"
LDAP_SSL=false
LDAP_TLS=false
```

## API Endpoints

### 1. Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "username": "usuario",
    "password": "contraseña"
}
```

Respuesta exitosa:
```json
{
    "status": "success",
    "message": "User authenticated successfully",
    "user": {
        "token": "api_token_hash",
        "user": {
            "username": "usuario",
            "email": "usuario@dominio.com",
            "domain": "dominio.com"
        }
    }
}
```

### 2. Verificar Autenticación
```http
GET /api/v1/check
Authorization: Bearer api_token_hash
```

Respuesta exitosa:
```json
{
    "status": "success",
    "authenticated": true,
    "user": {
        "username": "usuario",
        "email": "usuario@dominio.com",
        "domain": "dominio.com"
    }
}
```

## Características

- Autenticación contra Active Directory
- Generación y validación de tokens de API
- Almacenamiento en caché de sesiones
- Manejo de múltiples formatos de usuario (UPN, NetBIOS, DN)
- Respuestas JSON estandarizadas
- Validación de solicitudes mediante Form Requests
- Transformación de datos mediante Resources

## Seguridad

- Tokens de API únicos por sesión
- Almacenamiento seguro en caché
- Validación de credenciales LDAP
- Headers de seguridad estándar
- Protección CSRF para rutas web

## Requisitos

- PHP 8.2+
- Laravel 12.0
- Extensión PHP LDAP
- Servidor LDAP/Active Directory accesible We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
"# SSOBase" 
