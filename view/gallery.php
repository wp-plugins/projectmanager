<?php
/**
Template page for dataset gallery

The following variables are usable:

	$datasets: contains all datasets for current selection
	$num_cols: number of colums (default: 3)
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php do_action('projectmanager_tablenav'); ?>

<?php if ( $datasets ) : ?>

<div class='dataset_gallery'>
	<div class='gallery-row'>
				
	<?php foreach ( $datasets AS $dataset ) : ?>
	<?php
		$i++;
		$url = get_permalink();
		$url = add_query_arg('show', $dataset->id, $url);
		$url = ($this->isCategory()) ? add_query_arg('cat_id', $this->getCatID(), $url) : $url;
								
		$before_name = '<a href="'.$url.'">';
		$after_name = '</a>';
					
		$width = floor(100/$num_cols);
	?>
	<div class='gallery-item' style='width: <?php echo $width ?>%;'>
		<?php if ( $dataset->image != '' ) : ?>
		<?php echo $before_name ?><img src="<?php echo parent::getImageUrl('/thumb.'.$dataset->image) ?>" alt="<?php echo $dataset->name ?>" title="<?php echo $dataset->name ?>" /><?php echo $after_name ?>;
	
		<p class='caption'><?php echo $before_name.$dataset->name.$after_name ?></p>
	</div>
	
	<?php if ( ( ( 0 == $i % $num_cols)) && ( $i < count($datasets) ) ) : ?>
	</div><div class='gallery-row'>
	<?php endif; ?>
			
	<?php endforeach; ?>
	</div>
</div>

<br style='clear: both;' />

<?php if ( !parent::isSearch() ) : ?>
<p class='page-numbers'><?php echo parent::getPageLinks() ?></p>
<?php endif; ?>

<?php else : ?>
<p class='error'><?php _e( 'Nothing found', 'projectmanager') ?></p>
<?php endif; ?>