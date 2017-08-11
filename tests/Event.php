<?php

use Carbon\Carbon;
use Fresa\PostModel;

/**
 * Event model.
 */
class Event extends PostModel
{
    /**
     * Cast start and end attributes to Carbon date objects.
     *
     * @var array
     */
    protected $casts = [
        'start'        => 'date',
        'end'          => 'date',
        'all_day'      => 'boolean',
        'recurring'    => 'boolean',
        'repeat_until' => 'date',
    ];

    /**
     * Protected keys for this model.
     *
     * @var array
     */
    protected $required = [
        'title',
        'start',
        'end',
    ];

    /**
     * The custom post type with which to store the events.
     *
     * @var string
     */
    public static $postType = 'event';

    /**
     * Define a relationship to Category.
     *
     * @return Relation
     */
    public function categories()
    {
        return $this->hasTaxonomy(Category::class);
    }
}
