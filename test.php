<?php

include_once( 'colorsofimage.class.php' );

$image = isset( $_GET['image'] ) ? $_GET['image'] : null;


function display_colors( $colors ) {
	?>
	<div style="overflow: hidden">
		<?php foreach ( $colors as $color ) : ?>
			<?php display_color( $color ) ?>
		<?php endforeach; ?>
	</div>
	<?php
}

function display_color( $color ) {
	?>
	<div style="width: 20px; float: left; height: 20px; background-color: <?php echo $color ?>"></div>
	<?php
}

?>
<html>
	<head>
	</head>
	<body>
		<form>
			<input type="text" name="image" />
			<button type="submit">Submit</button>
		</form>

		<?php 
		if ( $image ) :
			$colors_of_image = new ColorsOfImage( $image );
			$colors = $colors_of_image->getProminentColors();
			?>

			<p><img src="<?php echo $image ?>" /></p>

			Colors of Image:
			<?php display_colors( $colors ); ?>

			<p>Background Color</p>
			<?php display_color( $colors_of_image->getBackgroundColor() ) ?>

			<p style="margin-top: 15px; clear: both">Color Map</p>
			<div>
				<?php foreach ( $colors_of_image->getColorMap() as $color_from => $color_to ) : ?>
					<div style="width: 90px; float: left; height: 25px;">
						<?php display_color( $color_from ) ?> <div style="float: left">&rarr;</div> <?php display_color( $color_to ) ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</body>
</html>