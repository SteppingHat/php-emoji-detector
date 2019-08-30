<?php

namespace SteppingHat\EmojiDetector\Model;

class EmojiInfo {

	/**
	 * The emoji characters
	 * @var string
	 */
	protected $emoji;

	/**
	 * A friendly emoji name
	 * @var string
	 */
	protected $name;

	/**
	 * An even friendlier emoji name
	 * @var string
	 */
	protected $shortName;

	/**
	 * The category of the emoji
	 * @var string
	 */
	protected $category;

	/**
	 * The detected skin tone variation (if present)
	 * @var string
	 */
	protected $skinTone;

	/**
	 * An array of individual UTF-8 hex characters that make up the emoji
	 * @var array
	 */
	protected $hexCodes;

	/**
	 * The offset of the emoji in the parent string if present
	 * @var int
	 */
	protected $offset;

	/**
	 * The multibyte offset
	 * @var int
	 */
	protected $mbOffset;

	public function __toString() {
		return $this->getEmoji();
	}

	/**
	 * @return string
	 */
	public function getEmoji() {
		return $this->emoji;
	}

	/**
	 * @param string $emoji
	 */
	public function setEmoji($emoji) {
		$this->emoji = $emoji;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getShortName() {
		return $this->shortName;
	}

	/**
	 * @param string $shortName
	 */
	public function setShortName($shortName) {
		$this->shortName = $shortName;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param string $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * @return string
	 */
	public function getSkinTone() {
		return $this->skinTone;
	}

	/**
	 * @param string $skinTone
	 */
	public function setSkinTone($skinTone) {
		$this->skinTone = $skinTone;
	}

	/**
	 * @return array
	 */
	public function getHexCodes() {
		return $this->hexCodes;
	}

	/**
	 * @param array $hexCodes
	 */
	public function setHexCodes($hexCodes) {
		$this->hexCodes = $hexCodes;
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset) {
		$this->offset = $offset;
	}

	/**
	 * @return int
	 */
	public function getMbOffset() {
		return $this->mbOffset;
	}

	/**
	 * @param int $mbOffset
	 */
	public function setMbOffset($mbOffset) {
		$this->mbOffset = $mbOffset;
	}

	/**
	 * @return int
	 */
	public function getMbLength() {
		if($this->hexCodes === null) return false;
		else return count($this->hexCodes);
	}

	/**
	 * @return string
	 */
	public function getHexString() {
		return implode('-', $this->getHexCodes());
	}

}