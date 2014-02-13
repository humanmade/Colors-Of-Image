<pre>
<?php

// full error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$img = 'https://www.google.com/images/srpr/logo11w.png';
$img = 'http://wallpapers.wallbase.cc/rozne/wallpaper-2312526.jpg';
$img = 'http://thumbs.wallbase.cc//rozne/thumb-2312526.jpg';


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


var_export($palette->getPalette());
ping('getPalette finished');

var_export($palette->getRgbArraysPalette());
ping('getRgbArraysPalette finished');

var_export($palette->getHexStringPalette());
ping('getHexStringPalette finished');

var_export($palette->getRgbStringPalette());
ping('getRgbStringPalette finished');

echo $palette;
ping('__toString finished');

foreach ($palette->getHexStringPalette() as $color) {
    echo '<span style="display:inline-block;width:6em;height:2em;background-color:#'
        . $color
        . '"></span>';
}

echo '<br /><img src="' . $img . '" alt="" style="max-width:500px;max-height:300px" />';

ping('preview finished');

// –––––––––––––––––

var_export($palette->getColors());
ping('getColors finished');

?>
</pre>
