Colors-Of-Image
===============

Color of Image is used to extract a color palette from a given image. Aside from being a native PHP implementation, Color of Image differes from many palette extracters as it works off a white list color palette.

The main advanage of working from a color palette is closer matching, as each pixel simply has to calculate the color-distance within the palatte and chose the best match. This is useful for working with color taxonomies as the taxonomy should have a finate amount of colors. 

Usage
__________

```PHP
// initiate with image
$image = new ColorOfImage( 'https://www.google.co.uk/images/srpr/logo3w.png' );

// get the prominent colors
$colors = $image->getProminentColors(); // array( '#FFFDD', ... )
```

And there we go! 

Options
__________

**Precision**

By default, `ColorOfImage` will process every 10th pixel. This is for performance reasons, you can change this like below. The precision is a performance-to-time desicion.

```PHP
$image = new ColorOfImage( $src, 5 /* precision */ );
```

**Color Count**

To control the amount colors returned set the third parameter.

```PHP
$image = new ColorOfImage( $src, 5, 3 /* number of colors to return */ );
```