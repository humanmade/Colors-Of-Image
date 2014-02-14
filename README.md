# ImagePalette
[![Build Status](https://travis-ci.org/brianfoxwell/ImagePalette.png)](https://travis-ci.org/brianfoxwell/ImagePalette)
[![Total Downloads](https://poser.pugx.org/bfoxwell/image-palette/downloads.png)](https://packagist.org/packages/bfoxwell/image-palette)
[![Latest Stable Version](https://poser.pugx.org/bfoxwell/image-palette/v/stable.png)](https://packagist.org/packages/bfoxwell/image-palette)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/brianfoxwell/ImagePalette/badges/quality-score.png?s=b7d1cdecd013eb493c32a2e4f2c53058185b555e)](https://scrutinizer-ci.com/g/brianfoxwell/ImagePalette/)

ImagePalette is used to extract a color palette from a given image. Aside from being a native PHP implementation, ImagePalette differs from many palette extractors as it works off a white list color palette. Below is the default palette:

![](http://i.imgur.com/Rabqkqq.png)

The main advantage of working from a color palette is closer matching, as each pixel simply has to calculate the color-distance within the palette and chose the best match. This is useful for working with color taxonomies as the taxonomy should have a finite amount of colors.

![](http://i.imgur.com/O8fsFWz.png)

See an example of this in action here: http://wallbase.cc/wallpaper/377921

## Requirements
```PHP >= 5.3``` ```php5-gd```

## Installation

Simply add the following to your ```composer.json``` file:

```JSON
"require": {
    "bfoxwell/image-palette": "dev-master"
}
```

## Usage

```PHP
// initiate with image
$palette = new \Bfoxwell\ImagePalette\ImagePalette( 'https://www.google.co.uk/images/srpr/logo3w.png' );

// get the prominent colors
$colors = $palette->colors;          // array('#ffffdd', ...)
$colors = $palette->hexStringColors; // array('#ffffdd', ...)
$colors = $palette->rgbStringColors; // array('rgb(255,0,15)', ...)
$colors = $palette->intcolors;       // array(0xffffdd, ...)
$colors = $palette->rgbColors;       // array(array(255, 0, 15), ...)

// to string as json
echo $palette; // '["#ffffdd", ... ]'

// implements IteratorAggregate
foreach ($palette as $color) { ... }
```

And there we go!

### Laravel 4

Find the `providers` key in `app/config/app.php` and register the `ImagePaletteServiceProvider`:

```php
'providers' => array(
    // ...
    'Bfoxwell\ImagePalette\Laravel\ImagePaletteServiceProvider',
)
```

Then, find the `aliases` key in `app/config/app.php` and register the `ImagePaletteFacade`:

```php
'aliases' => array(
    // ...
    'ImagePalette' => 'Bfoxwell\ImagePalette\Laravel\ImagePaletteFacade',
)
```

Example:

```php
$fileOrUrl = 'https://www.google.com/images/srpr/logo11w.png';

ImagePalette::getColors($fileOrUrl);
```

Result:
```php
array (
  0 => '#0066cc',
  1 => '#cc3333',
  2 => '#ff9900',
  3 => '#424153',
  4 => '#cc6633',
)
```

### Options

#### Precision

By default, `ImagePalette` will process every 10th pixel. This is for performance reasons, you can change this like below. The precision is a performance-to-time decision.

```PHP
$palette = new \bfoxwell\ImagePalette\ImagePalette( $src, 5 /* precision */ );
```

#### Color Count

To control the amount colors returned set the third parameter.
It can also be set for each getter.

```PHP
$palette = new \bfoxwell\ImagePalette\ImagePalette( $src, 5, 3 /* number of colors to return */ );
$colors = $palette->getColors(7 /* number of colors to return */);
```

## Contribution guidelines ##

See: https://github.com/brianfoxwell/ImagePalette/blob/master/CONTRIBUTING.md


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/brianfoxwell/imagepalette/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

