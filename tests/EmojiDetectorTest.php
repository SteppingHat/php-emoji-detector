<?php

namespace SteppingHat\EmojiDetector\Tests;

use PHPUnit\Framework\TestCase;
use SteppingHat\EmojiDetector\EmojiDetector;

class EmojiDetectorTest extends TestCase {

	public function testDetectEmoji() {
		$string = '🍆';
		$emojis = (new EmojiDetector())->detect($string);

		$this->assertCount(1, $emojis);

		$emoji = array_shift($emojis);

		$this->assertSame('🍆', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('eggplant', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('food-vegetable', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('Food & Drink', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertSame(1, $emoji->getMbLength(), "More emoji characters in the hex string are present than expected");
		$this->assertNull($emoji->getSkinTone(), "Emoji object contains a skin tone when none should exist");
		$this->assertSame(0, $emoji->getOffset(), "Emoji is indicating a position other than the start of the string");
		$this->assertSame(0, $emoji->getMbOffset(), "Emoji is indicating a position other than the start of the string");
		$this->assertSame(['1F346'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");
	}

	/**
	 *
	 */
	public function testDetectSimpleEmoji() {
		$string = '❤️';
		$emojis = (new EmojiDetector())->detect($string);

		$this->assertCount(1, $emojis);

		$emoji = array_shift($emojis);

		$this->assertSame('❤️', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('red heart', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('emotion', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('Smileys & Emotion', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertSame(2, $emoji->getMbLength(), "More emoji characters in the hex string are present than expected");
		$this->assertNull($emoji->getSkinTone(), "Emoji object contains a skin tone when none should exist");
		$this->assertSame(0, $emoji->getOffset(), "Emoji is indicating a position other than the start of the string");
		$this->assertSame(0, $emoji->getMbOffset(), "Emoji is indicating a position other than the start of the string");
		$this->assertSame(['2764', 'FE0F'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");
	}

    public function testDetectZWJEmoji() {
        $string = '🚣‍♂️';
        $emojis = (new EmojiDetector())->detect($string);

        $this->assertCount(1, $emojis);
    }

	public function testDetectEmojiInString() {
		$string  = 'LOL 😂!';
		$emojis = (new EmojiDetector())->detect($string);

		$this->assertCount(1, $emojis);

		$emoji = array_shift($emojis);

		$this->assertSame('😂', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('face with tears of joy', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('face-smiling', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('Smileys & Emotion', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertSame(1, $emoji->getMbLength(), "More emoji characters in the hex string are present than expected");
		$this->assertNull($emoji->getSkinTone(), "Emoji object contains a skin tone when none should exist");
		$this->assertSame(4, $emoji->getOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(4, $emoji->getMbOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(['1F602'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");
	}

	public function testDetectMultipleEmoji() {
		$string = '🌚💩';
		$emojis = (new EmojiDetector())->detect($string);

		$this->assertCount(2, $emojis);

		$emoji = array_shift($emojis);

		$this->assertSame('🌚', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('new moon face', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('sky & weather', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('Travel & Places', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertCount(1, $emoji->getHexCodes(), "More emoji characters in the hex string are present than expected");
		$this->assertNull($emoji->getSkinTone(), "Emoji object contains a skin tone when none should exist");
		$this->assertSame(0, $emoji->getOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(0, $emoji->getMbOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(['1F31A'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");

		$emoji = array_shift($emojis);

		$this->assertSame('💩', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('pile of poo', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('face-costume', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('Smileys & Emotion', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertSame(1, $emoji->getMbLength(), "More emoji characters in the hex string are present than expected");
		$this->assertNull($emoji->getSkinTone(), "Emoji object contains a skin tone when none should exist");
		$this->assertSame(4, $emoji->getOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(1, $emoji->getMbOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(['1F4A9'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");
	}

	public function testDetectSkinToneEmoji() {
		$string = '🤦🏻';
		$emojis = (new EmojiDetector())->detect($string);

		$this->assertCount(1, $emojis);

		$emoji = array_shift($emojis);

		$this->assertSame('🤦🏻', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('person facepalming: light skin tone', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('person-gesture', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('People & Body', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertCount(2, $emoji->getHexCodes(), "More emoji characters in the hex string are present than expected");
		$this->assertSame('skin-tone-2', $emoji->getSkinTone(), "Emoji reported a different skin tone than expected");
		$this->assertSame(0, $emoji->getOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(0, $emoji->getMbOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(['1F926', '1F3FB'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");
	}

	public function testDetectComplexString() {
		$string = 'I ❤️ working on 👨‍💻';
		$emojis = (new EmojiDetector())->detect($string);

		$this->assertCount(2, $emojis);

		$emoji = array_shift($emojis);

		$this->assertSame('❤️', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('red heart', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('emotion', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('Smileys & Emotion', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertCount(2, $emoji->getHexCodes(), "More emoji characters in the hex string are present than expected");
		$this->assertSame(null, $emoji->getSkinTone(), "Emoji reported a different skin tone than expected");
		$this->assertSame(2, $emoji->getOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(2, $emoji->getMbOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(['2764', 'FE0F'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");

		$emoji = array_shift($emojis);

		$this->assertSame('👨‍💻', $emoji->getEmoji(), "Expected emoji does not match actual emoji in string");
		$this->assertSame('man technologist', $emoji->getName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('person-role', $emoji->getShortName(), "Expected emoji name does not match the actual emoji name");
		$this->assertSame('People & Body', $emoji->getCategory(), "Expected emoji category does not match the actual emoji category");
		$this->assertCount(3, $emoji->getHexCodes(), "More emoji characters in the hex string are present than expected");
		$this->assertSame(null, $emoji->getSkinTone(), "Emoji reported a different skin tone than expected");
		$this->assertSame(20, $emoji->getOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(16, $emoji->getMbOffset(), "Emoji is indicating a position that is not expected");
		$this->assertSame(['1F468', '200D', '1F4BB'], $emoji->getHexCodes(), "Invalid hex codes representing the emoji were presented");
	}

	public function testSingleEmoji() {
		$detector = new EmojiDetector();

		$string = '🖥';
		$this->assertTrue($detector->isSingleEmoji($string));

		$string = '😂🎉';
		$this->assertFalse($detector->isSingleEmoji($string));

		$string = '👨‍💻';    // This one has a ZWJ character
		$this->assertTrue($detector->isSingleEmoji($string));

		$string = '👨💻';    // This one does not have a ZWJ character
		$this->assertFalse($detector->isSingleEmoji($string));
	}

    public function emojiStringDataProvider(): iterable {
        yield ['👀'];
        yield ['🙃🔫'];
        yield ['😂😂😂😂😂😂😂😂😂😂'];
        yield ['❤'];
        yield ['🚣‍♂️'];
    }

    /**
     * @dataProvider emojiStringDataProvider
     */
    public function testEmojiString(string $string) {
        $detector = new EmojiDetector();
        $this->assertTrue($detector->isEmojiString($string));
    }

    public function notPureEmojisProvider(): iterable {
        yield ['🌚 Seriously though, this emoji is such a meme'];
        yield ['🗿 Well would you look at that 👀'];
        yield ['Deez nuts 🥜'];
        yield ['HA! GOOTEEM!!'];
        yield ['❤!'];
        yield ['ޟ'];
        yield ['Ѩ'];
        yield ['Ҋ'];
        yield ['ԪԪ'];
        yield ['ԪԪ❗'];
    }

    /**
     * @dataProvider notPureEmojisProvider
     */
    public function testFailEmojiString(string $string) {
        $detector = new EmojiDetector();
        $this->assertFalse($detector->isEmojiString($string));
    }

}
