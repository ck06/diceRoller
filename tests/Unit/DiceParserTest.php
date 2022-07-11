<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\DiceRollConfig;
use App\Service\DiceStringParser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class DiceParserTest extends TestCase
{
    private DiceStringParser $parser;

    public function setUp(): void
    {
        $this->parser = new DiceStringParser();
    }

    /**
     * @dataProvider inputParserDataProvider
     *
     * @throws ReflectionException
     */
    public function testInputParser(mixed $input, array $expectedResult): void
    {
        $actualSupport = $this->parser->supports($input);
        $this->assertSame(true, $actualSupport);

        // get access to the input parser to test it directly
        $method = (new ReflectionClass($this->parser))->getMethod('parseInput');
        $method->setAccessible(true);

        $actualResult = $method->invokeArgs($this->parser, [$input]);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider diceStringParserDataProvider
     *
     * @throws ReflectionException
     */
    public function testDiceStringParser(string $diceString, DiceRollConfig $expectedConfig): void
    {
        // get access to the dice string parser to test it directly
        $method = (new ReflectionClass($this->parser))->getMethod('parseDiceString');
        $method->setAccessible(true);

        $actualConfig = $method->invokeArgs($this->parser, [$diceString]);
        $this->assertEquals($expectedConfig, $actualConfig);
    }

    /**
     * @dataProvider fullParserDataProvider
     *
     * @param array<DiceRollConfig> $expectedConfigs
     */
    public function testFullParser(string $input, array $expectedConfigs)
    {
        $actualConfigs = $this->parser->parse($input);
        $this->assertEquals($expectedConfigs, $actualConfigs);
    }

    private function inputParserDataProvider(): array
    {
        return [
            // basic patterns
            ['3d6', ['3d6']],
            ['1d20', ['1d20']],

            // drop lowest pattern (this is probably the most commonly used method)
            ['4d6dl1', ['4d6dl1']],

            // keep lowest pattern
            ['2d20kl1', ['2d20kl1']],

            // drop highest pattern
            ['5d8dh2', ['5d8dh2']],

            // keep highest pattern
            ['2d20kh1', ['2d20kh1']],

            // combination of both keep/drop patterns
            ['3d10kl1kh1', ['3d10kl1kh1']],
            ['5d6dl1dh1', ['5d6dl1dh1']],

            // addition and subtraction
            ['8+1d10', ['8', '+1d10']],
            ['2d8+1d6', ['2d8', '+1d6']],
            ['5d6-4', ['5d6', '-4']],

            // flat numbers are also supported.
            ['20', ['20']],
            ['10+4', ['10', '+4']],
        ];
    }

    private function diceStringParserDataProvider(): array
    {
        return [
            [
                '3d6',
                (new DiceRollConfig())->setOriginalString('3d6')->setMode('+')->setRoll('3d6')->setModifiers([])
            ],
            [
                '-1d4',
                (new DiceRollConfig())->setOriginalString('-1d4')->setMode('-')->setRoll('1d4')->setModifiers([])
            ],
            [
                '4d6dl1',
                (new DiceRollConfig())->setOriginalString('4d6dl1')->setMode('+')->setRoll('4d6')->setModifiers(['dl1'])
            ],
        ];
    }

    private function fullParserDataProvider(): array
    {
        return [
            [
                '4d6dl1',
                [
                    (new DiceRollConfig())
                        ->setOriginalString('4d6dl1')
                        ->setMode('+')
                        ->setRoll('4d6')
                        ->setModifiers(['dl1'])
                ]
            ],
            [
                '3d20dl1dh1',
                [
                    (new DiceRollConfig())
                        ->setOriginalString('3d20dl1dh1')
                        ->setMode('+')
                        ->setRoll('3d20')
                        ->setModifiers(['dl1', 'dh1'])
                ]
            ],
            [
                '2d6dl1+8-2d6kh1',
                [
                    (new DiceRollConfig())
                        ->setOriginalString('2d6dl1')
                        ->setMode('+')
                        ->setRoll('2d6')
                        ->setModifiers(['dl1']),
                    (new DiceRollConfig())
                        ->setOriginalString('+8')
                        ->setMode('+')
                        ->setRoll('8')
                        ->setModifiers([]),
                    (new DiceRollConfig())
                        ->setOriginalString('-2d6kh1')
                        ->setMode('-')
                        ->setRoll('2d6')
                        ->setModifiers(['kh1'])
                ]
            ],
        ];
    }
}
