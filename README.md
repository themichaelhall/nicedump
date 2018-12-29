# NiceDump

[![Build Status](https://travis-ci.org/themichaelhall/nicedump.svg?branch=master)](https://travis-ci.org/themichaelhall/nicedump)
[![AppVeyor](https://ci.appveyor.com/api/projects/status/github/themichaelhall/nicedump?branch=master&svg=true)](https://ci.appveyor.com/project/themichaelhall/nicedump/branch/master)
[![codecov.io](https://codecov.io/gh/themichaelhall/nicedump/coverage.svg?branch=master)](https://codecov.io/gh/themichaelhall/nicedump?branch=master)
[![StyleCI](https://styleci.io/repos/163510400/shield?style=flat&branch=master)](https://styleci.io/repos/163510400)
[![License](https://poser.pugx.org/nicedump/nicedump/license)](https://packagist.org/packages/nicedump/nicedump)
[![Latest Stable Version](https://poser.pugx.org/nicedump/nicedump/v/stable)](https://packagist.org/packages/nicedump/nicedump)
[![Total Downloads](https://poser.pugx.org/nicedump/nicedump/downloads)](https://packagist.org/packages/nicedump/nicedump)

Dump a PHP variable according to the [NiceDump format specification](https://nicedump.net/). 

## Requirements

- PHP >= 7.1

## Install with composer

``` bash
$ composer require nicedump/nicedump
```

## Basic usage

### Create a NiceDump

``` php
<?php

require __DIR__ . '/vendor/autoload.php';

use NiceDump\NiceDump;

// $var can be anything.
$var = 'Foo';

// Create a plain dump.
$niceDump = NiceDump::create($var);

// Create a dump with a name.
$niceDump = NiceDump::create($var, 'var');

// Create a dump with a name and a comment.
$niceDump = NiceDump::create($var, 'var', 'This is my variable');
```

### Output a NiceDump

``` php
<?php

require __DIR__ . '/vendor/autoload.php';

use NiceDump\NiceDump;

// $var can be anything.
$var = 'Foo';

// Create a plain dump.
$niceDump = NiceDump::create($var);

// Outputs the NiceDump as:
//
// =====BEGIN NICE-DUMP=====
// eyJ0eXBlIjoic3RyaW5nIiwidmFsdWUiOiJGb28iLCJzaXplIjozfQ==
// =====END NICE-DUMP=====
//
// which corresponds to:
//
// {"type":"string","value":"Foo","size":3}
echo $niceDump;

// Get the output explicitly as a string.
$dumpStr = $niceDump->__toString();

// Outputs the same as above.
echo $dumpStr;

// Output can also be done via the global nice_dump() function.
nice_dump($var);
nice_dump($var, 'var');
nice_dump($var, 'var', 'This is my variable');

// Use nice_dump_html() to enclose the output in an HTML-comment.
nice_dump_html($var);
nice_dump_html($var, 'var');
nice_dump_html($var, 'var', 'This is my variable');
```

### Custom serialization

The NiceDump serialization can be customized for a class by implementing the ```NiceDumpSerializable``` interface and the ```niceDumpSerialize()``` method: 

``` php
<?php

require __DIR__ . '/vendor/autoload.php';

use NiceDump\NiceDump;
use NiceDump\NiceDumpSerializable;

class Foo implements NiceDumpSerializable
{
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function niceDumpSerialize(): array
    {
        return [
            'type'  => '_text_',
            'value' => $this->text,
        ];
    }

    private $text;
}

$foo = new Foo('Bar');
$niceDump = NiceDump::create($foo);

// Outputs the NiceDump as:
//
// =====BEGIN NICE-DUMP=====
// eyJ0eXBlIjoiX3RleHRfIiwidmFsdWUiOiJCYXIifQ==
// =====END NICE-DUMP=====
//
// which corresponds to:
//
// {"type":"_text_","value":"Bar"}
echo $niceDump;
```

## License

MIT