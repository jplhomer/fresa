<?php

namespace Fresa;

use Carbon\Carbon;

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
	 * The default keys on post models
	 * @var array
	 */
	protected $default = [
		'title',
		'content',
		'date',
		'author',
		'status',
	];

	/**
	 * Inserts a post into the WP DB
	 * @return self
	 * @throws Exception if invalid data
	 */
	protected function insertModel()
	{
        $args = ['post_type' => $this->getPostType()] + $this->getDefaultValues();
		$id = wp_insert_post($args, true);

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
        $args = ['ID' => $this->id] + $this->getDefaultValues();
		wp_update_post($args);

		return $this;
	}

	/**
	 * Save meta fields to the DB
	 * @return self
	 */
	protected function persistMetaFields()
	{
		collect($this->attributes)->except($this->default)->keys()->each(function($key) {
			update_post_meta( $this->id, $key, $this->attributes[$key] );
		});

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
			'title' => $object->post_title,
			'content' => $object->post_content,
			'date' => $object->post_date,
			'author' => $object->post_author,
			'status' => $object->post_status,
		]);
	}

	/**
	 * Fetch meta fields and assign them to the instance
	 * @return array of post meta fields
	 */
	protected function fetchMetaFields()
	{
        return (array) get_post_meta( $this->id );
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
     * Get values for the default properties on the PostModel, and in some
     * cases, convert them into default values.
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        foreach ($this->default as $key) {
			switch ($key) {
				case 'status':
					$value = $this->getAttribute($key) ?: $this->getDefaultStatus();
					break;

                case 'date':
                    $value = $this->getAttribute($key) ?: (new Carbon)->toDateTimeString();
                    break;

				default:
					$value = $this->getAttribute($key) ?? '';
					break;
			}

            $values["post_" . $key] = $value;
        }

        return $values;
    }

    /**
     * The default status of a newly-created model
     * @return string  Valid post_status
     */
	public function getDefaultStatus()
	{
		return 'publish';
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
