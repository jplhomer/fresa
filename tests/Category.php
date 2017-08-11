<?php

/**
 * Category taxonomy.
 */
class Category extends \Fresa\Taxonomy
{
    /**
     * The taxonomy slug for this class.
     *
     * @var string
     */
    protected $taxonomy = 'my_category';

    /**
     * Define the relationship between Categories and Events.
     *
     * @return Relation
     */
    public function events()
    {
        return $this->belongsToPost(Event::class);
    }
}
