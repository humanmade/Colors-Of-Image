<pre>
<?php

// full error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$img = 'https://www.google.com/images/srpr/logo11w.png';
// $img = 'http://wallpapers.wallbase.cc/rozne/wallpaper-2312526.jpg';
// $img = 'http://thumbs.wallbase.cc//rozne/thumb-2312526.jpg';


$startPing;
$lastPing;

function ping($msg)
{
    global $startPing, $lastPing;
    $now = microtime(true);
    printf("\n\n# after %f <b>$msg</b> at %f \n<hr />\n", $now - $lastPing, $now - $startPing);
    $lastPing = $now;
}


$startPing = $lastPing = microtime(true);
ping('init');

$classPath = '../ImagePalette/src/Bfoxwell/ImagePalette/ImagePalette.php';
require_once($classPath);

ping('constructor start');

$palette = new Bfoxwell\ImagePalette\ImagePalette($img);

ping('constructor finished');

// basic getters
var_export($palette->colors);
ping('getColors finished');

var_export($palette->rgbColors);
ping('getRgbColors finished');

var_export($palette->hexStringColors);
ping('getHexStringColors finished');

var_export($palette->rgbStringColors);
ping('getRgbStringColors finished');

// string cast
var_export((string) $palette);
ping('__toString finished');

// color demonstration
foreach ($palette->getHexStringColors() as $color) {
    echo '<span style="display:inline-block;width:6em;height:2em;background-color:#'
        . $color
        . '"></span>';
}
echo "<br /><br />";
foreach ($palette->getRgbStringColors() as $color) {
    echo '<span style="display:inline-block;width:6em;height:2em;background-color:rgb'
        . $color
        . '"></span>';
}
echo "<br /><br />";

ping('preview finished');

// original image
echo '<br /><img src="' . $img . '" alt="" style="max-width:500px;max-height:300px" />';

?>
</pre>
