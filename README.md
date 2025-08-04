Blank Project For Laravel
=======================

A Laravel starter project with essential features pre-configured.

## Features

- **Frontend**: Uses Vite and Vue.js for a modern frontend experience
- **Database**: SQLite for local development (with remote SQLite support)
- **Authentication**: Laravel's built-in authentication system
- **No Inertia.js**: Uses traditional Laravel Blade templates with Vue components

## Remote SQLite Support

This project includes support for connecting to remote SQLite databases. This can be useful for:
- Accessing shared database files on network storage
- Working with SQLite databases on mounted remote filesystems
- Development and testing scenarios where a remote SQLite file is needed

For detailed instructions on configuring and using remote SQLite databases, see the [Remote SQLite Documentation](docs/remote-sqlite.md).

## Getting Started

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env` and configure as needed
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `npm run dev` for development or `npm run build` for production

## License

This project is open-sourced software licensed under the MIT license.
