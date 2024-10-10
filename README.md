PHP Emoji Detector
==================

[![Latest Stable Version](https://poser.pugx.org/steppinghat/emoji-detector/v)](//packagist.org/packages/steppinghat/emoji-detector)
[![Total Downloads](https://poser.pugx.org/steppinghat/emoji-detector/downloads)](//packagist.org/packages/steppinghat/emoji-detector)
[![License](https://img.shields.io/packagist/l/steppinghat/emoji-detector)](//packagist.org/packages/steppinghat/emoji-detector)
![Build Status](https://github.com/steppinghat/php-emoji-detector/actions/workflows/build_and_test.yml/badge.svg?branch=master)
![Unicode Version](https://img.shields.io/badge/unicode-15.1-purple)

Have an input string full of emoji's and you want to know detailed information about each emoji?
Want to build an easy way to validate emoji's that come in as input data?

This :clap: is :clap: the :clap: library :clap: you :clap: want!

## What does this thing do?

This library simply parses input strings and returns a list of relevant information about emoji's that are present in the string. It currently supports version 15.1 of the emoji standard.

## Installation

Install this library using composer

```
$ composer require steppinghat/emoji-detector
```

ℹ️ **We recommend using the [Caret Version Range (`^`)](https://getcomposer.org/doc/articles/versions.md#caret-version-range-)** (see below for why)

### Updates

Updates to the Unicode version will always be released as new minor versions. We recommend using the (`^`)
caret version range so that new Emoji's are automatically supported in your project when running `composer update`.

Any breaking changes will be released as new major versions, and bugfixes will be released as new patch versions as usual.

## Usage

* [The Model](#the-model)
* [Emoji detection](#emoji-detection)
* [Detect distinct emojis](#detect-distinct-emojis)
* [Testing for single emojis](#testing-for-single-emojis)
* [Testing if a string is completely emojis](#testing-if-a-string-is-completely-emojis)

### The Model

For most outputs, the library will provide an `EmojiInfo` object that contains all of the relevant information about an emoji.
The object will contain the following information:

| Property    | Description |
| ----------- | ----------- |
| `emoji`     | The emoji chacater itself |
| `name`      | A user friendly name of the specific emoji |
| `shortName` | A shortened name of the emoji |
| `category`  | A user friendly category name for the emoji |
| `skinTone`  | The level of skin tone of the emoji (if present) |
| `hexCodes`  | An array of individual hexadecimal characters that are used to make up the emoji |
| `offset`    | The position in the string where the emoji starts |
| `mbOffset`  | The multibyte position in the string where the emoji starts |

All of these properties are protected, but can be accessed by their appropriate getters and setters. E.g. `getCategory()` or `getSkinTone()`.


### Emoji detection

```php
<?php

require_once('vendor/autoload.php');

use SteppingHat\EmojiDetector\EmojiDetector;

$input = "Hello 👋 world!";
$detector = new EmojiDetector();
$emojis = $detector->detect($input);

print_r($emojis);
```
This example is the most common usage of the detector, returning an array of objects that provide detailed information about each emoji found in the input string.

```
Array
(
    [0] => SteppingHat\EmojiDetector\Model\EmojiInfo Object
        (
            [emoji:protected] => 👋
            [name:protected] => waving hand
            [shortName:protected] => hand-fingers-open
            [category:protected] => People & Body
            [skinTone:protected] =>
            [hexCodes:protected] => Array
                (
                    [0] => 1F44B
                )

            [offset:protected] => 6
            [mbOffset:protected] => 6
        )

)
```

The library has full support for complex emoji's that make use of the ZWJ (Zero Width Joiner) character.

```php
<?php

require_once('vendor/autoload.php');

use SteppingHat\EmojiDetector\EmojiDetector;

$input = "I ❤️ to 👨‍💻";
$detector = new EmojiDetector();
$emojis = $detector->detect($input);

print_r($emojis);
```

The above will produce the following output:

```
Array
(
    [0] => SteppingHat\EmojiDetector\Model\EmojiInfo Object
        (
            [emoji:protected] => ❤️
            [name:protected] => red heart
            [shortName:protected] => emotion
            [category:protected] => Smileys & Emotion
            [skinTone:protected] => 
            [hexCodes:protected] => Array
                (
                    [0] => 2764
                    [1] => FE0F
                )

            [offset:protected] => 2
            [mbOffset:protected] => 2
        )

    [1] => SteppingHat\EmojiDetector\Model\EmojiInfo Object
        (
            [emoji:protected] => 👨‍💻
            [name:protected] => man technologist
            [shortName:protected] => person-role
            [category:protected] => People & Body
            [skinTone:protected] => 
            [hexCodes:protected] => Array
                (
                    [0] => 1F468
                    [1] => 200D
                    [2] => 1F4BB
                )

            [offset:protected] => 13
            [mbOffset:protected] => 9
        )

)
```

### Detect distinct emojis

If you only want to detect distinct emojis in a string, you can use the `detectDistinct` method. This will return an array of distinct emojis found in the input string, and the position of the first distinct occurence of that emoji.

```php
<?php

require_once('vendor/autoload.php');

use SteppingHat\EmojiDetector\EmojiDetector;

$detector = new EmojiDetector();

$emojis = $detector->detectDistinct("WHAT IS A KILOMETER 🗣🗣🗣🦅🦅🦅")

print_r($emojis);
```

The above will produce the following output:

```
(
    [0] => SteppingHat\EmojiDetector\Model\EmojiInfo Object
        (
            [emoji:protected] => 🗣
            [name:protected] => speaking head
            [shortName:protected] => person-symbol
            [category:protected] => People & Body
            [skinTone:protected] => 
            [hexCodes:protected] => Array
                (
                    [0] => 1F5E3
                )

            [offset:protected] => 20
            [mbOffset:protected] => 20
        )

    [1] => SteppingHat\EmojiDetector\Model\EmojiInfo Object
        (
            [emoji:protected] => 🦅
            [name:protected] => eagle
            [shortName:protected] => animal-bird
            [category:protected] => Animals & Nature
            [skinTone:protected] => 
            [hexCodes:protected] => Array
                (
                    [0] => 1F985
                )

            [offset:protected] => 32
            [mbOffset:protected] => 23
        )

)
```


### Testing for single emojis

Sometimes it is handy to test if an input string is a single emoji on it's own.

```php
<?php

require_once('vendor/autoload.php');

use SteppingHat\EmojiDetector\EmojiDetector;

$detector = new EmojiDetector();

$detector->isSingleEmoji("💩"); // Returns TRUE
$detector->isSingleEmoji("Time to dance 🌚"); // Returns FALSE
$detector->isSingleEmoji("🍆🍒"); // Returns FALSE
```

### Testing if a string is completely emojis

Similar to calling, `isSingleEmoji`, calling `isEmojiString` will check if a string only contains emojis.

```php
<?php

require_once('vendor/autoload.php')

use SteppingHat\EmojiDetector\EmojiDetector;

$detector = new EmojiDetector();

$detector->isEmojiString("😂😂😂"); // Returns TRUE
$detector->isEmojiString("🏎️💨"); // Returns TRUE
$detector->isEmojiString("Deez nuts 🥜"); // Returns FALSE
```

## Tests

Included for library development purposes is a small set of test cases to assure that basic library functions work as expected. These tests can be launched by running the following:

```bash
$ composer test
```

## License

Made with :heart: by Javan Eskander

Available for use under the MIT license.

Emoji data sourced from [amio/emoji.json](https://github.com/amio/emoji.json)
