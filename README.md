PHP Emoji Detector
==================

Have an input string full of emoji's and you want to know detailed information about each emoji?
Want to build an easy way to validate emoji's that come in as input data?

This :clap: is :clap: the :clap: library :clap: for :clap: want!

## What does this thing do?

This library simply parses input strings and returns a list of relevant information about emoji's that are present in the string. It currently supports version 12.1 of the emoji standard.


## Installation

Install this library using composer

    $ composer require steppinghat/emoji-detector
    
    
## Usage

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

All of these properties are protected, but can be accessed by their appropriate getters and setters. E.g. `getCategory()` or `setSkinTone()`.


### Emoji detection

```php
<?php

require_once('vendor/autoload.php');

use SteppingHat\EmojiDetector;

$input = "Hello 👋 world!";
$detector = EmojiDetector();
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

use SteppingHat\EmojiDetector;

$input = "I ❤️ to 👨‍💻";
$detector = new SteppingHat\EmojiDetector\EmojiDetector();
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


### Testing for single emojis

Sometimes it is handy to test if an input string is a single emoji on it's own.

```php
<?php

require_once('vendor/autoload.php');

use SteppingHat\EmojiDetector;

$detector = new SteppingHat\EmojiDetector\EmojiDetector();

$detector->isSingleEmoji("💩"); // Returns TRUE
$detector->isSingleEmoji("Time to dance 🌚"); // Returns FALSE
$detector->isSingleEmoji("🍆🍒"); // Returns FALSE
```

## Tests

Included for library development purposes is a small set of test cases to assure that basic library functions work as expected. These tests can be launched by running the following:

```bash
$ php vendor/bin/simple-phpunit
```

## License

Made with :heart: by Javan Eskander

Available for use under the MIT license.

Emoji data sourced from [amio/emoji.json](https://github.com/amio/emoji.json)
