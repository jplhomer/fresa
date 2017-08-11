<?php

use Fresa\PostModel;
use Carbon\Carbon;

/**
 * Event model
 */
class Event extends PostModel
{

	/**
	 * A whitelist of properties for this model
	 * @var Array
	 */
	protected $keys = [
		'start',
		'end',
		'all_day',
		'more_information',
		'venue_name',
		'address',
		'city',
		'state',
		'zip',
		'contact_name',
		'contact_info',
		'recurring',
		'repeat_every',
		'repeat_frequency',
		'repeat_until',
	];

	/**
	 * Cast start and end attributes to Carbon date objects
	 * @var Array
	 */
	protected $casts = [
		'start' => 'date',
		'end' => 'date',
		'all_day' => 'boolean',
		'recurring' => 'boolean',
		'repeat_until' => 'date',
	];

	/**
	 * Protected keys for this model
	 * @var Array
	 */
	protected $requiredKeys = [
		'name',
		'start',
		'end',
	];

	/**
	 * The custom post type with which to store the events
	 * @var String
	 */
	public static $postType = 'event';

	/**
	 * Define a relationship to Category
	 * @return Relation
	 */
	public function categories()
	{
		return $this->hasTaxonomy(Category::class);
	}
}
