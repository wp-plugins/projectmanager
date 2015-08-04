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
	padding: 0.5em;
	opacity: 1;
	margin: 0.3em 0.3em 0.3em 0;
	float: left;
}
div#testimonials-intro ul li p {
	margin: 0.2em;
}
div#testimonials-intro ul li p.comment {
	background-color: #efefef;
	color: #000000;
	padding: 0.3em;
	font-style: italic;
}
div#testimonials-intro ul li img {
	float: left;
	border-radius: 200px;
}
div#testimonials-intro div.supporter {
	margin-top: 0.5em;
}
div#testimonials-intro ul li p.cite, div#testimonials-intro ul li p.location {
	margin: 0em;
	margin-left: 70px;
}
div#testimonials-intro .num_supporters {
	font-size: 1.5em;
	font-weight: bold;
	text-align: center;
	clear: both;
	margin-top: 1em;
}
div#testimonials-intro .num_supporters .join, div#testimonials-intro .num_supporters .testimonials-list {
	font-weight: normal;
}
</style>

<div id="testimonials-intro">

<?php if ( $datasets ) : $i = 0; ?>
<div class='testimonials-intro'>
	<ul>
	<?php foreach ( $datasets AS $dataset ) : $i++; ?>
		<li style="width: <?php echo (100/$ncol)-(0.10*100/$ncol) ?>%;">
			<div class="testimonial">
				<p class='comment'>&ldquo;<?php echo $dataset->comment ?>&rdquo;</p>
				<div class='supporter'>					
					<?php if ($project->show_image == 1 && !empty($dataset->image)) : ?>
					<img src="<?php echo $projectmanager->getFileURL('tiny.'.$dataset->image)?>" />
					<?php endif; ?>
					
					<p class="cite"><?php echo $dataset->name ?></p>
					<p class="location"><?php echo $dataset->city ?>, <?php echo $dataset->country ?></p>
				<!--<img class="supporter" src="<?php echo $dataset->thumbURL ?>" alt="<?php echo $dataset->name ?>, <?php echo $dataset->country ?>" data-container="body" data-toggle="popover" data-placement="auto" data-html="true" data-content="<q><?php echo $dataset->comment ?></q><cite><?php echo $dataset->name ?> - <?php echo $dataset->city ?>,<?php echo $dataset->country ?></cite>" />-->
				</div>
			</div>
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
	<?php if ($list_page_href != "") : ?>
	<span class="testimonials-list"><a href="<?php echo $list_page_href ?>"> <?php _e('List of supporters.', 'projectmanager') ?></a></span>
	<?php endif; ?>
</p>
<?php endif; ?>

</div>