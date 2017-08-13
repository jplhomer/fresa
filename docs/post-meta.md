# Post Meta

With Fresa, post meta is a first-class citizen of the Post Model object.

    use Fresa\PostModel;

    class Event extends PostModel {}

Assign meta values as properties to the Post Model instance:

    $event = new Event;
    $event->venue = 'Central Park';
    $event->venue; // 'Central Park'

Post meta values are automatically persisted to the record upon save:

    $event->save();
    $event->id; // 123

    $sameEvent = Event::find(123);
    $sameEvent->venue; // 'Central Park'

This is equivalent to using the standard `get_post_meta` method:

    get_post_meta(123, 'Venue', true); // 'Central Park'

## Casting Meta Values

Fresa supports casting meta values to certain types. Currently, `date` and `boolean` are supported.

Use the `$casts` property to instruct Fresa to cast your meta values:

    class Event extends PostModel
    {
        protected $casts = [
            'start' => 'date',
            'end' => 'date',
            'recurring' => 'boolean',
        ];
    }

This allows you to interact with casted meta values as expected:

    $event->recurring = 1;
    $event->start = 'today';
    $event->end = 'tomorrow';

    $event->recurring; // true
    $event->start; // instance of Carbon\Carbon;
    $event->end; // instance of Carbon\Carbon;

Casted values persist to the database in a safe manner, meaning date values stored in the database are converted to date time strings, so they can be used in WordPress queries:

    $event->save();
    $event->id; // 123

    get_post_meta(123, 'start', true); // 2017-01-01 00:00:00
