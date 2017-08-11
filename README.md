# Fresa

**🍓 Developing WordPress should be sweet.**

Use Fresa in your plugins and themes to make interacting with the WordPress ecosystem friendly and fast.

Tested on **WordPress 4.8** and requires **PHP 7+**. Your mileage may vary.

```php
use Fresa\PostModel;

class Event extends PostModel
{
	$postType = 'my_custom_post_type';
	$keys = ['start'];
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
$event->start = new \DateTime;
$event->save();

echo $event->id; // 1
```

And perform queries through a fluent interface:

```php
$event = Event::find(1);
$events = Event::where('start', '>=', new \DateTime)
				->order('start', 'asc')
				->limit(5)
				->offset(5)
				->get();

// Queries return an instance of \Illuminate\Support\Collection
$events->each(function($event) {
	echo $event->title;
});
```

## Installation

```sh
composer require jplhomer/fresa
```
