<?php

namespace Fresa\Concerns;

use Illuminate\Support\Str;

trait RegistersPostTypes {
	/**
	 * The name for the post type
	 * @var string
	 */
	protected $postTypeName = '';

	/**
	 * The args to override the post type registration
	 * @var array
	 */
	protected $postTypeArgs = [];

	public static function register($args = [])
	{
		(new static)->registerPostType($args);
	}

	public function registerPostType($args)
	{
		$this->postTypeArgs = $args;

		add_action( 'init', [$this, 'registerPostTypeHook'] );
	}

	public function registerPostTypeHook()
	{
		register_post_type( static::$postType, wp_parse_args($this->postTypeArgs, [
			'labels'            => $this->getPostTypeLabels(),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor' ),
			'has_archive'       => true,
			'rewrite'           => true,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-admin-post',
			'show_in_rest'      => true,
			'rest_base'         => static::$postType,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		] ));
	}

	/**
	 * Get the labels for the post type
	 * @return array
	 */
	public function getPostTypeLabels()
	{
		$name = $this->getPostTypeName();

		return [
			'name'                => __( $name, 'namespace' ),
			'singular_name'       => __( $name, 'namespace' ),
			'all_items'           => __( 'All ' . Str::plural($name), 'namespace' ),
			'new_item'            => __( 'New ' . Str::plural($name), 'namespace' ),
			'add_new'             => __( 'Add New', 'namespace' ),
			'add_new_item'        => __( 'Add New ' . $name, 'namespace' ),
			'edit_item'           => __( 'Edit ' . $name, 'namespace' ),
			'view_item'           => __( 'View ' . $name, 'namespace' ),
			'search_items'        => __( 'Search ' . Str::plural($name), 'namespace' ),
			'not_found'           => __( 'No ' . Str::plural($name) . ' found', 'namespace' ),
			'not_found_in_trash'  => __( 'No ' . Str::plural($name) . ' found in trash', 'namespace' ),
			'parent_item_colon'   => __( 'Parent ' . $name, 'namespace' ),
			'menu_name'           => __(  Str::plural($name), 'namespace' ),
		];
	}

	/**
	 * Get the name of the post type
	 * @return string
	 */
	public function getPostTypeName()
	{
		return $this->postTypeName ?: class_basename(static::class);
	}
}
