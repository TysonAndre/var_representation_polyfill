<?php

namespace VarRepresentation;

/**
 * Represents an expression
 */
abstract class Node {
    /** Convert this to a single line string */
    public abstract function __toString(): string;
    /**
     * Convert this to an indented string
     * @unused-param $depth
     */
    public function toIndentedString(int $depth): string {
        return $this->__toString();
    }
}
