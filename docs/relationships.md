# Relationships

In WordPress, post types can have many taxonomies, and taxonomies can belong to many post types.

In Fresa, these two relationship types are defined as `HasTaxonomy` and `BelongsToPost`.

## `HasTaxonomy` Relationships

In order to allow a custom Post Model instance to interact with a Taxonomy, add a method to your model:

    class Event extends PostModel
    {
        public function categories()
        {
            return $this->hasTaxonomy(EventCategory::class);
        }
    }

This allows you to attach taxonomy terms to specific post models, and vice versa:

    $category = EventCategory::find(123);
    $event->categories()->save($category);

Taxonomy terms will be available as a dynamic property on your post model object using the same name as the method you defined:

    foreach ($event->categories as $category) {
        echo $category->name;
    }

## `BelongsToPost` Relationships

When working with a taxonomy, you may find it convenient to be able to filter posts of a given taxonomy term.

Add a method to your taxonomy to create that relationship:

    class EventCategory extends Taxonomy
    {
        public function events() {
            return $this->belongsToPost(Event::class);
        }
    }

Then you can use the name of the method you created to attach a given category to a post model:

    $category = EventCategory::create([
        'name' => 'Fiesta!',
    ]);

    $event = Event::create([
        'title' => 'Strawberry Party',
    ]);

    $category->events()->save($event);

You can use the name of the method as a dynamic property get a collection of related models:

    $category->events->first()->title; // 'Strawberry Party'

## WordPress Requirements

When creating relationships and saving related models, both `PostModel` and `Taxonomy` instances need to have been registered with WordPress as valid custom types. This can be done with the `register` method or by using the traditional registration method.
