<?php

namespace Fresa\Relations;

use Fresa\Relation;

/**
 * HasTaxonomy relationship
 * E.g. Post HasMany Categories.
 */
class HasTaxonomy extends Relation
{
    /**
     * Hydrate existing terms in the relationship.
     *
     * @return self
     */
    public function hydrate()
    {
        $terms = wp_get_object_terms(
            $this->parent->id,
            $this->related->getTaxonomy()
        );

        collect($terms)->each(function ($term) {
            $this->objects->push(
                $this->related->newFromObject($term)
            );
        });

        return $this;
    }

    /**
     * Save all objects in relationship to the DB.
     *
     * @param \Fresa\Taxonomy $term Optional term to be passed in before save
     *
     * @return self
     */
    public function save($term)
    {
        if (!$term->exists) {
            throw new \Exception('Term must exist in the database before it can be added to a relationship.');
        }

        // Reset collection each time to remove duplicates
        $this->objects = $this->objects->push($term)->unique('id');

        wp_set_object_terms(
            $this->parent->id,
            $this->objects->map->id->toArray(),
            $this->related->getTaxonomy()
        );

        return $this;
    }
}
