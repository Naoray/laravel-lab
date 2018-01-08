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
git clone git@github.com:Naoray/laravel-lab.git
cd laravel-lab && composer install
cp .env.example .env
php artisan key:generate
```

## Usage
### Creating a new package
*To see all arguments/options available for make:package just type `php artisan package:make -h`*
```
php artisan make:package
```

![make:package command](https://user-images.githubusercontent.com/10154100/34663012-acc1e984-f454-11e7-9d9a-439c1080dddf.png)

### Adding a package
```
php artisan package:add
```

![package:add command](https://user-images.githubusercontent.com/10154100/34663069-0b983e2c-f455-11e7-9678-ffd1660aa055.png)

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