<?php

namespace VarRepresentation;

use RuntimeException;

/**
 * Encodes var_export output into var_representation() output
 */
class Encoder {
    /** @var list<string|array{0:int,1:string,2:int}> the raw tokens from token_get_all */
    protected $tokens;
    /** @var int the last valid index */
    protected $endIndex;
    /** @var string the original raw var_export output */
    protected $raw;
    /** @var int the current offset */
    protected $i = 1;
    /** @var string the built up representation */
    protected $representation = '';

    protected function __construct(string $raw) {
        $this->tokens = self::getTokensWithoutWhitespace($raw);
        $this->endIndex = count($this->tokens);
        $this->raw = $raw;
        unset($this->tokens[0]);
    }

    /**
     * Get tokens without T_WHITESPACE tokens
     * @return list<string|array{0:int,1:string,2:int}>
     */
    public static function getTokensWithoutWhitespace(string $raw): array  {
        $tokens = \token_get_all('<?php ' . $raw);
        foreach ($tokens as $i => $token) {
            if (is_array($token) && $token[0] === T_WHITESPACE) {
                unset($tokens[$i]);
            }
        }
        return array_values($tokens);
    }

    /**
     * Generate a readable var_representation from the original var_export output
     */
    public static function toVarRepresentation(string $raw_string): string
    {
        return (new self($raw_string))->encode();
    }

    /**
     * Encode the entire sequence of tokens
     */
    protected function encode(): string
    {
        $this->encodeValue(0);
        if ($this->i !== count($this->tokens) + 1) {
            throw new RuntimeException("Failed to read token #$this->i of $this->raw: " . var_export($this->tokens[$this->i] ?? null, true));
        }
        return $this->representation;
    }

    /**
     * Read the current token and advance
     * @return string|array{0:int,1:string,2:int}
     */
    private function getToken() {
        $token = $this->tokens[$this->i++];
        if ($token === null) {
            throw new RuntimeException("Unexpected end of tokens in $this->raw");
        }
        return $token;
    }

    /**
     * Read the current token without advancing
     * @return string|array{0:int,1:string,2:int}
     */
    private function peekToken() {
        $token = $this->tokens[$this->i];
        if ($token === null) {
            throw new RuntimeException("Unexpected end of tokens in $this->raw");
        }
        return $token;
    }

    /**
     * Convert a expression representation to the readable representation
     */
    protected function encodeValue(int $depth): void {
        while (true) {
            $token = $this->peekToken();
            if (is_string($token)) {
                if ($token === ')' || $token === ',') {
                    return;
                }
                $this->i++;
                // TODO: Handle `*` in *RECURSION*, `-`, etc
                $this->representation .= $token;
            } else {
                $this->i++;
                // TODO: Handle PHP_INT_MIN as a multi-part expression, strings, etc
                if (is_array($token)) {
                    switch ($token[0]) {
                        case T_CONSTANT_ENCAPSED_STRING;
                            $this->encodeString($token[1]);
                            break;
                        case T_ARRAY;
                            $next = $this->getToken();
                            if ($next !== '(') {
                                throw new RuntimeException("Expected '(' but got " . var_export($next, true));
                            }
                            $this->encodeArray($depth);
                            break;
                        case T_STRING;
                            if ($token[1] === 'NULL') {
                                $this->representation .= 'null';
                                break;
                            }
                        default:
                            $this->representation .= $token[1];
                    }
                }
            }
            if ($this->i >= $this->endIndex) {
                return;
            }
        }
    }

    protected function encodeString(string $prefix): void
    {
        $parts = [eval($prefix)];
        while ($this->peekToken() === '.') {
            $this->i++;
            $parts[] = eval($this->getToken());
        }
    }

    /**
     * Encode an array
     */
    protected function encodeArray(int $depth): void {
        $this->representation .= '[';
        while (true) {
            $token = $this->peekToken();
            if ($token === ',') {
                $this->i++;
                $this->representation .= ',';
                continue;
            } elseif ($token === ')') {
                $this->i++;
                $this->representation .= ']';
                return;
            }
            $this->encodeValue($depth + 1);
        }
    }
}
