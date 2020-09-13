<?php

namespace Fresa;

/**
 * Query API for WordPress.
 */
class Query
{
    /**
     * Model.
     *
     * @var \Fresa\Model
     */
    protected $model;

    /**
     * Build up the query object.
     *
     * @var array
     */
    public $query = [];

    /**
     * Default model keys, aka not meta keys.
     *
     * @var array<int, string>
     */
    protected $defaultModelKeys = [
        'post_title',
        'author',
        'date',
    ];

    /**
     * Initiate a new query, passing in an instance of the model.
     *
     * @param \Fresa\Model $model Instance of a model
     */
    public function __construct(Model $model, $args = [])
    {
        $this->model = $model;
        $this->query = wp_parse_args($args, [
            'post_type'      => $this->model->getPostType(),
            'posts_per_page' => -1,
        ]);

        return $this;
    }

    /**
     * Add a where clause to the query.
     *
     * @param mixed ...$args Key, Value to compare, Comparator
     *
     * @return self
     */
    public function where(...$args)
    {
        list($key, $value, $compare) = $this->getWhereArgs($args);

        $this->query['meta_query'][] = [
            'key'     => $key,
            'compare' => $compare,
            'value'   => $value,
        ];

        return $this;
    }

    /**
     * Apply a taxonomy query.
     *
     * @param string    $taxonomy Taxonomy slug
     * @param int|array $terms    Term ID(s)
     *
     * @return self
     */
    public function whereTaxonomy($taxonomy, $terms, $field = 'term_id')
    {
        $this->query['tax_query'][] = [
            'taxonomy' => $taxonomy,
            'terms'    => $terms,
            'field'    => $field,
        ];

        return $this;
    }

    /**
     * Get key, compare, and value args for the Where method.
     *
     * @param array $args Arguments passed
     *
     * @return array Arguments in [key, compare, value] structure
     */
    protected function getWhereArgs($args)
    {
        list($key, $compare) = $args;

        if (!empty($args[2])) {
            $value = $args[2];
        } else {
            $value = $compare;
            $compare = '=';
        }

        return [$key, $value, $compare];
    }

    /**
     * Order by a key/column.
     *
     * @param string $key   Key to order by
     * @param string $order Order (asc or desc)
     *
     * @return self
     */
    public function order($key, $order)
    {
        $this->query['orderby'] = $this->isKeyMeta($key) ? 'meta_value' : $key;
        $this->query['order'] = $order;

        if ($this->isKeyMeta($key)) {
            $this->query['meta_key'] = $key;
        }

        return $this;
    }

    /**
     * Determine if the given key should be treated as a meta value.
     *
     * @param string $key Key
     *
     * @return bool
     */
    protected function isKeyMeta($key)
    {
        return !in_array($key, $this->defaultModelKeys);
    }

    /**
     * Get the results of the query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return collect(get_posts($this->query))->map(function ($post) {
            return $this->model->newFromObject($post);
        });
    }

    /**
     * Set a limit on the query.
     *
     * @param int $limit Limit
     *
     * @return self
     */
    public function limit($limit)
    {
        $this->query['posts_per_page'] = $limit;

        return $this;
    }

    /**
     * Set an offset value for the query.
     *
     * @param int $offset
     *
     * @return self
     */
    public function offset($offset)
    {
        $this->query['offset'] = $offset;

        return $this;
    }
}
