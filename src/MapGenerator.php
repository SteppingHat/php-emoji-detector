<?php

namespace SteppingHat\EmojiDetector;

use DateTime;

class MapGenerator {

	private static $upstreamUrl = "https://unicode.org/Public/emoji/latest/emoji-test.txt";

	public static function generateMap() {
		$dataDir = __DIR__."/../var";
		if(!is_dir($dataDir)) mkdir($dataDir, 0777, true);

		$handle = fopen(self::$upstreamUrl, "r");
		if(!$handle) {
			throw new \Exception("Failed to fetch emoji data from unicode.org");
		}

		

		$mapData = [];
		$rawEmojis = [];

		$version = null;
		$currentGroup = null;
		$currentSubgroup = null;
		while(($line = fgets($handle)) !== false) {
			$line = trim($line);
			if($version === null && preg_match("/Version: (\d+\.\d+)/", $line, $matches)) {
				$version = $matches[1];
				continue;
			}

			if(strpos($line, "# group: ") === 0) {
				$currentGroup = substr($line, 9);
				$currentSubgroup = null;
				continue;
			}

			if(strpos($line, "# subgroup: ") === 0) {
				$currentSubgroup = substr($line, 12);
				continue;
			}

			if(strpos($line, "#") === 0) {
				continue;
			}

			if(empty($line)) {
				continue;
			}

			$parsedLine = self::parseLine($line);
			$data = [
				'name' => $parsedLine['variation'] ? $parsedLine['name']. ": ".$parsedLine['variation'] : $parsedLine['name'],
				'category' => $currentGroup,
				'subCategory' => $currentSubgroup,
				'emoji' => $parsedLine['emoji']
			];
			
			if($parsedLine['variation']) {
				$data['variation'] = $parsedLine['variation'];
			}

			$mapData[str_replace(' ', '-', $parsedLine['codes'])] = $data;
			$rawEmojis[] = $parsedLine['emoji'];
		}

		fclose($handle);

		if($version === null) {
			throw new \Exception("Version not found in emoji data");
		}

		$metadata = [
			'version' => $version,
		];

		file_put_contents($dataDir."/map.json", json_encode($mapData));
		file_put_contents($dataDir."/raw.json", json_encode($rawEmojis));
		file_put_contents($dataDir."/metadata.json", json_encode($metadata, true));
	}

	private static function parseLine(string $line) {
		$pattern = '/(\S+(?:\s+\S+)*?)\s*;\s*([\w-]+)\s*#\s*(\S+)\s*E\d+\.\d+\s*(.*)/u';
		preg_match($pattern, $line, $matches);
		if(count($matches) != 5) {
			throw new \Exception("Invalid line: ".$line);
		}
		$description = $matches[4];
		if (strpos($description, ': ') !== false) {
			list($name, $variation) = explode(': ', $description, 2);
		} else {
			$name = $description;
			$variation = null;
		}

		return [
			'codes' => $matches[1],
			'emoji' => $matches[3],
			'name' => trim($name),
			'variation' => $variation ? trim($variation) : null,
		];
	}

}