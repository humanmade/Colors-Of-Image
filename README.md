[![Build Status](https://travis-ci.org/brianfoxwell/ImagePalette.png)](https://travis-ci.org/brianfoxwell/ImagePalette)


ImagePalette
===============

ImagePalette is used to extract a color palette from a given image. Aside from being a native PHP implementation, ImagePalette differs from many palette extractors as it works off a white list color palette. Below is the default palette:

![](https://dl.dropbox.com/u/238502/Captured/RUf54.png)

The main advantage of working from a color palette is closer matching, as each pixel simply has to calculate the color-distance within the palette and chose the best match. This is useful for working with color taxonomies as the taxonomy should have a finite amount of colors.

![](https://dl.dropbox.com/u/238502/Captured/HphVw.png)

See an example of this in action here: http://www.rufflr.com/search/?color=ffcc33

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
$image = new \bfoxwell\ImagePalette\ImagePalette( 'https://www.google.co.uk/images/srpr/logo3w.png' );

// get the prominent colors
$colors = $image->getProminentColors(); // array( '#FFFDD', ... )
```

And there we go!

Options
__________

**Precision**

By default, `ImagePalette` will process every 10th pixel. This is for performance reasons, you can change this like below. The precision is a performance-to-time decision.

```PHP
$image = new \bfoxwell\ImagePalette\ImagePalette( $src, 5 /* precision */ );
```

**Color Count**

To control the amount colors returned set the third parameter.

```PHP
$image = new \bfoxwell\ImagePalette\ImagePalette( $src, 5, 3 /* number of colors to return */ );
```

## Contribution guidelines ##

See: https://github.com/brianfoxwell/ImagePalette/blob/master/CONTRIBUTING.md
