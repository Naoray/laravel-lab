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
php artisan make:package
```

![make:package command](https://user-images.githubusercontent.com/10154100/38869421-f4e4c602-424a-11e8-8f52-1247ed0a661e.png)

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

![make:package command](https://user-images.githubusercontent.com/10154100/38869421-f4e4c602-424a-11e8-8f52-1247ed0a661e.png)

### Adding a package
```
php artisan package:add
```

![package:add command](https://user-images.githubusercontent.com/10154100/38869340-ad224876-424a-11e8-82dc-d2f7eabdfc9a.png)

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