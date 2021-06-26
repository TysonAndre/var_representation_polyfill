<?php

declare(strict_types=1);

namespace VarRepresentation\Tests;

use PHPUnit\Framework\TestCase;

use function var_representation;

/**
 * Any common initialization or configuration should go here
 * (E.g. this changes https://phpunit.de/manual/current/en/fixtures.html#fixtures.global-state for some classes)
 */
class VarRepresentationTest extends TestCase
{
    public function assertVarRepresentationIs(string $expected, $value): void
    {
        $this->assertSame($expected, var_representation($value));
    }

    /**
     * @dataProvider varRepresentationProvider
     */
    public function testVarRepresentation(string $expected, $value): void {
        $this->assertSame($expected, var_representation($value));
    }

    /**
     * @return list<array{0:string,1:mixed}>
     */
    public function varRepresentationProvider(): array
    {
        return [
            ['1', 1],
            ['-1', -1],
            [var_export(PHP_INT_MIN, true), PHP_INT_MIN],
            ['0', 0],
            ['null', null],
            ['true', true],
            ['false', false],
            ["''", ''],
            ["'1'", '1'],
            ['"\\000"', "\0"],
            ['"\\000\\000"', "\0\0"],
            ["'\$var'", '$var'],
            ['[]', []],
            ['ArrayObject::__set_state([])', new \ArrayObject()],
            // ['(object)[]', new \stdClass()],
            // ['[1]', [1]],
        ];
    }
}
