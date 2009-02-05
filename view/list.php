<?php
/**
Template page for dataset list

The following variables are usable:

	$datasets: contains all datasets for current selection
	$num_datasets: contains the number of datasets
	$output: contains the output format, either 'table', 'ul' or 'ol'
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php
if ( parent::isSearch() ) : ?>
<h3 style='clear:both;'><?php printf(__('Search: %d of %d', 'projectmanager'),  parent::getNumDatasets(parent::getProjectID()), $num_datasets) ?></h3>
<?php elseif ( parent::isCategory() ) : ?>
<h3 style='clear:both;'><?php echo parent::getCatTitle(parent::getCatID()) ?></h3>
<?php endif; ?>

<?php do_action('projectmanager_tablenav'); ?>
		
<?php if ( $datasets ) : ?>
<?php if ( 'table' == $output ) : ?>
<table class='projectmanager'>
<tr>
	<th scope='col'><?php _e( 'Name', 'projectmanager' ) ?></th>
	<?php parent::printTableHeader(); ?>
</tr>
<?php else :
	echo "\n<".$output." class='projectmanager'>\n";
endif; ?>
		
<?php foreach ( $datasets AS $dataset ) : ?>
	<?php
		$url = get_permalink();$url = add_query_arg('show', $dataset->id, $url);
		$url = (parent::isCategory()) ? add_query_arg('cat_id', parent::getCatID(), $url) : $url;
		$name = (parent::hasDetails()) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
		
		$class = ("alternate" == $class) ? '' : "alternate";
	?>
	<?php if ( 'table' == $output ) : ?>
		<tr class='<?php echo $class ?>'><td><?php echo $name ?></td><?php parent::printDatasetMetaData( $dataset, 'td' ) ?></tr>
	<?php else : ?>
		<li><?php echo $name ?><ul><?php parent::printDatasetMetaData( $dataset, 'li' ) ?></ul></li>
	<?php endif; ?>
<?php endforeach ; ?>		
<?php echo "\n</$output>\n"; ?>


	<?php if ( !parent::isSearch() ) : ?>
		<p class='page-numbers'><?php echo parent::getPageLinks() ?></p>
	<?php endif; ?>


<?php else : ?>
	<p class='error'><?php _e( 'Nothing found', 'projectmanager') ?></p>
<?php endif; ?>