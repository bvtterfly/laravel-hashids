🚨 THIS PACKAGE HAS BEEN ABANDONED 🚨

I no longer use Laravel and cannot justify the time needed to maintain this package. That's why I have chosen to abandon it. Feel free to fork my code and maintain your own copy.


# Laravel Hashids

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bvtterfly/laravel-hashids.svg?style=flat-square)](https://packagist.org/packages/bvtterfly/laravel-hashids)
[![run-tests](https://github.com/bvtterfly/laravel-hashids/actions/workflows/run-tests.yml/badge.svg)](https://github.com/bvtterfly/laravel-hashids/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/bvtterfly/laravel-hashids/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/bvtterfly/laravel-hashids/actions/workflows/php-cs-fixer.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/bvtterfly/laravel-hashids.svg?style=flat-square)](https://packagist.org/packages/bvtterfly/laravel-hashids)

This package provides a trait that will generate hashids when saving any Eloquent model.
 
## Hashids

[Hashids](https://github.com/vinkla/hashids) is a small package to generate YouTube-like IDs from numbers. It converts numbers like `347` into strings like `yr8`.

## Installation

You can install the package via composer:

```bash
composer require bvtterfly/laravel-hashids
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="hashids-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Salt
    |--------------------------------------------------------------------------
    |
    | This is the salt that uses by Hashids package to generate unique id.
    |
    */
    'salt' => config('app.name')
];
```

## Usage

Your Eloquent models should have the ` Bvtterfly\LaravelHashids\HasHashId` trait that contains an abstract `getHashIdOptions` method that you must implement yourself, and it should return the `Bvtterfly\LaravelHashids\HashIdOptions` class.

Your models' migrations should have a field to save the generated hashid to.

Here's an example of what a model would look like:

```php
namespace App\Models;

use Bvtterfly\LaravelHashids\HasHashId;
use Bvtterfly\LaravelHashids\HashIdOptions;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasHashId;

    public function getHashIdOptions(): HashIdOptions
    {
        return HashIdOptions::create()->saveHashIdTo('hashid');
    }

}
```
> By default, Package will generate hashids from models' `id`.

And Its migration: 

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hashid')->nullable(); // Field name same as your `saveHashIdTo`
            //...
            $table->timestamps();
        });
    }
}
```
> The `hashid` column is generated from the `id` field, But `id` is an auto-increment column and doesn't have value before saving in the DB. So, The `hashid` column must be nullable. And The Package will generate `hashid` and update the model after being saved in the database.

### Generate from Hex numbers

If you want to generate hashids from hex numbers like [Mongo](https://www.mongodb.com)'s ObjectIds, you can change the type to the `hex`:

```php
public function getHashIdOptions(): HashIdOptions
{
    return HashIdOptions::create()
        ->saveHashIdTo('hashid')
        ->setType('hex') // default = int
    ;
}
```

### Generate from another field
```php
public function getHashIdOptions(): HashIdOptions
{
    return HashIdOptions::create()
        ->saveHashIdTo('hashid')
        ->generateHashIdFrom('custom_key')

    ;
}
```

### Generate from field with value

By default, This package will generate hashids and update the model from the auto-incremented `id` column after being saved in the database. Still, if your field has value, you can change it to generate hashids while saving:

```php
public function getHashIdOptions(): HashIdOptions
{
    return HashIdOptions::create()
        ->saveHashIdTo('hashid')
        ->setAutoGeneratedField(false)
    ;
}
```

### Use the same hashids among models

The package will add the models' table to the default salt to generate a unique output id per model. If you want your `Post` and `User` models to share the same output id when `id = 1`:

```php
public function getHashIdOptions(): HashIdOptions
{
    return HashIdOptions::create()
        ->saveHashIdTo('hashid')
        ->setGenerateUniqueHashIds(false)
    ;
}
```

### Use padding to make your output ids longer

Without padding, encoding of `1` returns something like `jR`, but You can use padding to have a longer output id.

> Note that output ids are only padded to fit **at least** a certain length. It doesn't mean that they will be *exactly* that length.


```php
public function getHashIdOptions(): HashIdOptions
{
    return HashIdOptions::create()
        ->saveHashIdTo('hashid')
        ->setMinimumHashLength(10)
    ;
}
```

### Using a custom alphabet
```php
public function getHashIdOptions(): HashIdOptions
{
    return HashIdOptions::create()
        ->saveHashIdTo('hashid')
        // use all lowercase alphabet instead of 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ->setAlphabet('abcdefghijklmnopqrstuvwxyz') 
    ;
}
```

### Using Hashids in routes

To use the hashids in routes, you may specify the hashid column in the route parameter definition:

```php
use App\Models\Post;
 
Route::get('/posts/{post:hashid}', function (Post $post) {
    return $post;
});
```
or If you would like model binding to always use the hashid column other than id when retrieving a given model class, you may override the getRouteKeyName method on the Eloquent model:

```php
/**
 * Get the route key for the model.
 *
 * @return string
 */
public function getRouteKeyName()
{
    return 'hashid';
}
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ARI](https://github.com/bvtterfly)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
