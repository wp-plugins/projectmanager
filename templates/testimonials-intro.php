<?php
/**
Template page for testimonials intro box

The following variables are usable:

	$project: contains data for the project
	$datasets: contains all datasets for current selection
	$pagination: contains the pagination
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>

<style type="text/css">
/*--- Testimonials Intro ---*/
div#testimonials-intro {
}
div#testimonials-intro div.testimonials-intro {
	clear: both;
	margin-bottom: 2em;
}
div#testimonials-intro ul {
	margin-left: 0;
	list-style-type: none;
}
div#testimonials-intro ul li {
	background-color: white;
	border: 2px solid #efefef;
	padding: 0.5em;
	border-radius: 10px;
	opacity: 1;
	margin: 0.3em 0.3em 0.3em 0;
	float: left;
}
div#testimonials-intro ul li p {
	margin: 0.2em;
}
div#testimonials-intro ul li p.comment {
	font-style: italic;
}
div#testimonials-intro .num_supporters {
	font-size: 1.5em;
	font-weight: bold;
	text-align: center;
	clear: both;
	margin-top: 1em;
}
div#testimonials-intro .num_supporters .join {
	font-weight: normal;
}
</style>

<div id="testimonials-intro">

<?php if ( $datasets ) : $i = 0; ?>
<div class='testimonials-intro'>
	<ul>
	<?php foreach ( $datasets AS $dataset ) : $i++; ?>
		<li style="width: <?php echo (100/$ncol)-(0.10*100/$ncol) ?>%;">
			<p class='comment'>&ldquo;<?php echo $dataset->comment ?>&rdquo;</p>
			<p class='cite'><?php echo $dataset->name ?> - <?php echo $dataset->city ?>, <?php echo $dataset->country ?></p>
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
	<span class="join"><a href="<?php echo $sign_petition_href ?>"> <?php _e('Become part of our cause.', 'projectmanager') ?></a></span>
	<?php endif; ?>
</p>
<?php endif; ?>

</div>