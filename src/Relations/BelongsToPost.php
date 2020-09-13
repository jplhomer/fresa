<?php

namespace Fresa\Relations;

use Fresa\Relation;

/**
 * BelongsToPost relationship
 * E.g. Categories belong to Posts.
 */
class BelongsToPost extends Relation
{
    /**
     * Hydrate existing terms in the relationship.
     *
     * @return self
     */
    public function hydrate()
    {
        $posts = get_posts([
            'posts_per_page' => -1,
            'post_type'      => $this->related->getPostType(),
            'tax_query'      => [
                [
                    'taxonomy' => $this->parent->getTaxonomy(),
                    'terms'    => $this->parent->id,
                ],
            ],
        ]);

        collect($posts)->each(function ($post) {
            $this->objects->push(
                $this->related->newFromObject($post)
            );
        });

        return $this;
    }

    /**
     * Save all objects in relationship to the DB.
     *
     * @param \Fresa\PostModel $post
     *
     * @return self
     */
    public function save($post)
    {
        if (!$post->exists) {
            throw new \Exception('Post must exist in the database before it can be added to a relationship.');
        }

        // Reset collection each time to remove duplicates
        $this->objects = $this->objects->push($post)->unique('id');

        $res = wp_set_object_terms(
            $post->id,
            $this->parent->id,
            $this->parent->getTaxonomy(),
            true // Append
        );

        return $this;
    }
}
