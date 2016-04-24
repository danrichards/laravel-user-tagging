Events
============

The `Taggable` trait will fire off two events.

```php
Dan\Tagging\Events\Tagged;

Dan\Tagging\Events\TaggedByUser;
```

You can add listeners and track these events.

```php
\Event::listen(Dan\Tagging\Events\Tagged::class, function($article) {
    \Log::debug($article->title . ' was tagged');
});
```