<?php

namespace Fresa;

/**
 * Model base class
 */
abstract class Model
{
	use Concerns\CastsAttributes,
		Concerns\HasRelationships;

	/**
	 * The WP Post ID
	 * @var Int
	 */
	public $id = 0;

	/**
	 * The default keys on every model
	 * @var Array
	 */
	protected $defaultKeys = [
		'id',
		'name',
		'content',
	];

	/**
	 * Does this model exist in the database?
	 * @var Boolean
	 */
	public $exists = false;

	/**
	 * Define a set of required keys to validate against
	 * @var Array
	 */
	protected $requiredKeys = [];

	/**
	 * Represents properties that can be defined on model
	 * @var Array
	 */
	protected $keys = [];

	public function __construct($args = [])
	{
		$this->hydrate($args);
	}

	/**
	 * Save the data for this model
	 * @return self
	 */
	public function save()
	{
		// Validate the data is good
		$this->validate();

		// Insert the base post if it's not there yet
		if ( empty($this->id) ) {
			$this->insertModel();
			$this->exists = true;
		} else {
			$this->persistDefaultFields();
		}

		$this->persistMetaFields();

		return $this;
	}

	/**
	 * Persist the WP Post fields to the database
	 * @return self
	 */
	abstract protected function persistDefaultFields();

	/**
	 * Persist meta fields to the DB
	 */
	abstract protected function persistMetaFields();

	/**
	 * Validate the current model for required keys
	 * @return Boolean  	Passes validation
	 * @throws Exception  	If validation fails
	 */
	protected function validate()
	{
		collect($this->requiredKeys)->each(function($key) {
			if ( empty($this->$key) ) {
				throw new \Exception("A {$key} attribute is required");
			}
		});

		return true;
	}

	/**
	 * Get a model from the database
	 * @param  Int $id    Post ID
	 * @return Model
	 */
	public static function find($id)
	{
		return (new static)->newFromObjectId($id);
	}

	/**
	 * Get a new instance from a DB object
	 * @var Mixed
	 */
	abstract public function newFromObject($object);

	/**
	 * Get a new instance from the ID of a DB object
	 * @var Int
	 */
	abstract public function newFromObjectId($objectId);

	/**
	 * Get the keys from the subclass
	 * @return Array
	 */
	public function keys()
	{
		return $this->keys;
	}

	/**
	 * Hydrate attributes on object from arguments and meta
	 * @param  array  $args Arguments
	 * @return self
	 */
	protected function hydrate($args = [])
	{
		// Step 1: Set up object based on variables passed
		collect($this->defaultKeys)->merge($this->keys)->each(function($key) use ($args) {
			$this->$key = $this->castValueForAttribute($key, $args[$key] ?? null);
		});

		// Step 2: In case we're hydrating an already-persisted model, hydrate
		// the rest of the properties from post meta. This means some keys from
		// above are possibly overwritten.
		if ( !empty($args['id']) ) {
			$this->exists = true;
			$this->fetchMetaFields();
		}

		return $this;
	}

	/**
	 * Fetch meta fields and assign them to object keys
	 */
	abstract protected function fetchMetaFields();

	/**
	 * Get all models in the database
	 * @return Collection
	 */
	abstract public static function all();

	/**
	 * Instantiate a new model instance and save it
	 * @param  Array $args  Args
	 * @return Model
	 */
	public static function create($args = [])
	{
		return (new static($args))->save();
	}

	/**
	 * Provide a way to move this to a string
	 * @return string
	 */
	public function __toString()
	{
		return serialize($this);
	}

	/**
	 * Handle undefined keys by returning relationship objects
	 * @param  string $key Key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (method_exists($this, $key)) {
			return $this->getRelation($key)->get();
		}
	}
}
