# Querying Post Models

Since Fresa is a wrapper around the existing WordPress Post data model, querying objects might seem familiar.

## Fetching a Single Model

To fetch an existing model from the database, use the static `find()` method on your model and pass it the ID:

    $event = Event::find(123);

    $event->title; // "My fun event";

## Querying Models

The `Fresa\Query` object acts as a wrapper around the native `WP_Query`. It is conveniently available on your model using various query methods.

When you query models, a `Collection` will be returned. Collections are instances of `Illuminate\Support\Collection` and have many helpful methods to iterate and access elements in a fluent way. See the [Laravel Collections documentation](https://laravel.com/docs/master/collections) for all the helper methods available to you.

### The `where` Method

Find models that match a certain meta value criteria using the `where` method:

    $events = Event::where('venue', 'Central Park')->get();

This single line of code is equivalent to a **typical WordPress query**:

    // Fetch results
    $results = get_posts([
        'post_type' => 'event',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'venue',
                'value' => 'Central Park',
            ],
        ],
    ]);

    // Instantiate objects
    foreach ($results as $result) {
        $events[] = new Event([
            'title' => $result->post_title,
            // etc
        ]);
    }

    // Create collection
    $events = collect($events);

The `where` method accepts three arguments, or two if you're checking for equivalency:

    Event::where('venue', 'Central Park');
    // is the same as
    Event::where('venue', '=', 'Central Park');

Standard comparators from the [WordPress Meta Query docs](https://codex.wordpress.org/Class_Reference/WP_Meta_Query) can be used:

    Event::where('start', '>=', '2017-01-01 00:00:00');

> **Note:** Queries made through `where` are currently limited to meta keys and values.

> **Note:** There is currently no support for the `type` argument for meta queries.

> **Note:** The `relation` argument for multiple meta queries is set to `AND` and currently cannot be changed.

### Query Modifiers

Queries results can be ordered using the `order` method:

    $events = Event::order('start', 'asc')->get();

The line above is equivalent to:

    $events = get_posts([
        'post_type' => 'event',
        'orderby' => 'meta_value',
        'meta_key' => 'start',
        'order' => 'asc',
    ]);

The `order` method accepts both [standard WordPress order arguments](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) in addition to meta keys. It will structure the query intelligently, so you don't have to specify.

Queries can also be paginated with `limit` and `offset`:

    $events = Event::limit(5)
                   ->offset(5)
                   ->get();

## Getting All Models

Use the `all` method to return all the models in the database.

    $events = Event::all();
