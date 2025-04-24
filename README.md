<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
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

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# ABC Cars - Used Car Sales Portal

A comprehensive platform for buying and selling used cars, with separate user and administrator roles.

## Features

### User Functionalities
- Register an account
- Securely log in
- Post cars for sale with images
- Deactivate car listings
- Update user profiles
- Schedule test drive appointments
- Submit bidding prices for cars

### Administrator Functionalities
- Register an account
- Securely log in
- View and manage registered users
- Assign administrative roles
- Approve or deactivate car posts
- Approve or reject user appointments based on bids
- Finalize transactions

### Shared Functionalities
- Access homepage, car listings, "About Us," and "Contact Us" pages
- Search cars by Make, Model, Registration Year, and Price Range

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: React 18 with TypeScript
- **UI Framework**: Tailwind CSS with DaisyUI components
- **Authentication**: Laravel Sanctum
- **Admin Dashboard**: Filament
- **Authenticated User Dashboard**: Filament
- **Database**: MySQL
- **State Management**: Inertia.js

## Setup Instructions

1. Clone the repository
```bash
git clone https://github.com/yourusername/abccars.git
cd abccars
```

2. Install PHP dependencies
```bash
composer install
```

3. Install NPM dependencies
```bash
npm install
```

4. Create and configure environment file
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in the .env file

6. Run migrations and seeders
```bash
php artisan migrate --seed
```

7. Start the development server
```bash
npm run dev
php artisan serve
```

## Default Credentials

### Admin User
- Email: admin@abccars.com
- Password: password

### Regular User
- Email: user@abccars.com
- Password: password

## Color Palette

| Color Role           | Hex Code | DaisyUI Mapping        | Usage                          |
|----------------------|----------|------------------------|--------------------------------|
| Primary (Midnight Teal) | #00494D | primary               | Buttons, links, highlights     |
| Secondary (Amber Gold)  | #FFC857 | secondary             | CTAs, price highlights         |
| Accent (Blush Coral)    | #F16A70 | accent                | Special UI elements, hover effects |
| Neutral (Slate Gray)    | #6E7C7C | neutral               | Backgrounds, inactive states   |
| Base (Soft Ivory)       | #F9F6F1 | base-100              | Main background color          |
| Text Contrast (Jet Black)| #1C1C1C | text-neutral-content  | Headlines, content text        |

## Pages Structure

### Pages Accessible for All Users
- Home Page
- About Us
- Contact Us
- Car Listing Page
- Car Details Page
- Register
- Login

### Pages Accessible for Authenticated Users
- User Dashboard
- Profile Page
- Car Listing Page
- Car Details Page

### Pages Accessible for Administrators
- Admin Dashboard
- Profile Page
- Car Listing Page
- Car Details Page

## License

This project is licensed under the MIT License. See the LICENSE file for details.
