<?php

namespace Fresa;

use Illuminate\Support\Str;

/**
 * Taxonomy abstract class.
 */
abstract class Taxonomy extends Model
{
    use Concerns\RegistersTaxonomies;

    /**
     * The taxonomy slug for this class.
     *
     * @var string
     */
    protected $taxonomy = '';

    /**
     * The default keys on a Taxonomy.
     *
     * @var array<int, string>
     */
    protected $default = [
        'name',
        'description',
        'slug',
    ];

    /**
     * Properties that should be required to create a new taxonomy.
     *
     * @var array
     */
    protected $required = [
        'name',
    ];

    /**
     * Insert new category.
     *
     * @return self
     */
    protected function insertModel()
    {
        $results = wp_insert_term(
            $this->name,
            $this->getTaxonomy(),
            [
                'description' => $this->description ?? '',
            ]
        );

        $this->id = $results['term_id'];

        return $this;
    }

    /**
     * Save default fields to existing instance.
     *
     * @return self
     */
    protected function persistDefaultFields()
    {
        wp_update_term(
            $this->id,
            $this->getTaxonomy(),
            [
                'name'        => $this->name,
                'description' => $this->description,
                'slug'        => $this->slug,
            ]
        );

        return $this;
    }

    /**
     * Get a new instance from an ID.
     *
     * @param int $id Category ID
     *
     * @return \Fresa\Taxonomy
     */
    public function newFromObjectId($id)
    {
        return $this->newFromObject(get_term($id));
    }

    /**
     * Get a new instance from an object.
     *
     * @param \WP_Term $object
     *
     * @return \Fresa\Taxonomy|null
     */
    public function newFromObject($object)
    {
        if (empty($object)) {
            return null;
        }

        return new static([
            'id'          => $object->term_id,
            'name'        => $object->name,
            'description' => $object->description,
            'slug'        => $object->slug,
        ]);
    }

    public function getDefaultValues()
    {
        $values = [];
        foreach ($this->default as $key) {
            $values[$key] = $this->getAttribute($key) ?? '';
        }

        return $values;
    }

    /**
     * Get all terms in a taxonomy.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all()
    {
        $taxonomy = new static();

        return collect(get_terms([
            'taxonomy'   => $taxonomy->getTaxonomy(),
            'hide_empty' => false,
        ]))->map(function ($term) use ($taxonomy) {
            return $taxonomy->newFromObject($term);
        });
    }

    public function fetchMetaFields()
    {
        return [];
    }

    protected function persistMetaFields()
    {
        // TODO
    }

    /**
     * Deletes the taxonomy term.
     *
     * @return self
     */
    public function delete()
    {
        if (!$this->exists) {
            return false;
        }

        wp_delete_term($this->id, $this->getTaxonomy());

        return parent::delete();
    }

    /**
     * Get the taxonomy slug to use for this category.
     *
     * @return string
     */
    public function getTaxonomy()
    {
        if (!empty($this->taxonomy)) {
            return $this->taxonomy;
        }

        return Str::snake((new \ReflectionClass(static::class))->getShortName());
    }

    /**
     * Get a collection of Taxonomies where key=value.
     *
     * @param string $key   Key
     * @param mixed  $value Value
     *
     * @return \Illuminate\Support\Collection
     */
    public static function where($key, $value)
    {
        $taxonomy = new static();
        $terms = get_terms([
            'taxonomy'   => $taxonomy->getTaxonomy(),
            'hide_empty' => false,
            $key         => $value,
        ]);

        return collect($terms)->map(function ($term) use ($taxonomy) {
            return $taxonomy->newFromObject($term);
        });
    }
}
