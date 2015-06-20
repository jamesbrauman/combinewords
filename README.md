# combinewords
> Combine words from word lists into strings in Laravel 5.

combinewords is a package for Laravel 5 that provides functionality to combine random words selected from loaded lists to generate a string.


## Installation

Add `thesnackalicious/combinewords` to your `composer.json` file:

```
"require": {
  "thesnackalicious/combinewords": "dev-master"
}
```

Use `composer` to install this package.

```
$ composer update
```

### Register the Service Provider and Facade

After updating composer add the service provider to the `providers` array in your `config/app.php` file:

```
TheSnackalicious\CombineWords\Providers\CombineWordsServiceProvider::class
```

Add the facade to the `aliases` array in your `config/app.php` file:

```
'CombineWords' => TheSnackalicious\CombineWords\Facades\CombineWords::class
```

### Publish the configuration file

If you want to use an alternative configuration, you can publish the `combinewords.php` configuration file:

```
$ php artisan vendor:publish
```

## How it works

combinewords generates strings by choosing random words based on the supplied format string. For each word holder in the format string, combinewords will choose a random word to replace it from the corresponding .json file. For example: 

```
$ php artisan tinker
>>> CombineWords::make('{color}{noun}');
=> "blackwood"
```

In this example combinewords chooses one random word from the `colors.json` file and one random word from the `nouns.json` file to create the `blackwood` string.

combinewords will look for word list files in the directory specified by the value in the `combinewords.directory` configuration key. combinewords comes with two word lists included: `colors.json` and `nouns.json`.

### Adding Requirements

combinewords allows you to specify requirements that the generated string must meet:

```
$ php artisan tinker
>>> CombineWords::requirement(new MinimumLengthRequirement(5))->make('{color}{noun}');
=> "orangefather"
>>> CombineWords::requirements([new MaximumLengthRequirement(10), function($s) { return strpos($s, 'pink') === 0; }])->make('{color}{noun}');
=> "pinktree"
```

The maximum number of attempts that combinewords will perform is specified by the value in the `combinewords.max_attempts` configuration key. This value can also be passed in as the second parameter to the `make` function.

Note that the requirements stored on the generator are cleared after every call to the `make` function. If you want to preserve the requirements for the next `make` function call you should pass `true` as the third parameter of the `make` function.