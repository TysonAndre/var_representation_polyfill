<?php

declare(strict_types=1);

namespace VarRepresentation\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use VarRepresentation\Encoder;

use function var_representation;

/**
 * Any common initialization or configuration should go here
 * (E.g. this changes https://phpunit.de/manual/current/en/fixtures.html#fixtures.global-state for some classes)
 */
class VarRepresentationTest extends TestCase
{
    public function assertVarRepresentationIs(string $expected, $value, int $flags): void
    {
        $this->assertSame($expected, Encoder::toVarRepresentation($value, $flags), 'unexpected result for Encoder::toVarRepresentation');
        $this->assertSame($expected, @var_representation($value, $flags), 'unexpected result for var_representation (possibly the native version)');
    }

    /**
     * @dataProvider varRepresentationProvider
     */
    public function testVarRepresentation(string $expected, $value): void
    {
        $this->assertVarRepresentationIs($expected, $value, VAR_REPRESENTATION_SINGLE_LINE);
        if (is_string($value)) {
            $this->assertSame($expected, Encoder::encodeRawString($value));
        }
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
            ['"\\x00"', "\0"],
            ['"\\x00\\x00"', "\0\0"],
            ["'\$var'", '$var'],
            ['[]', []],
            ['null', STDIN],
            ["[['key' => 'value']]", [['key' => 'value']]],
            ['[1]', [1]],
            ['[1, 2]', [1, 2]],
            ['[-1 => 1]', [-1 => 1]],
            ["['key' => 'value']", ['key' => 'value']],
            ['\ArrayObject::__set_state([])', new \ArrayObject()],
            [
                '"\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b\x0c\r\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f !\"#\$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f"',
                "\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b\x0c\r\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f !\"#\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f",
            ],
            ['(object) []', new \stdClass()],
        ];
    }

    public function testVarRepresentationCircular(): void
    {
        $o = new stdClass();
        $o->o = $o;
        $o->other = 123;
        $expected = <<<EOT
(object) [
  'o' => null,
  'other' => 123,
]
EOT;
        $this->assertSame($expected, @var_representation($o));
        $this->assertSame("(object) ['o' => null, 'other' => 123]", @var_representation($o, VAR_REPRESENTATION_SINGLE_LINE));
    }

    /**
     * @dataProvider varRepresentationIndentedProvider
     */
    public function testVarRepresentationIndented(string $expected, $value): void
    {
        $this->assertVarRepresentationIs($expected, $value, 0);
    }

    /**
     * @return list<array{0:string,1:mixed}>
     */
    public function varRepresentationIndentedProvider(): array
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
            ['"\\x00"', "\0"],
            ['"\\x00\\x00"', "\0\0"],
            ["'\$var'", '$var'],
            ['[]', []],
            ['null', STDIN],
            [
                <<<EOT
[
  [
    'key' => 'value',
  ],
]
EOT
                , [['key' => 'value']]
            ],
            [
                <<<EOT
[
  1,
]
EOT
                , [1],
            ],
            [
                <<<EOT
[
  [
    1,
    [],
  ],
]
EOT
                , [[1,[]]],
            ],
            ['\ArrayObject::__set_state([])', new \ArrayObject()],
            ['(object) []', new \stdClass()],
            [
            <<<EOT
(object) [
  'key' => [],
  'other' => [
    (object) [
      'x' => -1,
    ],
  ],
]
EOT
                , (object) ['key' => [], 'other' => [(object) ['x' => -1]]],
            ],
        ];
    }

    /**
     * @dataProvider encodeStringProvider
     */
    public function testEncodeString(string $expected_double_quoted, ?string $expected_quoted, string $raw): void
    {
        $this->assertSame($expected_double_quoted, Encoder::encodeRawStringDoubleQuoted($raw));
        $this->assertSame($expected_quoted ?? $expected_double_quoted, Encoder::encodeRawString($raw));
    }

    /**
     * @return list<array{0:string,1?:string,2:string}>
     */
    public function encodeStringProvider(): array
    {
        return [
            ['""', "''", ''],
            ['"a"', "'a'", 'a'],
            ['"\$\""', "'$\"'", '$"'],
            ['"\x00"', null, "\0"],
        ];
    }

}
