# Taxonomy Models

Fresa ships with a base model called `Taxonomy`. Think of native WordPress categories or tags as examples of taxonomies.

## Define a Taxonomy

Define a taxonomy by extending the base class:

    use Fresa\Taxonomy;

    class EventCategory extends Taxonomy
    {
    }

You can then interact with the taxonomy in an object-oriented fashion:

    $category = new EventCategory;
    $category->name = 'Celebrations';
    $category->save();

## Defining Taxonomy Slugs

By default, the snake-case version of the class name will be used as a slug to store the taxonomy terms in WordPress.

    $e = new EventCategory;
    $e->getTaxonomy(); // 'event_category'

If you want to customize the taxonomy slug, set the `$taxonomy` variable on the class:


    use Fresa\Taxonomy;

    class EventCategory extends Taxonomy
    {
        protected $taxonomy = 'my_event_category';
    }

## Required Attributes

By default, `Taxonomy` requires that a `name` be defined on the model before it can be saved.

    EventCategory::create(); // Throws Exception
    EventCategory::create(['name' => "Fun Parties"]); // Success!

You can modify or extend the required attributes on your model using the `$required` property:

    use Fresa\Taxonomy;

    class EventCategory extends Taxonomy
    {
        protected $required = [
            'name',
            'description',
        ];
    }

## Querying Taxonomy Terms

You can also perform basic queries of taxonomy terms:

    $categories = EventCategory::where('slug', 'celebrations');

Note that the query is limited to a simple **key/value equality check.**

You can also fetch all terms in a given taxonomy:

    $categories = EventCategory::all();

Use the `find` method to retrieve a single taxonomy term:

    $category = EventCategory::find(123);

> **Note**: Taxonomy term meta is not yet enabled via Fresa.

## Deleting Taxonomy Models

To delete taxonomy models, use the `delete` method:

    $category->delete();

## Taxonomy Registration

In order to interface with a taxonomy in the WordPress admin interface, you need to register the taxonomy.

Fresa allows you to register the taxonomy with one line of code:

    EventCategory::register([Event::class]);

The register method requires one argument, which is an array of `PostModel` class names. Fresa does the hard work of connecting the two models (post and taxonomy) so your custom taxonomy is nested in the correct spot in the admin interface.

You can also pass customizations as the second argument to override any setting in the taxonomy registration process:

    EventCategory::register([Event::class], [
        'hierarchical' => true, // behave as category, not tag
        'show_admin_column' => true,
    ]);
