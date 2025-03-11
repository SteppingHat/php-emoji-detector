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
	public function detect($string): array {
		$matches = $this->findEmojis($string, false);
		return $this->process($matches, $string);
	}

	/**
	 * @param string $string
	 * @return EmojiInfo[]
	 */
	public function detectDistinct($string): array {
		$matches = $this->findEmojis($string, true);
		return $this->process($matches, $string);
	}

	/**
	 * @param array $matches
	 * @param bool $unique
	 */
	private function process($matches, $string): array {
		$oldEncoding = mb_internal_encoding();
		mb_internal_encoding('UTF-8');

		$emojiInfos = $this->processMatches($matches, $string);

        $this->sortEmojiInfos($emojiInfos);

        // Assure that we're matching the correct emoji (needs extra care when working with ZWJ characters)
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

			if($last->getMbOffset() == $emoji->getMbOffset()) {
				if($last->getMbLength() < $emoji->getMbLength()) {
					$data[$key] = $emoji;
				}
			} else if($emoji->getMbOffset() >= mb_strlen($last->getEmoji()) + $last->getMbOffset()) {
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
	public function isSingleEmoji($string): bool {
		if(mb_strlen($string) > self::LONGEST_EMOJI) return false;

		$emojis = $this->detect($string);
		if(count($emojis) !== 1) return false;

		$emoji = array_pop($emojis);
		$string = str_replace($emoji->getEmoji(), '', $string);

		$split = $this->str_split_unicode($string);
		if(count($split) > 1) return false;
		else if(count($split) === 1 && $split[0] === '') return false;

		return true;
	}

    /**
     * @param $string
     * @return bool
     */
    public function isEmojiString($string): bool {
        $emojis = $this->detect($string, false);

        // Check if the string length differs
        $emojiStringLength = 0;
        foreach($emojis as $emoji) {
            $emojiStringLength += $emoji->getMbLength();
        }
        if($emojiStringLength !== mb_strlen($string)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $string
     * @param bool $unique
     * @return array
     */
    private function findEmojis(string $string, bool $unique = true): array {
        $matches = [];
        foreach($this->regex as $icon) {
            if($unique) {
                $strpos = mb_strpos($string, $icon);
                if($strpos !== false) {
                    $matches[] = [$icon, $strpos];
                }
            } else {
                $lastPos = 0;
                while (($lastPos = mb_strpos($string, $icon, $lastPos)) !== false) {
                    $matches[] = [$icon, $lastPos];
                    $lastPos = $lastPos + mb_strlen($icon);
                }
            }
        }
        return $matches;
    }

    /**
     * @param $matches
     * @param string $string
     * @return EmojiInfo[]
     */
    private function processMatches($matches, string $string) {
        /** @var EmojiInfo[] $emojiInfos */
        $emojiInfos = [];
        $length = 0;

        foreach($matches as $match) {
            $emojiInfo = new EmojiInfo();

            $emojiInfo->setEmoji($match[0]);
            $emojiInfo->setOffset(strpos($string, $match[0], $match[1]));
            $emojiInfo->setMbOffset(mb_strpos($string, $match[0], $match[1]));

            // Break apart the hex characters and build the hex string
            $hexCodes = [];

            for($i = 0; $i < mb_strlen($emojiInfo->getEmoji()); $i++) {
                $hexCodes[] = strtoupper(dechex($this->unicodeOrd(mb_substr($match[0], $i, 1))));
            }
            $emojiInfo->setHexCodes($hexCodes);

            // Denote the emoji name
            if(array_key_exists($emojiInfo->getHexString(), $this->map)) {
                $emojiInfo->setName($this->map[$emojiInfo->getHexString()]['name']);
                $emojiInfo->setCategory($this->map[$emojiInfo->getHexString()]['category']);
                $emojiInfo->setShortName($this->map[$emojiInfo->getHexString()]['subCategory']);
            }

            // Denote the skin tone
            foreach($hexCodes as $hexCode) {
                if(array_key_exists($hexCode, self::SKIN_TONES)) {
                    $emojiInfo->setSkinTone(self::SKIN_TONES[$hexCode]);
                }
            }

            $length += mb_strlen($match[0]);

            $emojiInfos[] = $emojiInfo;
        }

        return $emojiInfos;
    }

    private function loadMap() {
		$mapFile = $this->dataDir . '/map.json';
		if(!file_exists($mapFile)) {
			throw new Exception("Could not load Emoji map file");
		}

		$this->map = json_decode(file_get_contents($mapFile), true);
	}

    private function loadRawEmojis() {
		$mapFile = $this->dataDir . '/raw.json';
		if(!file_exists($mapFile)) {
			throw new Exception("Could not load Emoji raw file");
		}

		$this->regex = json_decode(file_get_contents($mapFile), true);
	}

    /**
     * @param EmojiInfo[] $emojiInfos
     */
    private function sortEmojiInfos(array &$emojiInfos) {
        usort($emojiInfos, function(EmojiInfo $a, EmojiInfo $b) {
            if($a->getOffset() == $b->getOffset()) {
                return 0;
            }
            return $a->getOffset() < $b->getOffset() ? -1 : 1;
        });
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

    /**
	 * @param $str
	 * @param int $l
	 * @return false|string[]
	 */
	private function str_split_unicode($str, $l = 0) {
		if ($l > 0) {
			$ret = array();
			$len = mb_strlen($str, "UTF-8");
			for ($i = 0; $i < $len; $i += $l) {
				$ret[] = mb_substr($str, $i, $l, "UTF-8");
			}
			return $ret;
		}
		return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	}

}
