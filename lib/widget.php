<?php
/** Widget class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2009
*/

class ProjectManagerWidget extends ProjectManager
{

	/**
	 * initialize
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		$this->loadOptions();
	}
	function ProjectManagerWidget()
	{
		$this->__construct();
	}
	
	
	/**
	 * register widget
	 *
	 * @param none
	 */
	function register()
	{
		if (!function_exists('register_sidebar_widget')) {
			return;
		}
			
		foreach ( $this->getProjects() AS $project ) {
			$widget_ops = array('classname' => 'widget_projectmanager', 'description' => $project->title );
			wp_register_sidebar_widget( sanitize_title($project->title), $project->title, array(&$this, 'display'), $widget_ops );
			wp_register_widget_control( sanitize_title($project->title), $project->title, array(&$this, 'control'), array('width' => 250, 'height' => 100), array( 'project_id' => $project->id, 'widget_id' => sanitize_title($project->title) ) );
		}
	}
	
		
	/**
	 * load Options
	 *
	 * @param none
	 * @return void
	 */
	function loadOptions()
	{
		$this->options = get_option( 'projectmanager_widget' );
	}
	
	
	/**
	 * displays widget
	 *
	 * @param $args
	 *
	 */
	function display( $args )
	{
		global $wpdb;
		
		$options = get_option( 'projectmanager_widget' );
		$widget_id = $args['widget_id'];
		$project_id = $options[$widget_id]['project_id'];
		parent::initialize($project_id);
		
		$defaults = array(
			'before_widget' => '<li id="projectmanager" class="widget '.get_class($this).'_'.__FUNCTION__.'">',
			'after_widget' => '</li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
			'widget_title' => $options[$widget_id]['title'],
			'limit' => $options[$widget_id]['limit'],
			'slideshow' => ( 1 == $options[$widget_id]['slideshow']['show'] ) ? true : false,
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		
		
		$limit = ( 0 != $limit ) ? "LIMIT 0,".$limit : '';
		$datasets = $wpdb->get_results( "SELECT `id`, `name`, `image` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id} ORDER BY `id` DESC ".$limit." " ); 

		echo $before_widget . $before_title . $widget_title . $after_title;
		if ( $slideshow )
			echo '<div id="projectmanager_slideshow_'.$project_id.'" class="projectmanager_slideshow">';
		else
			echo "<ul class='projectmanager_widget'>";
				
		if ( $datasets ) {
			$url = get_permalink($options[$widget_id]['page_id']);
			foreach ( $datasets AS $dataset ) {
				$url = add_query_arg('show', $dataset->id, $url);
				$name = (parent::hasDetails()) ? '<a href="'.$url.'"><img src="'.parent::getImageUrl($dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" /></a>' : '<img src="'.parent::getImageUrl($dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" />';
				
				if ( $slideshow ) {
					if ( $dataset->image != '' )
						echo $name;
				} else
					echo "<li>".$name."</li>";
			}
		}
		if ( $slideshow )
			echo "</div>";
		else
			echo "</ul>";
		echo $after_widget;
	}
	
	
	/**
	 * widget control panel
	 *
	 * @param none
	 */
	function control( $args )
	{
		extract( $args );
		$options = get_option( 'projectmanager_widget' );
		
		if ($_POST['projectmanager-submit']) {
			$options[$widget_id]['project_id'] = $project_id;
			$options[$widget_id]['title'] = $_POST['widget_title'][$project_id];
			$options[$widget_id]['limit'] = $_POST['limit'][$project_id];
			$options[$widget_id]['page_id'] = $_POST['page_id'][$project_id];
			$options[$widget_id]['slideshow'] = array('show' => $_POST['projectmanager_slideshow'][$project_id], 'width' => $_POST['projectmanager_slideshow_width'][$project_id], 'height' => $_POST['projectmanager_slideshow_height'][$project_id], 'time' => $_POST['projectmanager_slideshow_time'][$project_id], 'fade' => $_POST['projectmanager_slideshow_fade'][$project_id], 'random' => $_POST['projectmanager_slideshow_order'][$project_id]);
				
			update_option( 'projectmanager_widget', $options );
		}
		
		echo '<div class="projectmanager_widget_control">';
		echo '<p><label for="widget_title">'.__('Title', 'projectmanager').'</label><input class="widefat" type="text" name="widget_title['.$project_id.']" id="widget_title" value="'.$options[$widget_id]['title'].'" /></p>';
		echo '<p><label for="limit">'.__('Display', 'projectmanager').'</label>&#160;<select style="margin-top: 0;" size="1" name="limit['.$project_id.']" id="limit">';
		$selected['show_all'] = ( $options[$widget_id]['limit'] == 0 ) ? " selected='selected'" : '';
		echo '<option value="0"'.$selected['show_all'].'>'.__('All','projectmanager').'</option>';
		for ( $i = 1; $i <= 10; $i++ ) {
		        $selected = ( $options[$widget_id]['limit'] == $i ) ? " selected='selected'" : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		echo '</select></p>';
		echo '<p><label for="page_id['.$project_id.']">'.__('Page','projectmanager').'</label>&#160;'.wp_dropdown_pages(array('name' => 'page_id['.$project_id.']', 'selected' => $options[$widget_id]['page_id'], 'echo' => 0)).'</p>';
		echo '<fieldset class="slideshow_control"><legend>'.__('Slideshow','projectmanager').'</legend>';
		$checked = ($options[$widget_id]['slideshow']['show'] == 1) ? ' checked="checked"' : '';
		echo '<p><input type="checkbox" name="projectmanager_slideshow['.$project_id.']" id="projectmanager_slideshow" value="1"'.$checked.' style="margin-left: 0.5em;" />&#160;<label for="projectmanager_slideshow" class="right">'.__( 'Use Slideshow', 'projectmanager' ).'</label></p>';
		echo '<p><label for="projectmanager_slideshow_width">'.__( 'Width', 'projectmanager' ).'</label><input type="text" size="3" name="projectmanager_slideshow_width['.$project_id.']" id="projectmanager_slideshow_width" value="'.$options[$widget_id]['slideshow']['width'].'" class="widefat" style="display: inline; clear: none; width: auto;" /> px</p>';
		echo '<p><label for="projectmanager_slideshow_height">'.__( 'Height', 'projectmanager' ).'</label><input type="text" size="3" name="projectmanager_slideshow_height['.$project_id.']" id="projectmanager_slideshow_height" value="'.$options[$widget_id]['slideshow']['height'].'" class="widefat" style="display: inline; clear: none; width: auto;" /> px</p>';
		echo '<p><label for="projectmanager_slideshow_time">'.__( 'Time', 'projectmanager' ).'</label><input type="text" name="projectmanager_slideshow_time['.$project_id.']" id="projectmanager_slideshow_time" size="1" value="'.$options[$widget_id]['slideshow']['time'].'" class="widefat" style="display: inline; clear: none; width: auto;" /> '.__( 'seconds','projectmanager').'</p>';
		echo '<p><label for="projectmanager_slideshow_fade">'.__( 'Fade Effect', 'projectmanager' ).'</label>'.$this->getSlideshowFadeEffects($options[$widget_id]['slideshow']['fade'], $project_id).'</p>';
		echo '<p><label for="projectmanager_slideshow_order">'.__('Order','projectmanager').'</label>'.$this->getSlideshowOrder($options[$widget_id]['slideshow']['random'], $project_id).'</p>';
		echo '</fieldset>';
	
		echo '<input type="hidden" name="projectmanager-submit" value="1" />';
		echo '</div>';
	}
	
	
	/**
	* dropdown list of available fade effects
	*
	* @param string $selected
	* @param int $project_id
	* @return string
	*/
	function getSlideshowFadeEffects( $selected, $project_id )
	{
		$effects = array(__('Fade','projectmanager') => 'fade', __('Zoom Fade','projectmanager') => 'zoomFade', __('Scroll Up','projectmanager') => 'scrollUp', __('Scroll Left','projectmanager') => 'scrollLeft', __('Scroll Right','projectmanager') => 'scrollRight', __('Scroll Down','projectmanager') => 'scrollDown', __( 'Zoom','projectmanager') => 'zoom', __('Grow X','projectmanager') => 'growX', __('Grow Y','projectmanager') => 'growY', __('Zoom BR','projectmanager') => 'zoomBR', __('Zoom TL','projectmanager') => 'zoomTL', __('Random','projectmanager') => 'random');
		
		$out = '<select size="1" name="projectmanager_slideshow_fade['.$project_id.']" id="projectmanager_slideshow_fade">';
		foreach ( $effects AS $name => $effect ) {
			$checked =  ( $selected == $effect ) ? " selected='selected'" : '';
			$out .= '<option value="'.$effect.'"'.$checked.'>'.$name.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * dropdown list of Order possibilites
	 *
	 * @param string $selected
	 * @param int $project_id
	 * @return string
	 */
	function getSlideshowOrder( $selected, $project_id )
	{
		$order = array(__('Ordered','projectmanager') => 'false', __('Random','projectmanager') => 'true');
		$out = '<select size="1" name="projectmanager_slideshow_order['.$project_id.']" id="projectmanager_slideshow_order">';
		foreach ( $order AS $name => $value ) {
			$checked =  ( $selected == $value ) ? " selected='selected'" : '';
			$out .= '<option value="'.$value.'"'.$checked.'>'.$name.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * gets all widgedized projects
	 *
	 * @param none
	 * @return array
	 */
	function getProjects()
	{
		global $wpdb;
		$options = get_option( 'projectmanager' );
		
		$projects = parent::getProjects();
		
		$widget_projects = array();
		foreach ( $projects AS $project ) {
			if ( 1 == $options['project_options'][$project->id]['use_widget'] )
				$widget_projects[] = $project;
		}
		return $widget_projects;
	}
}

?>