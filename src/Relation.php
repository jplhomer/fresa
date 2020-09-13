<?php

namespace Fresa;

use Illuminate\Support\Collection;

/**
 * Define a relationship between a PostModel and a Taxonomy.
 */
abstract class Relation
{
    /**
     * Store the parent instance.
     *
     * @var \Fresa\Taxonomy
     */
    protected $parent;

    /**
     * Store the related instance.
     *
     * @var object
     */
    protected $related;

    /**
     * Cache of objects in relationship.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $objects;

    /**
     * @param class-string $related
     */
    public function __construct(Model $parent, $related)
    {
        if (!$parent->exists) {
            throw new \Exception('Model must be saved before adding relationships');
        }

        $this->parent = $parent;
        $this->related = new $related();
        $this->objects = new Collection();

        // Initially hydrate the relationship
        if ($this->parent->exists) {
            $this->hydrate();
        }
    }

    /**
     * Get all objects in relationship.
     *
     * @return Collection
     */
    public function get()
    {
        return $this->objects;
    }

    /**
     * Hydrate existing terms in the relationship.
     *
     * @return self
     */
    abstract public function hydrate();

    /**
     * Save all objects in relationship to the DB.
     *
     * @param \Fresa\Taxonomy $item Item to save to the relationship
     *
     * @return self
     */
    abstract public function save($item);
}
