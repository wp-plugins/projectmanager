<?php
/*
* Template Name: Default
* Version: 1.0
* Author: Kolja Schleich
* E-Mail: kolja.schleich@googlemail.com
*/
?>
<?php if ( projectmanager_is_single() ) : ?>
	<h3 style="clear: both;"><?php projectmanager_project_title() ?></h3>
	<p class="list"><?php projectmanager_list_return_link() ?></p>
	<?php if ( projectmanager_has_dataset() ) : ?>
	<fieldset><legend><?php projectmanager_dataset_name() ?></legend>
		<?php if ( projectmanager_dataset_has_image() ) : ?>
		<img src="<?php projectmanager_dataset_image() ?>" title="<?php projectmanager_dataset_name() ?>" alt="<?php projectmanager_dataset_name() ?>" style="float: right;" />
		<?php endif; ?>
		<dl><?php projectmanager_dataset_meta( 'dl' ) ?></dl>
	</fieldset>	
	<?php endif; ?>
<?php else : ?>
	<?php projectmanager_search_form( 'float: right; position: relative; top: 0.5em;' ); ?>
	
	<?php //if ( projectmanager_has_groups() ) projectmanager_groups_form(); ?>

	<?php if ( projectmanager_is_home() ) : ?>
		<h3 style="clear: both;"><?php projectmanager_project_title() ?></h3>
	<?php elseif ( projectmanager_is_search() ) : ?>
		<h3 style="clear: both;">Search Results ( <?php projectmanager_num_search_found() ?> of <?php projectmanager_num_total() ?>)</h3>
	<?php else : ?>
	<h3 style="clear: both;"><?php projectmanager_project_title()?> - <?php projectmanager_group_title() ?></h3>
	<?php endif; ?>
		
	<?php projectmanager_pagination() ?>
	
	<table class="projectmanager" summary="" title="">
		<thead>
			<tr>
				<th>Name</th>
				<?php projectmanager_table_header() ?>
			</tr>
		</thead>
		<tbody id="the-list">
			<?php if ( projectmanager_has_dataset() ) : ?>
			<?php projectmanager_datasets_table( true ) ?>
			<?php endif; ?>
		</tbody>
	</table>
	
	<?php projectmanager_pagination() ?>
<?php endif; ?>
