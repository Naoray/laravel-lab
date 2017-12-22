# Laravel Lab
A default laravel app with a few commands with makes start developing with packages easier.

## Purpose
Developing packages with laravel is easy, but the process of creating the project folders, initializing composer, readme
contribution guide, ... You get the point. This Project serves as a testing environment when developing or adjusting
packages, but also as a quick package stub generator. With one command you can create a new package with
- Ready to use Composer file
- License file (currently only MIT)
- Contribution guidelines
- Default travis config
- Default phpunit.xml file
- Service Provider

... and the package gets directly pulled in via composer repositories.

```
php artisan make:package test-this naoray
// creates a package located in ../packages/naoray/test-this
```

## Install
```
git clone git@github.com:Naoray/lab-app.git
cd lab-app && composer install
cp .env.example .env
php artisan key:generate
```

## Usage
### Creating a new package
*To see all arguments/options available for make:package just type `php artisan package:make -h`*
```
php artisan make:package package-name vendor-name
// creates a package located in ../packages/vendor-name/package-name
```

### Adding a package
```
php artisan package:add package-name path-to-package
```

### Resetting Lab
Useful if you want to work on a complete different project.

**Careful: This command resets your lab to the last state of the master branch**
```
php artisan app:reset
```

## ToDo
- add tests

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security-related issues, please email kkoenig@byte5.de instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.


