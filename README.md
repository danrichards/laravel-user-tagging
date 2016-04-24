Laravel User Tagging
============

[![Latest Stable Version](https://poser.pugx.org/dan/laravel-user-tagging/v/stable.svg)](https://packagist.org/packages/dan/laravel-user-tagging)
[![Total Downloads](https://poser.pugx.org/dan/laravel-user-tagging/downloads.svg)](https://packagist.org/packages/dan/laravel-user-tagging)
[![License](https://poser.pugx.org/dan/laravel-user-tagging/license.svg)](https://packagist.org/packages/dan/laravel-user-tagging)
[![Build Status](https://travis-ci.org/dan/laravel-user-tagging.svg?branch=master)](https://travis-ci.org/dan/laravel-user-tagging)

This library provides a scalable method for tracking tags that are attached to
a eloquent `Model` by your app's users. This allows for indexing content based
on how your app's users have tagged the content.

If you're looking for something more basic without user tracking or caching,
please see the fantastic [`laravel-tagging`](https://github.com/rtconner/laravel-tagging)
by [rtconner](https://github.com/rtconner). Rob's library served as the base
for building this project.

A [`laravel-repository`](https://github.com/Torann/laravel-repository) package
by [Torann](https://github.com/Torann) is used in this project for its
repositories and caching. Please see his project for additional documentation
on the repositories provided within.


#### Getting Starting

To use this library, you will need at least one repository using Torann's
pattern. This library will encapsulate whatever `Model` your app needs to tag.
I would recommend [setting up](https://github.com/Torann/laravel-repository)
and testing this repository before continuing.

All the repositories included are complete with integration tests. The tests
also serve as documentation!

#### Composer Install
	
```shell
composer require dan/laravel-user-tagging "dev-master"
```

#### Install and then Run the migrations

```php
'providers' => array(
	'Dan\Tagging\Providers\TaggingServiceProvider',
);
```
```bash
php artisan vendor:publish --provider="Dan\Tagging\Providers\TaggingServiceProvider"
php artisan migrate
```

After these two steps are done, you can edit config/tagging.php with your
preferred settings. You must provide at least one taggable interface in the
config.

```php
// Taggable Interfaces for Abstract Taggable Repositories
'taggable_interfaces' => [
    '\App\Models\Post' => '\App\Repositories\Posts\PostsInterface'
],
```
#### Quick Sample Usage

```php

```

[More examples in the documentation](docs/usage-examples.md)

### Configure

[See config/tagging.php](config/tagging.php) for configuration options.

#### Credits

 - Dan Richards - http://danrichards.net
 - Robert Conner - http://smartersoftware.net
 - Daniel Stainback - http://lyften.com
