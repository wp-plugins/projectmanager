<?php
/**
Template page for dataset gallery

The following variables are usable:

	$gallery: contains data for the gallery
	$datasets: contains all datasets for current selection
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php if ( isset( $_GET['show'] ) ) : ?>
	<?php do_action('projectmanager_dataset', array('id' => $_GET['show'], 'echo' => 1)) ?>
<?php else: ?>
	
<?php do_action('projectmanager_tablenav'); ?>

<?php if ( $datasets ) : $i = 0; ?>

<div class='dataset_gallery'>
	<?php foreach ( $datasets AS $dataset ) : $i++; ?>
	
	<div class='gallery-item' style='width: <?php echo $gallery['dataset_width'] ?>;'>
		<div class="gallery-image">
			<?php if ( $dataset->image != '' ) : ?>
			<a href="<?php echo $dataset->URL ?>"><img src="<?php echo $dataset->thumbURL ?>" alt="<?php echo $dataset->name ?>" title="<?php echo $dataset->name ?>" /></a>;
			<?php endif; ?>
	
			<p class='caption'><a href="<?php echo $dataset->URL ?>"><?php echo $dataset->name ?></a></p>
		</div>
	</div>
	
	<?php if ( 0 == $i % $gallery['num_cols'] ) : ?>
	<br style="clear: both;" />
	<?php endif; ?>

	<?php endforeach; ?>
	</div>
</div>

<br style='clear: both;' />

<p class='page-numbers'><?php echo $pagination ?></p>

<?php else : ?>
<p class='error'><?php _e( 'Nothing found', 'projectmanager') ?></p>
<?php endif; ?>

<?php endif; ?>