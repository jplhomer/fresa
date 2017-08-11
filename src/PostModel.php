<?php

namespace Fresa;

/**
 * Post Model extends functionality onto the base Model class
 */
abstract class PostModel extends Model
{
	use Concerns\RegistersPostTypes;

	/**
	 * Post type to override.
	 * @var String
	 */
	public static $postType = '';

	/**
	 * Override Model save method to also persist relations
	 * @return self
	 */
	public function save()
	{
		parent::save();

		$this->persistRelations();

		return $this;
	}

	/**
	 * Inserts a post into the WP DB
	 * @return self
	 * @throws Exception if invalid data
	 */
	protected function insertModel()
	{
		$id = wp_insert_post([
			'post_type' => $this->getPostType(),
			'post_title' => $this->name,
			'post_content' => $this->content ?? '',
			'post_status' => 'publish',
		], true);

		if ( is_wp_error($id) ) {
			throw new \Exception("Error creating model: {$id->get_error_message()}");
		}

		$this->id = $id;

		return $this;
	}

	/**
	 * Save default post fields
	 * @return self
	 */
	protected function persistDefaultFields()
	{
		wp_update_post([
			'ID' => $this->id,
			'post_title' => $this->name,
			'post_content' => $this->content ?? '',
		]);

		return $this;
	}

	/**
	 * Save meta fields to the DB
	 * @return self
	 */
	protected function persistMetaFields()
	{
		foreach ($this->keys as $key) {
			update_post_meta( $this->id, $key, $this->getValueFromCastedAttribute($key, $this->$key) );
		}

		return $this;
	}

	public function newFromObjectId($id)
	{
		return $this->newFromObject(get_post($id));
	}

	public function newFromObject($object)
	{
		return new static([
			'id' => $object->ID,
			'name' => $object->post_title,
			'content' => $object->post_content,
		]);
	}

	/**
	 * Fetch meta fields and assign them to the instance
	 * @return self
	 */
	protected function fetchMetaFields()
	{
		foreach ($this->keys() as $key) {
			if ( $value = get_post_meta( $this->id, $key, true ) ) {
				$this->$key = $this->castValueForAttribute($key, $value);
			}
		}

		return $this;
	}

	public static function all()
	{
		return (new static)->newQuery()->get();
	}

	/**
	 * Get a new query object for this model
	 * @return Query
	 */
	public function newQuery($args = [])
	{
		return new Query($this, $args);
	}

	/**
	 * Get the post type for the model
	 * @return string
	 */
	public function getPostType()
	{
		return static::$postType;
	}

	/**
	 * Get the WordPress permalink for this post
	 * @return string
	 */
	public function permalink()
	{
		if ($this->exists) {
			return get_permalink($this->id);
		}
	}

	/**
	 * Get the content formatted as an excerpt
	 * @return string
	 */
	public function excerpt()
	{
		return wp_trim_words($this->content, 20);
	}

	/**
	 * Delegate missing instance methods to the query object
	 * @param  string $method Method name
	 * @param  array $args    Args passed
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		return $this->newQuery()->$method(...$args);
	}

	/**
	 * Attempt to call a missing static method as an instance method
	 * @param  string $method Method
	 * @param  array $args    Args passed
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		return (new static)->$method(...$args);
	}
}
