<?php

namespace SteppingHat\EmojiDetector;

use Exception;
use SteppingHat\EmojiDetector\Model\EmojiInfo;

class EmojiDetector {

	const LONGEST_EMOJI = 8;

	const SKIN_TONES = [
		'1F3FB' => 'skin-tone-2',
		'1F3FC' => 'skin-tone-3',
		'1F3FD' => 'skin-tone-4',
		'1F3FE' => 'skin-tone-5',
		'1F3FF' => 'skin-tone-6'
	];

	private $map;
	private $regex;
	private $dataDir;

	/**
	 * EmojiDetector constructor.
	 * @throws Exception
	 */
	public function __construct() {
		$this->dataDir = __DIR__ . '/../var';
		$this->loadMap();
		$this->loadRawEmojis();
	}

	/**
	 * @param $string
	 * @return EmojiInfo[]
	 */
	public function detect($string) {

		$oldEncoding = mb_internal_encoding();
		mb_internal_encoding('UTF-8');

		/** @var EmojiInfo[] $emojiInfos */
		$emojiInfos = [];

		$matches = [];
		foreach($this->regex as $icon) {
			$strpos = mb_strpos($string, $icon);
			if($strpos !== false) {
				$matches[] = [$icon, $strpos];
			}
		}

		$length = 0;

		foreach($matches as $match) {
			$emojiInfo = new EmojiInfo();

			$emojiInfo->setEmoji($match[0]);
			$emojiInfo->setOffset(strpos($string, $match[0]));
			$emojiInfo->setMbOffset(mb_strpos($string, $match[0]));

			// Break apart the hex characters and build the hex string
			$hexCodes = [];

			for($i = 0; $i < mb_strlen($emojiInfo->getEmoji()); $i++) {
				$hexCodes[] = strtoupper(dechex($this->unicodeOrd(mb_substr($match[0], $i, 1))));
			}
			$emojiInfo->setHexCodes($hexCodes);

			// Denote the emoji name
			if(array_key_exists($emojiInfo->getHexString(), $this->map)) {
				$emojiInfo->setName($this->map[$emojiInfo->getHexString()]['name']);
				$emojiInfo->setShortName($this->map[$emojiInfo->getHexString()]['shortName']);
				$emojiInfo->setCategory($this->map[$emojiInfo->getHexString()]['category']);
			}


			// Denote the skin tone
			foreach($hexCodes as $hexCode) {
				if(array_key_exists($hexCode, self::SKIN_TONES)) {
					$emojiInfo->setSkinTone(self::SKIN_TONES[$hexCode]);
				}
			}


			$length += (strlen($emojiInfo->getEmoji()) - 1);

			$emojiInfos[] = $emojiInfo;
		}

		usort($emojiInfos, function(EmojiInfo $a, EmojiInfo $b) {
			return $a->getOffset() > $b->getOffset();
		});

		/** @var EmojiInfo[] $data */
		$data = [];
		foreach($emojiInfos as $emoji) {
			if(count($data) == 0) {
				$data[] = $emoji;
				continue;
			}

			/** @var EmojiInfo $last */
			$last = end($data);
			$key = key($data);

			if($last->getOffset() == $emoji->getOffset()) {
				if($last->getMbLength() < $emoji->getMbLength()) {
					$data[$key] = $emoji;
				}
			} else if($emoji->getOffset() >= strlen($last->getEmoji()) + $last->getOffset()) {
				$data[] = $emoji;
			}

			reset($data);
		}

		mb_internal_encoding($oldEncoding);
		return $data;
	}

	/**
	 * @param $string
	 * @return bool
	 */
	public function isSingleEmoji($string) {
		if(mb_strlen($string) > self::LONGEST_EMOJI) return false;

		$emojis = $this->detect($string);
		if(count($emojis) !== 1) return false;

		$emoji = array_pop($emojis);
		$string = str_replace($emoji->getEmoji(), '', $string);
		if(strlen($string) > 0) return false;

		return true;
	}

	/**
	 * @throws Exception
	 */
	private function loadMap() {
		$mapFile = $this->dataDir . '/map.json';
		if(!file_exists($mapFile)) {
			throw new Exception("Could not load Emoji map file");
		}

		$this->map = json_decode(file_get_contents($mapFile), true);

	}

	/**
	 * @throws Exception
	 */
	private function loadRawEmojis() {
		$mapFile = $this->dataDir . '/raw.json';
		if(!file_exists($mapFile)) {
			throw new Exception("Could not load Emoji raw file");
		}

		$this->regex = json_decode(file_get_contents($mapFile), true);

	}

	/**
	 * @param $hexChar
	 * @return bool|int
	 */
	private function unicodeOrd($hexChar) {
		$ord0 = ord($hexChar[0]);
		if($ord0 >= 0 && $ord0 <= 127) return $ord0;

		$ord1 = ord($hexChar[1]);
		if($ord0 >= 192 && $ord0 <= 223) return ($ord0 - 192) * 64 + ($ord1 - 128);

		$ord2 = ord($hexChar[2]);
		if($ord0 >= 224 && $ord0 <= 239) return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);

		$ord3 = ord($hexChar[3]);
		if($ord0 >= 240 && $ord0 <= 247) return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);

		return false;
	}

}
