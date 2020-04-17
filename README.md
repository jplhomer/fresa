# Fresa

**🍓 Developing WordPress should be sweet.**

Use [Fresa](https://fresa.jplhomer.org) in your plugins and themes to make interacting with the WordPress ecosystem friendly and fast.

Tested on **WordPress 4.8** and requires **PHP 7+**. Your mileage may vary.

```php
use Fresa\PostModel;

class Event extends PostModel
{
    $postType = 'my_custom_post_type';
}
```

Register your custom post types in one line:

```php
Event::register();
```

Interact with your post in an object-oriented fashion:

```php
$event = new Event;
$event->title = 'Hello World.';
$event->venue = 'Times Square';
$event->save();

echo $event->id; // 1
echo $event->venue; // 'Times Square';
// Same as get_post_meta(1, 'venue', true);
```

And perform queries through a fluent interface:

```php
$event = Event::find(1);
$events = Event::where('venue', 'Times Square')
                ->order('date', 'asc')
                ->limit(5)
                ->offset(5)
                ->get();
```

Queries return a [Collection instance](https://laravel.com/docs/5.4/collections):

```php
$events->each(function($event) {
    echo $event->title;
});
```

## Installation

```sh
composer require jplhomer/fresa
```

**[Read the full documentation here](https://fresa.jplhomer.org)**.

## Development

```sh
composer install
```

### Running Tests

First, set up the required WordPress testing library on your machine:

```bash
# Replace db-name, db-user, db-pass with arguments of a database to be created
# If you already have a database, pass `true` to the last argument, e.g.
# wptests root '' 127.0.0.1 latest true
./bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]
```

Run tests using PHPUnit:

```bash
vendor/bin/phpunit
```
