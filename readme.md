# Grid Principles' File API
A common way to hook into Grid Principles' [File Server](https://bitbucket.org/gp_greg/file-server).

## Installation
- Add to `composer.json`
- Run `composer update`
- Add provider and facade to `config/app.php` **before** RouteServiceProvider
- Publish migrations / config / views using `php artisan vendor:publish`
- Run migration `php artisan migrate`
- Add credentials to `.env`

## Usage
- Import view into your Blade file `@include('file::upload')`