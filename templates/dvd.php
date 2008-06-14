<?php
/*
* Template Name: DVD Database
* Version: 1.0
* Author: Kolja Schleich
* E-Mail: kolja.schleich@googlemail.com
*/
?>
<?php projectmanager_search_form( 'float: right; position: relative; top: 0.5em;' ); ?>

<?php if ( projectmanager_has_groups() ) : ?>
<form class="projectmanager" action="" method="get" onchange="this.submit()" style="float: left; margin-bottom: 2em;">
	<select size="1" name="grp_id">
		<option value="">Filmkategorie</option>
		<option value="">-------------</option>
		<?php projectmanager_groups_selections() ?>
	</select>
	<input type="submit" value="Los" />
</form>
<?php endif; ?>


<?php if ( projectmanager_is_home() ) : ?>
	<p style="text-align: left; margin: 2em 0; padding-left: 0; clear: both;">Es sind <?php projectmanager_num_total() ?> DVDs in <?php projectmanager_num_groups() ?> Kategorien in der Datenbank gespeichert!</p>
<?php else : ?>
	<?php if ( projectmanager_is_search() ) : ?>
		<h3 style="clear: both;">Suchergebnisse ( <?php projectmanager_num_search_found() ?> von <?php projectmanager_num_total() ?>)</h3>
	<?php else : ?>
		<h3 style="clear: both;">DVD <?php projectmanager_group_title() ?></h3>
	<?php endif; ?>
	<table class="projectmanager" summary="" title="">
		<thead>
			<tr>
				<th>DVD Title</th>
				<?php projectmanager_table_header() ?>
			</tr>
		</thead>
		<tbody id="the-list">
		<?php if ( projectmanager_has_dataset() ) : ?>
			<?php projectmanager_datasets_table( false ) ?>
		<?php endif; ?>
		</tbody>
	</table>
	
	<?php projectmanager_pagination() ?>
<?php endif; ?>