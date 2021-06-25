<?php

declare(strict_types=1);

namespace VarRepresentation\Tests;

use Phan\Config;
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

    public function testVarRepresentation(): void {
        $this->assertVarRepresentationIs('1', 1);
        $this->assertVarRepresentationIs('0', 0);
        $this->assertVarRepresentationIs('null', null);
        $this->assertVarRepresentationIs('true', true);
        $this->assertVarRepresentationIs('false', false);
        $this->assertVarRepresentationIs("''", '');
        $this->assertVarRepresentationIs("'1'", '1');
        $this->assertVarRepresentationIs('"\\0"', "\0");
    }

    public function testVarRepresentationArray(): void {
        $this->assertVarRepresentationIs("[]", []);
    }
}
