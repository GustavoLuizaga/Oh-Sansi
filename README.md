# 🎯 Sistema de Inscripción para las Olimpiadas Oh! SanSi

<p align="center"><a href="https://tecnocursosedu.com/olimpiadas-osansi-2024/" target="_blank"><img src="https://tecnocursosedu.com/wp-content/uploads/2024/10/ohsansi.jpg" width="400"></a></p>

## 🚀 Descripción del Proyecto

Este sistema permite gestionar el proceso de inscripción de estudiantes a las Olimpiadas Oh! SanSi. Incluye funcionalidades para que los administradores, tutores y estudiantes realicen sus tareas de manera eficiente y organizada.

---

## 🔧 Tecnologías Usadas

- **PHP 7.4.22**  
- **Laravel 8.83.29**  
- **MySQL 5.7**  
- **Apache 2.4.28**  
- **Visual Studio Code**  
- **PowerDesigner** *(para el modelado de base de datos)*  
- **StarUML** *(para diagramas UML)*  
- **GIMP 2.10** *(para edición gráfica)*  

---

## 🔥 Instalación y Configuración

1. **Clonar el repositorio:**
```bash
    git clone https://github.com/KleberVM/sansi-system.git
```

2. **Instalar dependencias (Recomendable estar dentro de la carpeta del proyecto):**
```bash
    composer install
    npm install
```

3. **Configurar el archivo .env:**
```bash
    cp .env.example .env
    php artisan key:generate
```

4. **Configurar la base de datos (Puedes saltarte al paso 5):**
```bash
    php artisan migrate --seed
```

5. **Levantar el servidor local:**
```bash
    php artisan serve
```

---

## Comandos útiles para el proyecto

- Refresca la base de datos (Ten en cuenta que se perderan los datos que estan guardados en la base de datos.)
```bash
php artisan migrate:fresh --seed 
```

- Ejecuta el seeder que tenemos (llena la base de datos con datos escenciales como roles , funciones ,ui).
```bash
php artisan db:seed --class=DefaultDataSeeder 
```

- Para los que tienen php superior a 7.4.22 
```bash
composer remove phpoffice/phpspreadsheet
```

- para actualizar composer
```bash
composer update
```

---

## 🧙‍♂️ Datos de acceso
Cuando ejcutas el seeder tendras al admin por defecto
- **Admin:**
    - **Usuario:** admin
    - **email:** admin@gmail.com
    - **Contraseña:** 12345678

---

## 📝 PHP INI
Extenciones que debes de tener habilitadas
- extension=curl
- extension=fileinfo
- extension=mbstring
- extension=mysqli
- extension=openssl
- extension=pdo_mysql
- extension=zip
- extension_dir = "ext"

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

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

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
