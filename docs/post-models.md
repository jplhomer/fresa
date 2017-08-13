# Post Models

Post Models are the bread and butter of Fresa. They allow you to take an ordinary WordPress post type and treat it like a first-class object-oriented citizen.

To use Fresa's Post Model interface, create a new sublass of `Fresa\PostModel`. For this example, we will assume you are building an events plugin.

    use Fresa\PostModel;

    class Event extends Post Model
    {
        // Optionally define the post type
        public static $postType = 'event';
    }

Once your subclass is defined, you can begin interacting with the existing WordPress data model right away.

If you don't define a custom post type, Fresa assumes the default post type of `post`.

## Accessing Default Properties

The WordPress post object is accessible in a more convenient form:

    $event->title;   // post_title
    $event->content; // post_content
    $event->date;    // post_date
    $event->author;  // post_author

### Available Default Post Properties

A good rule of thumb is to access WordPress properties without the `post_` prefix.

WordPress Property | Fresa Equivalent
--------|-------
`ID` | `id`
`post_title` | `title`
`post_content` | `content`
`post_author` | `author`
`post_date` | `date`
`post_status` | `status`

## Instantiating Models

Models can be instantiated like standard PHP classes, or you can pass an array of initial arguments to populate the instance:

    // Instantiate with no arguments
    $event = new Event;
    $event->title = "My fun event";

    // Instantiate with initial arguments
    $event = new Event([
        'title' => "My fun event"
    ]);
    $event->title; // "My fun event"

## Saving Models

Custom models can be instantiated and built without being persisted to the database right away:

    $event = new Event;
    $event->title = "My fun event";
    $event->content = "Come have fun with us!";

    $event->title; // "My fun event"
    $event->id; // 0

When you are ready to persist your model to the database, call the `save()` method on the model:

    $event->save();
    $event->id; // 123

You can instantiate and persist a model in one easy step using the static `create()` on your model:

    $event = Event::create([
        'title' => "My fun event"
    ]);

    $event->title; // "My fun event"
    $event->id; // 123

### Post Status

The default post status for new models is `publish`. You can change the default post status by overriding the `getDefaultStatus()` method on your model:

    public function getDefaultStatus()
    {
        return 'draft';
    }

## Deleting Models

This feature has not yet been added to Fresa. In the meantime, you can use the existing WordPress function `wp_delete_post`:

    wp_delete_post($event->id);

## Custom Post Type Registration

In order to interface with a custom post type in the WordPress admin interface, you need to register it. With Fresa, you can register a custom post type with a single line of code:

    Event::register();

When using the `register()` method, Fresa requires you to have a custom `$postType` variable set.

By default, Fresa will use the name of the class as the post type label. If you'd like to customize the post type label, which is used throughout the WordPress admin, set `$postTypeName` to a string:

    class Event extends PostModel
    {
        $postType = 'event';
        $postTypeName = 'Company Event';
    }

You can override any of the options or labels registered with the post type as an argument to the method:

    Event::register([
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_nav_menus' => false,
    ]);
