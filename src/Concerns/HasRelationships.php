<?php

namespace Fresa\Concerns;

use Fresa\Relation;
use Fresa\Relations\HasTaxonomy;
use Fresa\Relations\BelongsToPost;

trait HasRelationships {
	/**
	 * Cached Relation objects
	 * @var array
	 */
	protected $relations = [];

	/**
	 * Get the cached Relation instance
	 * @param  string $key      Key
	 * @return Relation
	 */
	public function getRelation($key)
	{
		// Get cached relation first
		if (! empty($this->relations[$key])) {
			return $this->relations[$key];
		}

		$relation = $this->$key();

		// Cache the relation
		$this->relations[$key] = $relation;

		return $relation;
	}

	/**
	 * Define a new relationship
	 * @param  Class $taxonomy  Taxonomy class
	 * @return Relation         The Relation instance
	 */
	public function hasTaxonomy($taxonomy)
	{
		$relation = new HasTaxonomy($this, $taxonomy);
		$this->relations[$this->guessRelation()] = $relation;
		return $relation;
	}

	/**
	 * Define new belongsTo relationship
	 * @param  Class $model  Model
	 * @return Relation
	 */
	public function belongsToPost($model)
	{
		$relation = new BelongsToPost($this, $model);
		$this->relations[$this->guessRelation()] = $relation;
		return $relation;
	}

	/**
	 * Guess the "belongs to" relationship name.
	 *
	 * @return string
	 */
	protected function guessRelation()
	{
		list($one, $two, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

		return $caller['function'];
	}
}
