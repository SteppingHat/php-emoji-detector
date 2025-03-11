<?php 

namespace SteppingHat\EmojiDetector\Tests;

use PHPUnit\Framework\TestCase;
use SteppingHat\EmojiDetector\MapGenerator;

class MapGeneratorTest extends TestCase {

    public static function rawDataProvider(): iterable {
        yield [
            'raw' => '1F600                                                  ; fully-qualified     # 😀 E1.0 grinning face: skin-tone',
            'expected' => [
                'codes' => '1F600',
                'emoji' => '😀',
                'name' => 'grinning face',
                'variation' => 'skin-tone'
            ]
        ];
        yield [
            'raw' => '1F9CE 1F3FF 200D 2642 200D 27A1 FE0F                   ; minimally-qualified # 🧎🏿‍♂‍➡️ E15.1 man kneeling facing right: dark skin tone',
            'expected' => [
                'codes' => '1F9CE 1F3FF 200D 2642 200D 27A1 FE0F',
                'emoji' => '🧎🏿‍♂‍➡️',
                'name' => 'man kneeling facing right',
                'variation' => 'dark skin tone'
            ]
        ];
        yield [
            'raw' => '1F9CE 1F3FF 200D 2642 FE0F 200D 27A1                   ; minimally-qualified # 🧎🏿‍♂️‍➡ E15.1 man kneeling facing right: dark skin tone',
            'expected' => [
                'codes' => '1F9CE 1F3FF 200D 2642 FE0F 200D 27A1',
                'emoji' => '🧎🏿‍♂️‍➡',
                'name' => 'man kneeling facing right',
                'variation' => 'dark skin tone'
            ]
        ];
        yield [
            'raw' => '1F9CE 1F3FF 200D 2642 200D 27A1                        ; minimally-qualified # 🧎🏿‍♂‍➡ E15.1 man kneeling facing right: dark skin tone',
            'expected' => [
                'codes' => '1F9CE 1F3FF 200D 2642 200D 27A1',
                'emoji' => '🧎🏿‍♂‍➡',
                'name' => 'man kneeling facing right',
                'variation' => 'dark skin tone'
            ]
        ];
        yield [
            'raw' => '1F9D1 200D 1F9AF                                       ; fully-qualified     # 🧑‍🦯 E12.1 person with white cane',
            'expected' => [
                'codes' => '1F9D1 200D 1F9AF',
                'emoji' => '🧑‍🦯',
                'name' => 'person with white cane',
                'variation' => null
            ]
        ];
        yield [
            'raw' => '1F9D1 1F3FB 200D 1F9AF                                 ; fully-qualified     # 🧑🏻‍🦯 E12.1 person with white cane: light skin tone',
            'expected' => [
                'codes' => '1F9D1 1F3FB 200D 1F9AF',
                'emoji' => '🧑🏻‍🦯',
                'name' => 'person with white cane',
                'variation' => 'light skin tone'
            ]
        ];
        yield [
            'raw' => '1F469 1F3FC 200D 2764 FE0F 200D 1F469 1F3FE            ; fully-qualified     # 👩🏼‍❤️‍👩🏾 E13.1 couple with heart: woman, woman, medium-light skin tone, medium-dark skin tone',
            'expected' => [
                'codes' => '1F469 1F3FC 200D 2764 FE0F 200D 1F469 1F3FE',
                'emoji' => '👩🏼‍❤️‍👩🏾',
                'name' => 'couple with heart',
                'variation' => 'woman, woman, medium-light skin tone, medium-dark skin tone'
            ]
        ];
        yield [
            'raw' => '1F469 1F3FC 200D 2764 200D 1F469 1F3FE                 ; minimally-qualified # 👩🏼‍❤‍👩🏾 E13.1 couple with heart: woman, woman, medium-light skin tone, medium-dark skin tone',
            'expected' => [
                'codes' => '1F469 1F3FC 200D 2764 200D 1F469 1F3FE',
                'emoji' => '👩🏼‍❤‍👩🏾',
                'name' => 'couple with heart',
                'variation' => 'woman, woman, medium-light skin tone, medium-dark skin tone'
            ]
        ];
    }

    /**
     * @dataProvider rawDataProvider
     */
    public function testParseLine(string $raw, array $expected) {
        $reflection = new \ReflectionClass(MapGenerator::class);
        $method = $reflection->getMethod('parseLine');
        $method->setAccessible(true);
        for ($i = 1; $i < 1000; $i++) {
            $parsedLine = $method->invoke(null, $raw);
        }
        $this->assertEquals($expected, $parsedLine);
    }

}