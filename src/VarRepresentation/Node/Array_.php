<?php

namespace VarRepresentation\Node;

use VarRepresentation\Node;

/**
 * Represents an array literal
 */
class Array_ extends Node {
    /** @var list<ArrayEntry> the list of nodes (keys and optional values) in the array */
    protected $entries;

    /** @param list<ArrayEntry> $entries the list of nodes (keys and optional values) in the array */
    public function __construct(array $entries) {
        $this->entries = $entries;
    }

    public function __toString(): string
    {
        // TODO check if list
        $inner = implode(', ', $this->entries);
        return '[' . $inner . ']';
    }
}
