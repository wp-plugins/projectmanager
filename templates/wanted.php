<?php
/*
* Template Name: Wanted Posters
* Version: 1.0
* Author: Kolja Schleich
* E-Mail: kolja.schleich@googlemail.com
*/
?>

<!--<h3 style="clear: both;"><?php projectmanager_project_title()?></h3>-->
<?php if ( projectmanager_is_single() ) : ?>
	<p class="list"><?php projectmanager_list_return_link() ?></p>
	<?php if ( projectmanager_has_dataset() ) : ?>
		<fieldset><legend>Profile of <?php projectmanager_dataset_name() ?></legend>
			<?php if ( projectmanager_dataset_has_image() ) : ?>
				<img src="<?php projectmanager_dataset_image() ?>" title="Portrait of <?php projectmanager_dataset_name() ?>" alt="Portrait of <?php projectmanager_dataset_name() ?>" style="float: right; margin-right: 1.5em;" />
			<?php endif; ?>
			<?php projectmanager_dataset_meta( 'dl' ) ?>
		</fieldset>	
	<?php endif; ?>
<?php else : ?>
	<?php if ( projectmanager_has_groups() ) : ?>
	<form class="projectmanager" action="" method="get" onchange="this.submit()" style="float: left; margin-bottom: 2em;">
		<select size="1" name="grp_id">
			<option value="">Groups</option>
			<option value="">-------------</option>
			<?php projectmanager_groups_selections() ?>
		</select>
		<input type="submit" value="Los" />
	</form>
	<?php endif; ?>
	
	<?php projectmanager_pagination() ?>
	
	<?php if ( projectmanager_has_dataset() ) : ?>
		<table class="projectmanager" summary="" title="'.$options['plugin_title'].'">
			<tr>
				<?php projectmanager_gallery() ?>
			</tr>
		</table>
	<?php else : ?>
		<p class="error">Sorry, nothing could be found!</p>
	<?php endif; ?>
	
	<?php projectmanager_pagination() ?>
<?php endif; ?>