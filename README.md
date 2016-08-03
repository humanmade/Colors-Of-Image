Colors-Of-Image
===============

# This project is no longer supported, please refer to the fork at https://github.com/brianmcdo/ImagePalette

Color of Image is used to extract a color palette from a given image. Aside from being a native PHP implementation, Color of Image differes from many palette extracters as it works off a white list color palette. Below is the default palette:

![](https://dl.dropbox.com/u/238502/Captured/RUf54.png)

The main advanage of working from a color palette is closer matching, as each pixel simply has to calculate the color-distance within the palatte and chose the best match. This is useful for working with color taxonomies as the taxonomy should have a finate amount of colors. 

![](https://dl.dropbox.com/u/238502/Captured/HphVw.png)

See an example of this in action here: http://www.rufflr.com/search/?color=ffcc33

Usage
__________

```PHP
// initiate with image
$image = new ColorsOfImage( 'https://www.google.co.uk/images/srpr/logo3w.png' );

// get the prominent colors
$colors = $image->getProminentColors(); // array( '#FFFDD', ... )
```

And there we go! 

Options
__________

**Precision**

By default, `ColorsOfImage` will process every 10th pixel. This is for performance reasons, you can change this like below. The precision is a performance-to-time desicion.

```PHP
$image = new ColorsOfImage( $src, 5 /* precision */ );
```

**Color Count**

To control the amount colors returned set the third parameter.

```PHP
$image = new ColorsOfImage( $src, 5, 3 /* number of colors to return */ );
```

## Contribution guidelines ##

see https://github.com/humanmade/Colors-Of-Image/blob/master/CONTRIBUTING.md
