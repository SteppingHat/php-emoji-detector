<?php

namespace SteppingHat\EmojiDetector;

class MapGenerator {

    private static $upstreamUrl = 'https://unpkg.com/emoji.json@13.1.0/emoji.json';

	public static function generateMap() {
		$dataDir = __DIR__."/../var";
		if(!is_dir($dataDir)) mkdir($dataDir, 0777, true);

		// Load emoji data from amio/emoji.json
		$emojiData = json_decode(file_get_contents(self::$upstreamUrl), true);

		$mapData = [];
		$rawEmojis = [];
		foreach($emojiData as $emoji) {

			$data = [
				'name' => strtolower($emoji['name']),
				'shortName' => self::getShortName($emoji['category']),
				'category' => self::getCategory($emoji['category'])
			];

			$mapData[str_replace(' ', '-', $emoji['codes'])] = $data;

			$rawEmojis[] = $emoji['char'];

			if(isset($emoji['variations'])) {
				foreach($emoji['variations'] as $variation) {
					$mapData[$variation] = $data;
				}
			}

			if(isset($emoji['skin_variations'])) {
				foreach($emoji['skin_variations'] as $variation) {
					$mapData[$variation['unified']] = $data;
				}
			}

		}

		file_put_contents($dataDir."/map.json", json_encode($mapData));

		$keys = array_keys($mapData);
		usort($keys, function($a, $b) {
			return strlen($b) - strlen($a);
		});

		file_put_contents($dataDir."/raw.json", json_encode($rawEmojis));
	}

	private static function getShortName($string) {
		preg_match('#\((.*?)\)#', $string, $matches);
		return str_replace(['(', ')'], '', array_shift($matches));
	}

	private static function getCategory($string) {
		return substr($string, 0, strpos($string, '(') - 1);
	}

}