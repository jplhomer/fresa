<?php

namespace Fresa\Concerns;

trait HasAttributes {
    /**
     * The attributes in a model
     * @var array
     */
    protected $attributes = [];

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)
            || $this->hasCastedValue($key)) {
            return $this->getAttributeValue($key);
        }

        if (method_exists($this, $key)) {
            return $this->getRelation($key)->get();
        }
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasCastedValue($key)) {
            $value = $this->getValueFromCastedAttribute($key, $value);
        }

        $this->attributes[$key] = $value;
    }

    public function getAttributeValue($key)
    {
        $value = $this->attributes[$key] ?? null;

        if ($this->hasCastedValue($key)) {
            return $this->castValueForAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Backfill an array of meta to the attributes, if it hasn't been set yet
     * @param  array $values  Key/value pairs
     * @return self
     */
    public function fillExistingMeta($values)
    {
        foreach ($values as $key => $value) {
            if (! isset($this->attributes[$key]) ) {
                $this->attributes[$key] = $value[0];
            }
        }

        return $this;
    }
}
