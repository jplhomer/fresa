<?php

namespace Fresa\Concerns;

use Carbon\Carbon;

/**
 * Casts attributes when gettings
 */
trait CastsAttributes
{
    /**
     * Attributes which are casted
     * @var Array
     */
    protected $casts = [];

    public function hasCastedValue($key)
    {
        return array_key_exists($key, $this->casts);
    }

	/**
	 * Get the casted value for an attribute
	 * @param  string $name  Attribute name
	 * @param  mixed $value  Value
	 * @return mixed         Casted Value
	 */
    function castValueForAttribute($name, $value)
    {
        if ( ! array_key_exists($name, $this->casts) ) {
            return $value;
        }

		$method = $this->getCasterMethod($name);

        if ( ! method_exists($this, $method) ) {
            throw new \Exception("Caster method {$method} not available.");
        }

        return $this->$method($value);
    }

	public function getValueFromCastedAttribute($name, $value)
	{
		if ( ! array_key_exists($name, $this->casts) ) {
            return $value;
        }

		$method = $this->getCasterMethod($name, true);

		if ( ! method_exists($this, $method) ) {
            throw new \Exception("Caster method {$method} not available.");
        }

		return $this->$method($value);
	}

	private function getCasterMethod($name, $inverse = false)
	{
		$caster = ucwords($this->casts[$name]);
        return $inverse ? "castFrom{$caster}" : "castTo{$caster}";
	}

	/**
	 * Cast date to a Carbon instance
	 * @param  string $value Value
	 * @return Carbon        Carbon instance
	 */
	protected function castToDate($value)
	{
		if (empty($value)) {
			return $value;
		}

		return new Carbon($value);
	}

	/**
	 * Cast Carbon instance to timestamp
	 * @param  Carbon $date Date
	 * @return String       Date Time Stamp
	 */
	protected function castFromDate($value)
	{
		if (! $value instanceof Carbon) {
			return $value;
		}

		return $value->toDateTimeString();
	}

	/**
	 * Cast a value to a boolean
	 * @param  mixed  $value Value
	 * @return Boolean
	 */
	protected function castToBoolean($value)
	{
		return (bool) $value;
	}

	/**
	 * Casts a boolean to a database value
	 * @param  Boolean $value Value
	 * @return Int            Casted value
	 */
	protected function castFromBoolean($value)
	{
		return $value ? 1 : 0;
	}
}
