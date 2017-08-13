# Admin Columns

When you register a custom post type with WordPress, often times you'd like to add columns to the index screen within the admin interface.

This typically involves several filters and rendering functions. But with Fresa, it's a single call to the `Columns` helper.

## Registering Custom Columns

Use the `Columns` helper to register custom columns for a defined `PostModel`.

    use Fresa\Admin\Columns;

    Columns::for(Event::class)
            ->column('venue')
            ->column('start', [
                'value' => function($event) {
                    return $event->start->format("g:ia");
                },
            ])
            ->columns('recurring', [
                'label' => 'Recurring Event?',
                'value' => function($event) {
                    return $event->recurring ? 'Yes' : 'No';
                },
            ])
            ->register();

Let's step through each of the features of the `Columns` helper.

### Set the Model: `for`

Pass the class name of your custom model to the helper.

### Add a Column: `column`

The `column` method accepts a slug for the column with additional optional arguments.

The following arguments are accepted:

- `label`: The label for the column. If no label is passed, a title case version of the slug will be used.
- `value`: The value to show for each row in the column. This is passed as a Closure which contains an instance of the model for that given row. If no value function is defined, the helper will attempt to pull the value of that attribute from the model instance.

### Register The Columns: `register`

Since the hook can only be called once, be sure to call `register` at the end of your column registrations.

## Hiding Existing Columns

The `Columns` helper ships with a helpful `hide` method which prevents an existing column from displaying in the admin:

    Columns::for(Event::class)
        ->column('start', [
            // args
        ])
        ->hide('date')
        ->register();

In the example above, the default WordPress date column will not display.
