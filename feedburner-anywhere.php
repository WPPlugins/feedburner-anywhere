<?php
/*
Plugin Name: Feedburner Anywhere
Plugin URI: http://brandontreb.com/feedburner-anywhere
Description: Allows you to display your Feedburner subscriber count in a widget or in your posts without having to use Feedburner's not so beautiful icon :)
Version: 1.1
Author: Brandon Trebitowski
Author URI: http://brandontreb.com/


Copyright 2009  Brandon Trebitowski  (email : brandontreb@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/* 
 * Plugin Settings 
 */
$data = array(
	'fba_feedburner_url' => '',
	'fba_feedburer_subscribers' => 0);

add_option('fba_settings',$data,'fba_options');

$fba_settings = get_option('fba_settings');

/*
 * Admin menu
 */
add_action('admin_menu', 'fba_menu');

function fba_menu() {
  add_options_page('Feedburner Anywhere Options', 'Feedburner Anywhere', 8, __FILE__, 'fba_options');
}

function fba_options() {
	global $fba_settings, $wpdb;

	if(isset($_POST['fba_submit'])) {
		$fba_settings['fba_feedburner_url'] = $_POST['feedburner_url'];
		update_option('fba_settings',$fba_settings);
		
		$fba_message = "Successfully saved Feedburner settings.";
	}
	
	if( isset($_GET['fba_get_data'])) {
		fba_get_feedburner_subscriber_count();
	}
	
	if ($fba_message != '') echo '<div id="message" class="updated fade"><p>' . $fba_message . '</p></div>';
?>
	<div class="wrap" id="twitpop-options">
  	<h2>Custom Feedburner Anywhere Options</h2>
  	<form action="" method="post">
  		<p>Add the URL of your Feedburner RSS feed below. When you have done so, click on <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&fba_get_data=1">Get Feedburner Data</a> to get your current subscriber count. <small>(Example:http://feeds2.feedburner.com/brandontreb).</small> Make sure that you have Enabled the Feedburner Awareness API (<a href="http://brandontreb.com/displaying-you-feedburner-subscriber-count-anywhere-php-coding-tutorial/" target="_blank">How?</a>).</p>
		<table cellpadding="10" cellspacing="10">
			<tr>
				<td align="right" width="200">
	  				<label for="username">Subscriber Count:</label>
		  		</td>
		  		<td>
					<?php echo $fba_settings['fba_feedburer_subscribers'] ? $fba_settings['fba_feedburer_subscribers'] : "no data"; ?>
				</td>
			</tr>
			<tr>
				<td align="right" width="200">
	  				<label for="username">Next Update:</label>
		  		</td>
		  		<td>
					<?php echo date("D, M d, Y \a\\t g:i a",wp_next_scheduled('fba_cron')); ?>
				</td>
			</tr>
			<tr>
				<td align="right" width="200">
	  				<input type="hidden" name="redirect" value="true" />
	  				<label for="username">Feedburner URL:</label>
		  		</td>
		  		<td>
					<input type="text" name="feedburner_url" value="<?php echo $fba_settings['fba_feedburner_url']; ?>" style="width:250px;">
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="fba_submit" value="Save Settings" class="button-primary"> <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&fba_get_data=1">Get Feedburner Data</a></td>
			</tr>
		</table>
		</form>
	</div>
<?php	
}

/* Update the content tag [feedburner] with subscriber count */
add_filter('the_content','fba_subscribers');

function fba_subscribers($content) {
	global $fba_settings;
	$content = str_replace("[feedburner]",$fba_settings['fba_feedburer_subscribers'],$content);
	return $content;
}

/*
 * Cron to gather feedburner info
 */
register_activation_hook(__FILE__, 'fba_activation');
add_action('fba_cron', 'fba_get_feedburner_subscriber_count');

function fba_activation() {
	if (!wp_next_scheduled('fba_cron')) {
		wp_schedule_event(time(), 'hourly', 'fba_cron');
	}
}

// Grab subcscriber count daily
function fba_get_feedburner_subscriber_count() {
	global $fba_settings;

	$blog_name = explode("/",$fba_settings['fba_feedburner_url']);
	$blog_name = $blog_name[count($blog_name) - 1];

	$subscriberCount = 0;
	$fburl = 'http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=' . $blog_name;
	$ch 	= curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $fburl);
	$data = curl_exec($ch); 
	
	curl_close($ch);		
	if ($data) {
		preg_match('/circulation=\"([0-9]+)\"/',$data, $matches);
		if(count($matches) < 2) {
			$subscriberCount = 0;
		} 
		if ($matches[1] != 0) {
			$subscriberCount = $matches[1];
		}
	}
	
	if($subscriberCount > 0) {
		$fba_settings['fba_feedburer_subscribers'] = $subscriberCount;
		update_option('fba_settings',$fba_settings);
	}
	
	echo "<b>Feedburner Response:</b><br>";
	echo "<pre>";
	echo  htmlspecialchars($data);
	echo "</pre>";
}

register_deactivation_hook(__FILE__, 'fba_deactivation');

function fba_deactivation() {
	wp_clear_scheduled_hook('fba_cron');
}

/*
 * Widget Code
 */
 
 /**
 * cfbWidget Class
 */
class cfbWidget extends WP_Widget {
    /** constructor */
    function cfbWidget() {
        parent::WP_Widget(false, $name = 'Feedburner Count');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {	
    	global $fba_settings;
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $text = str_replace("%d",$fba_settings['fba_feedburer_subscribers'],$instance['text']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                  <?php echo $text; ?>                 
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $text = esc_attr($instance['text']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            
            <p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:'); ?> <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></label></p>
             <p>Type in %d in the location where you want to display your subscriber count.</p>
        <?php 
    }

} 
// register cfbWidget widget
add_action('widgets_init', create_function('', 'return register_widget("cfbWidget");'));

?>