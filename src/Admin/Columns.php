<?php

namespace Fresa\Admin;

/**
 * Manage columns for a custom post type
 */
class Columns
{
	/**
	 * Post type to update columns
	 * @var string
	 */
	protected $postType = 'post';

	/**
	 * The name of the model
	 * @var string
	 */
	protected $model = '';

	/**
	 * Args with which to build columns
	 * @var array
	 */
	protected $args = [];

	function __construct($model, $args = [])
	{
		$this->model = $model;
		$this->postType = (new $model)->getPostType();
		$this->args = $args;
	}

	/**
	 * Register event handlers
	 * @return self
	 */
	public function register()
	{
		add_filter("manage_{$this->postType}_posts_columns", [$this, 'add']);
		add_action("manage_{$this->postType}_posts_custom_column", [$this, 'render'], 10, 2);

		return $this;
	}

	/**
	 * Register a column
	 * @param  string $slug
	 * @param  array  $options
	 * @return self
	 */
	public function column($slug, $options = [])
	{
		$this->args['columns'][$slug] = $options;

		return $this;
	}

	/**
	 * Hide a column
	 * @param  string $slug
	 * @return self
	 */
	public function hide($slug)
	{
		$this->args['hide'][] = $slug;

		return $this;
	}

	/**
	 * Register the columns
	 * @param array $columns Existing columns
	 */
	public function add($columns)
	{
		foreach ($this->args['columns'] as $slug => $settings) {
			$label = $settings['label'] ?? ucwords($slug);
			$columns[$slug] = $label;
		}

		foreach ($this->args['hide'] ?? [] as $slug) {
			unset($columns[$slug]);
		}

		return $columns;
	}

	/**
	 * Render the custom columns
	 * @param  string $column Current column
	 * @param  int $id        Post ID
	 * @return void
	 */
	public function render($column, $id)
	{
		$instance = $this->model::find($id);
		$settings = $this->args['columns'][$column];
		if (!empty($settings['value']) && is_callable($settings['value'])) {
			echo $settings['value']($instance);
		}
	}

	/**
	 * Create a new instance
	 * @param  string $model Model class
	 * @return self
	 */
	public static function for($model)
	{
		return new static($model);
	}
}
