<?php
/**
Template page for petition landing page

The following variables are usable:

	$project: contains data for the project
	$datasets: contains all datasets for current selection
	$pagination: contains the pagination
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>

<style type="text/css">
/*--- Petition Landingpage ---*/
div#projectmanager_petition_landingpage {
}
div#projectmanager_petition_landingpage div.petition_landingpage {
	clear: both;
	margin-bottom: 2em;
}
div#projectmanager_petition_landingpage ul {
	margin-left: 0;
	list-style-type: none;
}
div#projectmanager_petition_landingpage ul li {
	background-color: white;
	border: 2px solid #efefef;
	padding: 0.5em;
	border-radius: 10px;
	opacity: 1;
	margin: 0.3em 0.3em 0.3em 0;
	float: left;
}
div#projectmanager_petition_landingpage ul li p {
	margin: 0.2em;
}
div#projectmanager_petition_landingpage ul li p.comment {
	font-style: italic;
}
div#projectmanager_petition_landingpage .num_supporters {
	font-size: 1.5em;
	font-weight: bold;
	text-align: center;
	clear: both;
	margin-top: 1em;
}
div#projectmanager_petition_landingpage .num_supporters .signpetition {
	font-weight: normal;
}
</style>

<div id="projectmanager_petition_landingpage">

<?php if (!empty($title)) : ?>
<h2><?php echo $title ?></h2>
<?php endif; ?>

<?php if ( $datasets ) : $i = 0; ?>
<div class='petition_landingpage'>
	<ul>
	<?php foreach ( $datasets AS $dataset ) : $i++; ?>
		<li style="width: <?php echo (100/$ncol)-(0.10*100/$ncol) ?>%;">
			<p class='comment'>&ldquo;<?php echo $dataset->comment ?>&rdquo;</p>
			<p class='cite'><?php echo $dataset->name ?> - <?php echo $dataset->city ?>,<?php echo $dataset->country ?></p>
			<!--<img class="supporter" src="<?php echo $dataset->thumbURL ?>" alt="<?php echo $dataset->name ?>, <?php echo $dataset->country ?>" data-container="body" data-toggle="popover" data-placement="auto" data-html="true" data-content="<q><?php echo $dataset->comment ?></q><cite><?php echo $dataset->name ?> - <?php echo $dataset->city ?>,<?php echo $dataset->country ?></cite>" />-->
			<?php if ( 0 == $i % $ncol ) : ?>
			<br style="clear: both;" />
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<br style="clear: both;" />
<p class="num_supporters">
	<?php echo $project->num_datasets ?> <?php _e('Supporters Already.', 'projectmanager') ?>
	<?php if ($sign_petition_href != "") : ?>
	<span class="signpetition"><a href="<?php echo $sign_petition_href ?>"> <?php _e('Become part of our cause.', 'projectmanager') ?></a></span>
	<?php endif; ?>
</p>
<?php endif; ?>

</div>