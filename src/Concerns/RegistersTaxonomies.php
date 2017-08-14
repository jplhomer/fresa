<?php

namespace Fresa\Concerns;

use Illuminate\Support\Str;

/**
 * Creates an API around WordPress's register_taxonomy.
 */
trait RegistersTaxonomies
{
    /**
     * Post models attached to taxonomy.
     *
     * @var array
     */
    protected $taxonomyPostModels = [];

    /**
     * Name of taxonomy.
     *
     * @var string
     */
    protected $taxonomyName = '';

    /**
     * Args to override taxonomy registration.
     *
     * @var array
     */
    protected $taxonomyArgs = [];

    public static function register($postModels = [], $args = [])
    {
        (new static())->registerTaxonomy($postModels, $args);
    }

    /**
     * Register the taxonomy.
     *
     * @param array $postModels Array of PostModels (optional)
     * @param array $args       Array of arguments to override default options
     *
     * @return void
     */
    public function registerTaxonomy($postModels, $args)
    {
        $this->taxonomyPostModels = $postModels;
        $this->taxonomyArgs = $args;

        add_action('init', [$this, 'registerTaxonomyHook']);
    }

    /**
     * Hook to be run on init to register taxonomy.
     *
     * @return void
     */
    public function registerTaxonomyHook()
    {
        register_taxonomy(
            $this->getTaxonomy(),
            $this->getTaxonomyPostTypes(),
            wp_parse_args($this->taxonomyArgs, [
                'hierarchical'      => false,
                'public'            => true,
                'show_in_nav_menus' => true,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => true,
                'capabilities'      => [
                    'manage_terms'  => 'edit_posts',
                    'edit_terms'    => 'edit_posts',
                    'delete_terms'  => 'edit_posts',
                    'assign_terms'  => 'edit_posts',
                ],
                'labels'                => $this->getTaxonomyLabels(),
                'show_in_rest'          => true,
                'rest_base'             => $this->getTaxonomy(),
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            ])
        );
    }

    /**
     * Get the labels for the taxonomy.
     *
     * @return array Labels
     */
    public function getTaxonomyLabels()
    {
        $name = $this->getTaxonomyName();

        return [
            'name'                       => __(Str::plural($name), 'namespace'),
            'singular_name'              => _x($name, 'taxonomy general name', 'namespace'),
            'search_items'               => __('Search '.Str::plural(Str::lower($name)), 'namespace'),
            'popular_items'              => __('Popular '.Str::plural(Str::lower($name)), 'namespace'),
            'all_items'                  => __('All '.Str::plural(Str::lower($name)), 'namespace'),
            'parent_item'                => __('Parent '.Str::lower($name), 'namespace'),
            'parent_item_colon'          => __('Parent '.Str::lower($name).':', 'namespace'),
            'edit_item'                  => __('Edit '.Str::lower($name), 'namespace'),
            'update_item'                => __('Update '.Str::lower($name), 'namespace'),
            'add_new_item'               => __('New '.Str::lower($name), 'namespace'),
            'new_item_name'              => __('New '.Str::lower($name), 'namespace'),
            'separate_items_with_commas' => __('Separate '.Str::plural(Str::lower($name)).' with commas', 'namespace'),
            'add_or_remove_items'        => __('Add or remove '.Str::plural(Str::lower($name)), 'namespace'),
            'choose_from_most_used'      => __('Choose from the most used '.Str::plural(Str::lower($name)), 'namespace'),
            'not_found'                  => __('No '.Str::plural(Str::lower($name)).' found.', 'namespace'),
            'menu_name'                  => __(Str::plural($name), 'namespace'),
        ];
    }

    /**
     * Get the post type slugs assigned to this taxonomy.
     *
     * @return array Post type slugs
     */
    public function getTaxonomyPostTypes()
    {
        return collect($this->taxonomyPostModels)->map(function ($model) {
            return (new $model)->getPostType();
        })->toArray();
    }

    /**
     * Get the name of the taxonomy.
     *
     * @return string Name
     */
    public function getTaxonomyName()
    {
        return $this->taxonomyName ?: class_basename(static::class);
    }
}
