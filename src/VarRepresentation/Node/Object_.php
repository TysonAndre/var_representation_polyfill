<?php

namespace VarRepresentation\Node;

use VarRepresentation\Node;

/**
 * Represents an object construction.
 */
class Object_ extends Node {
    /** @var string prefix (e.g. ArrayObject::__set_state() */
    protected $prefix;
    /** @var Array_ inner array */
    protected $array;
    /** @var string suffix (e.g. ')') */
    protected $suffix;

    public function __construct(string $prefix, Array_ $array, string $suffix) {
        $this->prefix = $prefix;
        $this->array = $array;
        $this->suffix = $suffix;
    }

    public function __toString(): string
    {
        return $this->prefix . $this->array->__toString() . $this->suffix;
    }
}
