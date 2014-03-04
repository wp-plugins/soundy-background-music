<?php
/**
 * @package Soundy
 * @version 1.0
 */
/*
Plugin Name: Soundy Background Music
Plugin URI: http://webartisan.ch/en/products/soundy
Description: This plugin allows administrators and authors to set a background sound on any post or page.
Version: 1.0
Author: Bertrand du Couédic
Author URI: http://webartisan.ch/en/about
License: GPL2

Copyright 2014 Bertrand du Couédic  (email: bducouedic@webartisan.ch)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class WarSoundy 
{
	private $post_id; 
	private $default_audio_url        = '/soundy-background-music/audio/danse_russe.mp3';
	private $default_audio_title      = 'Danse Russe';
	private $default_play_button_url  = '/soundy-background-music/images/buttons/48x48/play-square-grey.png';
	private $default_play_hover_url   = '/soundy-background-music/images/buttons/48x48/play-square-blue.png';
	private $default_pause_button_url = '/soundy-background-music/images/buttons/48x48/pause-square-grey.png';
	private $default_pause_hover_url  = '/soundy-background-music/images/buttons/48x48/pause-square-blue.png';

	private	$units = array( 
				               		'px' => '(pixels)', 
				                	'%'  => '(percentage)', 
				                	'in' => '(inches)',
				                	'mm' => '(millimeters)',
				                	'cm' => '(centimeters)'
				                );

	public function __construct()  
	{		
		$this->default_audio_url        = WP_PLUGIN_URL . $this->default_audio_url;
		$this->default_play_button_url  = WP_PLUGIN_URL . $this->default_play_button_url;
		$this->default_play_hover_url   = WP_PLUGIN_URL . $this->default_play_hover_url;
		$this->default_pause_button_url = WP_PLUGIN_URL . $this->default_pause_button_url;
		$this->default_pause_hover_url  = WP_PLUGIN_URL . $this->default_pause_hover_url;

		if( is_admin() )
		{
			register_activation_hook( __FILE__, array( $this, 'activate' ) ); 
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	
			add_action( 'admin_menu', array( $this, 'add_plugin_settings_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) ); 
			
			$plugin = plugin_basename( __FILE__ ); 
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ) );
			
			$uri = $_SERVER[ 'REQUEST_URI' ];
			$is_edit_post =  ( strpos( $uri, '/wp-admin/post.php' ) == 0 ) ||
		      						 ( strpos( $uri, '/wp-admin/post-new.php' ) == 0 );
		  
		  if( ( isset( $_GET['page'] ) && ( $_GET['page'] == 'soundy' ) ) || $is_edit_post )
		  {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
				add_filter( 'get_media_item_args', array( $this, 'get_media_item_args' ) );
			}
			
			if( $is_edit_post )
			{
				add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
				add_action( 'save_post', array( $this, 'save_post_data' ) );
			}
		}
		else
		{
			wp_register_style( 'soundy', WP_PLUGIN_URL . '/soundy-background-music/css/style-front-end.css' );		
			wp_enqueue_style( 'soundy');
			add_action( 'wp_head', array( $this, 'insert_audio' ) );
			add_shortcode( 'soundy', array( $this, 'soundy_shortcode' ) );
		}
	}  

	public function activate() 
	{
			add_option( 'war_soundy_enable_bg_sound',    'no' ); 
			add_option( 'war_soundy_audio_file_url',     $this->default_audio_url ); 
			add_option( 'war_soundy_audio_title',        $this->default_audio_title ); 
			add_option( 'war_soundy_autoplay',           'yes' ); 
			add_option( 'war_soundy_loop',               'yes' ); 
			add_option( 'war_soundy_display_play_pause', 'yes' ); 
			add_option( 'war_soundy_url_play_button',    $this->default_play_button_url ); 
			add_option( 'war_soundy_url_play_hover',     $this->default_play_hover_url ); 
			add_option( 'war_soundy_url_pause_button',   $this->default_pause_button_url ); 
			add_option( 'war_soundy_url_pause_hover',    $this->default_pause_hover_url ); 
			add_option( 'war_soundy_pp_position',        'document' ); 
			add_option( 'war_soundy_pp_corner',          'upper_right' ); 
			add_option( 'war_soundy_offset_x',           '30' ); 
			add_option( 'war_soundy_offset_x_unit',      'px' ); 
			add_option( 'war_soundy_offset_y',           '30' ); 
			add_option( 'war_soundy_offset_y_unit',      'px' ); 
	}
	
	public function deactivate() 
	{
	}

	public function add_plugin_settings_menu() 
	{ 
		add_options_page( 'Soundy', 'Soundy', 'manage_options', 'soundy', array( $this, 'create_plugin_settings_page' ) ); 
	}
	
	public function create_plugin_settings_page() 
	{ 
		if( ! current_user_can( 'manage_options' ) ) 
		{ 
			wp_die( __('You do not have sufficient permissions to access this page.' ) );
	  } 
	  
	  include( sprintf( "%s/templates/settings.php", dirname( __FILE__ ) ) ); 
	}
	
	public function get_media_item_args( $args )
	{
		$args[ 'send' ] = true;
		return $args;
	}
	
	public function admin_scripts( $hook ) 
	{
		wp_register_script( 'button-upload', 
		                    WP_PLUGIN_URL . '/soundy-background-music/js/back-end.js', 
		                    array('jquery','media-upload','thickbox'));

		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'jquery-ui-core');
		wp_enqueue_script( 'jquery-ui-tabs');
		wp_enqueue_script( 'media-upload');
		wp_enqueue_script( 'thickbox');
		wp_enqueue_script( 'button-upload');
		
		wp_register_style( 'jquery-ui', WP_PLUGIN_URL . '/soundy-background-music/css/jquery-ui-v1.10.4.css' );
		wp_register_style( 'soundy', WP_PLUGIN_URL . '/soundy-background-music/css/style-back-end.css' );
		
		wp_enqueue_style( 'jquery-ui');
		wp_enqueue_style( 'thickbox');
		wp_enqueue_style( 'soundy');
	}
	
	public function plugin_settings_link( $links ) 
	{ 
		$settings_link = '<a href="options-general.php?page=soundy">Settings</a>'; 
		array_unshift( $links, $settings_link ); 
		return $links; 
	}
	
	public function get_audio_type_from_URL( $url )
	{
		$file_extension = pathinfo( $url, PATHINFO_EXTENSION );
		$audio_type = '';
	
		switch( $file_extension )
		{
			case 'mp3':
			case 'mpg':
			case 'mpeg':
				$audio_type = 'mpeg';
				break;
			case 'ogg':
				$audio_type = 'ogg';
				break;
			case 'wav':
			case 'wave':
				$audio_type = 'wav';
				break;
		}
		
		return $audio_type;
	}

	public function do_settings_section( $page_id, $section_id )
	{
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_fields ) ||
				 ! isset( $wp_settings_fields[ $page_id ] ) ||
				 ! isset( $wp_settings_fields[ $page_id ][ $section_id ] ) ) 
			return;
		
		$section = $wp_settings_sections[ $page_id ][ $section_id ];
		if ( $section[ 'title' ] )
		echo "<h3>{$section[ 'title' ]}</h3>\n";

		if ( $section[ 'callback' ] )
			call_user_func( $section[ 'callback' ], $section );

		echo '<table class="form-table">';
		do_settings_fields( $page_id, $section_id );
		echo '</table>';
	}

	public function register_settings()
	{    
		$this->add_settings_section_audio_track();
		$this->add_settings_section_play_pause_button();
		$this->add_settings_section_play_pause_position_corner();
		$this->add_settings_section_play_pause_position_static();
	}

	public function add_settings_section_audio_track()
	{
		add_settings_section(
    	'war_soundy_settings_section_audio_track',
      'Audio Track Options',                 
      array( $this, 'display_settings_section_audio_track_header' ),
      'soundy'
    );
    
		register_setting( 'war_soundy', 'war_soundy_enable_bg_sound' ); 
    add_settings_field( 
	    'war_soundy_enable_bg_sound',
	    'Enable Background Sound',
	    array( $this, 'add_settings_field_enable_bg_sound' ),
	    'soundy',                       
	    'war_soundy_settings_section_audio_track'
		);
    
		register_setting( 'war_soundy', 'war_soundy_audio_file_url', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_audio_file_url',
	    'Audio File URL',
	    array( $this, 'add_settings_field_audio_file_URL' ),
	    'soundy',                       
	    'war_soundy_settings_section_audio_track'
		);
    
		register_setting( 'war_soundy', 'war_soundy_audio_title', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_audio_title',
	    'Audio Title',
	    array( $this, 'add_settings_field_audio_title' ),
	    'soundy',                       
	    'war_soundy_settings_section_audio_track'
		);
    
		register_setting( 'war_soundy', 'war_soundy_autoplay' ); 
    add_settings_field( 
	    'war_soundy_autoplay',
	    'Autoplay',
	    array( $this, 'add_settings_field_autoplay' ),
	    'soundy',                       
	    'war_soundy_settings_section_audio_track'
		);
    
		register_setting( 'war_soundy', 'war_soundy_loop' ); 
    add_settings_field( 
	    'war_soundy_loop',
	    'Audio Repeat Loop',
	    array( $this, 'add_settings_field_loop' ),
	    'soundy',                       
	    'war_soundy_settings_section_audio_track'
		);
	}
	
	public function add_settings_section_play_pause_button()
	{
		add_settings_section(
    	'war_soundy_settings_section_play_pause_button',
      'Play/Pause Button',                 
      array( $this, 'display_settings_section_play_pause_button_header' ),
      'soundy'
    );
    
		register_setting( 'war_soundy', 'war_soundy_display_play_pause' ); 
    add_settings_field( 
	    'war_soundy_display_play_pause',
	    'Display Play/Pause Button',
	    array( $this, 'add_settings_field_display_play_pause_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button'
		);

		register_setting( 'war_soundy', 'war_soundy_url_play_button', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_play_button',
	    'Play Button Image URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'play_button' )
		);

		register_setting( 'war_soundy', 'war_soundy_url_play_hover', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_play_hover',
	    'Play Hover Image URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'play_hover' )
		);

		register_setting( 'war_soundy', 'war_soundy_url_pause_button', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_pause_button',
	    'Pause Button Image URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'pause_button' )
		);

		register_setting( 'war_soundy', 'war_soundy_url_pause_hover', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_pause_hover',
	    'Pause Hover Image URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'pause_hover' )
		);
	}
		
	public function add_settings_section_play_pause_position_corner()
	{
		add_settings_section(
    	'war_soundy_settings_section_play_pause_position_corner',
      'Play/Pause Corner Position',                 
      array( $this, 'display_settings_section_play_pause_position_corner_header' ),
      'soundy'
    );
    
		register_setting( 'war_soundy', 'war_soundy_pp_position' ); 
		register_setting( 'war_soundy', 'war_soundy_pp_corner' ); 
    add_settings_field( 
	    'war_soundy_pp_corner',
	    'Corner Position',
	    array( $this, 'add_settings_field_pp_position' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_position_corner'
		);

		register_setting( 'war_soundy', 'war_soundy_offset_x' ); 
		register_setting( 'war_soundy', 'war_soundy_offset_x_unit' ); 
    add_settings_field( 
	    'war_soundy_offset_x',
	    'Button X Offset',
	    array( $this, 'add_settings_field_offset_x' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_position_corner'
		);

		register_setting( 'war_soundy', 'war_soundy_offset_y' ); 
		register_setting( 'war_soundy', 'war_soundy_offset_y_unit' ); 
    add_settings_field( 
	    'war_soundy_offset_y',
	    'Button Y Offset',
	    array( $this, 'add_settings_field_offset_y' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_position_corner'
		);
	}
		
	public function add_settings_section_play_pause_position_static()
	{
		add_settings_section(
    	'war_soundy_settings_section_play_pause_position_static',
      'Play/Pause Static Position',                 
      array( $this, 'display_settings_section_play_pause_position_static_header' ),
      'soundy'
    );
    
    add_settings_field( 
	    'war_soundy_template_tags',
	    'Template Tags',
	    array( $this, 'add_settings_field_template_tags' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_position_static'
		);

    add_settings_field( 
	    'war_soundy_shortcodes',
	    'Shortcodes',
	    array( $this, 'add_settings_field_shortcodes' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_position_static'
		);
	}

	public function display_settings_section_audio_track_header() 
	{
	    echo '';
	}
		
	public function display_settings_section_play_pause_button_header() 
	{
	    echo '';
	}
		
	public function display_settings_section_play_pause_position_corner_header() 
	{
	    echo '';
	}
		
	public function display_settings_section_play_pause_position_static_header() 
	{
	    echo '';
	}
	
	public function add_settings_field_enable_bg_sound( $args ) 
	{
		?>
		<input type="checkbox" 
		       value="yes"
		       name="war_soundy_enable_bg_sound" 
		       id="war_soundy_enable_bg_sound"
		       <?php echo get_option( 'war_soundy_enable_bg_sound' ) == 'yes' ? ' checked' : ''; ?> />
		<label for="war_soundy_enable_bg_sound">Enable background sound per default</label>	
		<?php     
	}

	public function add_settings_field_audio_file_URL( $args ) 
	{
		$file_url  = get_option( 'war_soundy_audio_file_url' );
		$file_type = pathinfo( $file_url, PATHINFO_EXTENSION );
		
		$this->add_field_audio_file_URL_table( $file_url, $file_type, $this->default_audio_url, $this->default_audio_title );
	}

	private function add_field_audio_file_URL_table( $file_url, $file_type, $default_url, $default_title ) 
	{
		$default_title = str_replace( "&#039;", "\&#039;", $default_title );
		?>
		<script>
			war_bindMediaUploader( 'war_soundy_audio_file_url', 'war_audio_library_button', 'audio' );
		</script>
		<table class="war_soundy_no_border">
			<tr>
				<td>
					<input id="war_soundy_audio_file_url" 
					       name="war_soundy_audio_file_url" 
					       type="text" 
					       size="70%" 
					       value="<?php echo $file_url; ?>"
					       style="direction: rtl;"
					       onchange="war_audioUrlChanged( this );" />
				</td>
				<td>
		      <input id="war_audio_library_button" 
		             type="button" 
		             value="Media Library" 
		             class="war_soundy" />
  			</td>
			</tr>
			<tr>
				<td>
					<audio id="war_soundy_audio_player" 
						     class="war_soundy"
						     controls>
						<source id="war_soundy_audio_player_source"
							      src="<?php echo $file_url; ?>" 
							      type="audio/<?php echo $file_type; ?>">
				  </audio>
				</td>
		    <td style="text-align: right">
		    	<a href="#" 
		    		 onclick="war_setDefaultAudioURL( '<?php echo $default_url; ?>', '<?php echo $default_title; ?>' );"
		      >Default Audio</a>&nbsp;
		    </td>
			</tr>
		</table>
		<?php     
	}
	
	public function add_settings_field_audio_title( $args )
	{
		?>
		<input type="text"
					 size="70%"
		       value="<?php echo get_option( 'war_soundy_audio_title' ); ?>"
		       name="war_soundy_audio_title" 
		       id="war_soundy_audio_title" />
		<?php     
	}

	public function add_settings_field_autoplay( $args ) 
	{
		?>
		<input type="checkbox" 
		       value="yes"
		       name="war_soundy_autoplay" 
		       id="war_soundy_autoplay"
		       <?php echo get_option( 'war_soundy_autoplay' ) == 'yes' ? ' checked' : ''; ?> />
		<?php     
	}

	public function add_settings_field_loop( $args ) 
	{
		?>
		<input type="checkbox" 
		       value="yes"
		       name="war_soundy_loop" 
		       id="war_soundy_loop"
		       <?php echo get_option( 'war_soundy_loop' ) == 'yes' ? ' checked' : ''; ?> />
		<?php     
	}
	
	public function add_settings_field_display_play_pause_button( $args )
	{
		?>
		<input type="checkbox" 
		       value="yes"
		       name="war_soundy_display_play_pause" 
		       id="war_soundy_display_play_pause"
		       <?php echo get_option( 'war_soundy_display_play_pause' ) == 'yes' ? ' checked' : ''; ?> />
		<?php     
	}
	
	public function add_settings_field_url_pp_button( $args )
	{
		$type = $args[ 0 ];
		?>
		<script>
			war_bindMediaUploader( 'war_soundy_url_<?php echo $type; ?>', 
			                       'img_<?php echo $type; ?>_library_button', 'image' );
		</script>
		<table class="war_soundy_no_border">
			<tr>
				<td>
					<input id="war_soundy_url_<?php echo $type; ?>" 
					       name="war_soundy_url_<?php echo $type; ?>" 
					       type="text" 
					       size="70%" 
					       value="<?php echo get_option( "war_soundy_url_$type" ); ?>"
					       style="direction: rtl;"
					       onchange="war_imgUrlChanged( this );" />
    		</td>
    		<td>
		      <input id="img_<?php echo $type; ?>_library_button" 
		             type="button" 
		             value="Media Library" 
		             class="war_soundy" />
    		</td>
    	</tr>
    	<tr>
    		<td>
		      <img id="war_soundy_url_<?php echo $type; ?>_img" 
		           src="<?php echo get_option( "war_soundy_url_$type" ); ?>"
		           class="war_soundy">
		    </td>
		    <td style="text-align: right">
		    	<a href="#" 
		    		 onclick="war_setDefaultButtonURL( '<?php echo $type; ?>', '<?php echo $this->{"default_${type}_url"}; ?>' );"
		      >Default Button</a>&nbsp;
		    </td>
		  </tr>
		</table>
		<?php     
	}
	
	public function add_settings_field_pp_position( $args )
	{
		$pp_position = get_option( 'war_soundy_pp_position' );
		$pp_corner   = get_option( 'war_soundy_pp_corner' );
		
		$pp_comment = $pp_position == 'document' ? '(absolute position)' : '(fixed position)';
		
		$positions = array( 
												 document  => 'Document',
												 window    => 'Window'
											);
		$options_position = '';
		foreach( $positions as $position_id => $position_desc )
		{
   		$options_position .= '<option value="' . $position_id. '" ' . 
   		                     ( $position_id == $pp_position ? 'selected' : '' ) . '>' . $position_desc . '</option>';
		}		

		$corners = array( 
											upper_right  => 'Upper Right Corner',
											bottom_right => 'Bottom Right Corner',
											upper_left   => 'Upper Left Corner', 
											bottom_left  => 'Bottom Left Corner'
										);
		$options_corner = '';
		foreach( $corners as $corner_id => $corner_desc )
		{
   		$options_corner .= '<option value="' . $corner_id. '" ' . 
   		                   ( $corner_id == $pp_corner ? 'selected' : '' ) . '>' . $corner_desc . '</option>';
		}		
		?>
		<select name="war_soundy_pp_position"
			      onchange="war_ppPositionChanged( this )">
			<?php echo $options_position; ?>
		</select>
		<select name="war_soundy_pp_corner">
			<?php echo $options_corner; ?>
		</select>
		<span id="war_soundy_pp_comment"><?php echo $pp_comment; ?></span>
		<?php     
	}
	
	public function add_settings_field_offset_x( $args )
	{
		$unit_x = get_option( 'war_soundy_offset_x_unit' );
    $unit_options_x = '';
    $unit_comment_x = '(pixels)';
    foreach( $this->units as $unit => $comment )
    {
    	if( $unit == $unit_x )
    	{
    		$unit_options_x .= '<option selected>' . $unit . '</option>';
    		$unit_comment_x = $comment;
    	}
    	else
    	{
    		$unit_options_x .= '<option>' . $unit . '</option>';
    	}
    }									
		?>
		<input type="text" 
		       name="war_soundy_offset_x" 
		       id="war_soundy_offset_x" 
		       value="<?php echo get_option( 'war_soundy_offset_x' ); ?>"
		       size="4" />
		<select name="war_soundy_offset_x_unit"
			      onchange="war_lengthUnitChanged( this )">
			<?php echo $unit_options_x; ?>
		</select>
		<span id="war_soundy_unit_comment_x"><?php echo $unit_comment_x; ?></span>
		Horizontal length between button and vertical corner edge
		<?php     
	}
	
	public function add_settings_field_offset_y( $args )
	{
		$unit_y = get_option( 'war_soundy_offset_y_unit' );
    $unit_options_y = '';
    $unit_comment_y = '(pixels)';
    foreach( $this->units as $unit => $comment )
    {
    	if( $unit == $unit_y )
    	{
    		$unit_options_y .= '<option selected>' . $unit . '</option>';
    		$unit_comment_y = $comment;
    	}
    	else
    	{
    		$unit_options_y .= '<option>' . $unit . '</option>';
    	}
    }									
		?>
		<input type="text" 
		       name="war_soundy_offset_y" 
		       id="war_soundy_offset_y" 
		       value="<?php echo get_option( 'war_soundy_offset_y' ); ?>"
		       size="4" />
		<select name="war_soundy_offset_y_unit"
			      onchange="war_lengthUnitChanged( this )">
			<?php echo $unit_options_y; ?>
		</select>
		<span id="war_soundy_unit_comment_y"><?php echo $unit_comment_y; ?></span>
		Vertical length between button and horizontal corner edge
		<?php     
	}

	public function add_settings_field_template_tags( $args )
	{
		?>
		<p>To display the Play/Pause button in the header of posts and pages, use the template tag: <strong>soundy_button()</strong>.</p>
		<p>To display the audio track title in the header of posts and pages, use the template tag: <strong>soundy_title()</strong>.</p>
		<p>These template tags will typically be used in the header.php file.</p>
		<p>Template tags use examples:</p>
		<ul style="padding-left: 40px;">
			<li><strong>&lt;?php soundy_button(); ?&gt;</strong>
			<li><strong>&lt;?php soundy_title(); ?&gt;</strong>
		</ul>
		<p>Positioning with the soundy_button() template tag will disable corner positioning of the Play/Pause button.</p>
		<?php
	}
	
	public function add_settings_field_shortcodes( $args )
	{
		?>
		<p>To display the Play/Pause button in the content of posts and pages, use the shortcode: <strong>[soundy button]</strong>.</p>
		<p>To display the audio track title in the content of posts and pages, use the template tag: <strong>[soundy title]</strong>.</p>
		<p>Positioning with the [soundy button] shortcode will disable template tag and corner positioning of the Play/Pause button.</p>
		<p>Shortcode positioning has precedence upon template tag positioning, which has precedence upon corner positioning.</p>
		<p>Note that multiple inserts of the Play/Pause button are not supported.</p>
		<?php
	}
	
	public function add_meta_box( $post ) 
	{ 
		$screen = get_current_screen();
		
		add_meta_box( 'soundy-meta-box', 
		              'Soundy Background', 
		              array( $this, 'render_meta_box' ), 
		              $screen->post_type,
		              'normal',
		              'high' );
	}
	
	public function get_meta_data( $meta_data_name, $can_be_default = false )
	{
		$meta_data = get_post_meta( $this->post_id, $meta_data_name, true );
		
		if( ( $meta_data == '' || $meta_data == 'default' ) && $can_be_default )
		{
			$meta_data = 'default';
		}
		elseif( $meta_data == '' || $meta_data == 'default' )
		{
			$meta_data = get_option( $meta_data_name );
		}
		
		if( $meta_data == 'no_value' )
		{
			$meta_data = '';
		}
				
		return $meta_data;
	}
	
	public function render_meta_box( $post ) 
	{
		$this->post_id = $post->ID;
		
		$enable_bg_sound    = $this->get_meta_data( 'war_soundy_enable_bg_sound', true );
		$audio_file_url     = $this->get_meta_data( 'war_soundy_audio_file_url', false );
		$audio_title        = $this->get_meta_data( 'war_soundy_audio_title', false );
		$autoplay           = $this->get_meta_data( 'war_soundy_autoplay', true );
		$loop               = $this->get_meta_data( 'war_soundy_loop', true );

		$audio_default_url    = get_option( 'war_soundy_audio_file_url' );
		$audio_default_title  = get_option( 'war_soundy_audio_title' );
		$audio_file_type      = pathinfo( $audio_file_url, PATHINFO_EXTENSION );
		?>
		<script>
			war_bindMediaUploader( 'war_soundy_audio_file_url', 'war_audio_library_button', 'audio' );
		</script>
		<table class="form-table war_soundy">
		<tr>
			<th class="war_soundy">
				<label for="war_soundy_enable_bg_sound">Enable Background Sound</label>
			</th>
			<td>
				<input type="radio" 
							 id="war_soundy_enable_bg_sound_default" 
							 name="war_soundy_enable_bg_sound" 
							 value="default" <?php echo ( $enable_bg_sound == 'default' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_enable_bg_sound_default" style="margin-right: 1em;">Default</label>
				
				<input type="radio" 
							 id="war_soundy_enable_bg_sound_yes" 
							 name="war_soundy_enable_bg_sound" 
							 value="yes" <?php echo ( $enable_bg_sound == 'yes' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_enable_bg_sound_yes" style="margin-right: 1em;">Yes</label>

				<input type="radio" 
							 id="war_soundy_enable_bg_sound_no" 
							 name="war_soundy_enable_bg_sound" 
							 value="no" <?php echo ( $enable_bg_sound == 'no' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_enable_bg_sound_no" style="margin-right: 1em;">No</label>
	    </td>
		</tr>
		<tr>
			<th class="war_soundy">
				<label for="war_soundy_audio_file_url">Audio File URL</label>
			</th>
			<td>
				<?php $this->add_field_audio_file_URL_table( $audio_file_url, 
																										 $audio_file_type, 
																										 $audio_default_url,
																										 $audio_default_title ); ?>
	    </td>
		</tr>
		<tr>
			<th class="war_soundy">
				<label for="war_soundy_audio_title">Audio Title</label>
			</th>
			<td>
				<input type="text"
							 size="70%"
				       value="<?php echo $audio_title; ?>"
				       name="war_soundy_audio_title" 
				       id="war_soundy_audio_title" />
	    </td>
		</tr>
		<tr>
			<th class="war_soundy">
				<label for="war_soundy_autoplay">Autoplay</label>
			</th>
			<td>
				<input type="radio" 
							 id="war_soundy_autoplay_default" 
							 name="war_soundy_autoplay" 
							 value="default" <?php echo ( $autoplay == 'default' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_autoplay_default" style="margin-right: 1em;">Default</label>
				
				<input type="radio" 
							 id="war_soundy_autoplay_yes" 
							 name="war_soundy_autoplay" 
							 value="yes" <?php echo ( $autoplay == 'yes' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_autoplay_yes" style="margin-right: 1em;">Yes</label>

				<input type="radio" 
							 id="war_soundy_autoplay_no" 
							 name="war_soundy_autoplay" 
							 value="no" <?php echo ( $autoplay == 'no' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_autoplay_no" style="margin-right: 1em;">No</label>
	    </td>
		</tr>
		<tr>
			<th class="war_soundy">
				<label for="war_soundy_loop">Audio Repeat Loop</label>
			</th>
			<td>
				<input type="radio" 
							 id="war_soundy_loop_default" 
							 name="war_soundy_loop" 
							 value="default" <?php echo ( $loop == 'default' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_loop_default" style="margin-right: 1em;">Default</label>
				
				<input type="radio" 
							 id="war_soundy_loop_yes" 
							 name="war_soundy_loop" 
							 value="yes" <?php echo ( $loop == 'yes' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_loop_yes" style="margin-right: 1em;">Yes</label>

				<input type="radio" 
							 id="war_soundy_loop_no" 
							 name="war_soundy_loop" 
							 value="no" <?php echo ( $loop == 'no' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_loop_no" style="margin-right: 1em;">No</label>
	    </td>
		</tr>
		</table>
		<!--
		<script>
			function war_show()
			{
				var src = jQuery( '#war_soundy_audio_player_source' ).attr( 'src' );
				var type = jQuery( '#war_soundy_audio_player_source' ).attr( 'type' );
				
				alert( 'src = ' + src + ' \ntype = ' + type );
			}
		</script>
		<h1 onclick="war_show();">CLICK</h1>
		-->
	
		<?php
	}
	
	public function save_post_data( $post_id ) 
	{ 
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
			
		if ( 'page' == $_POST[ 'post_type' ] ) 
		{
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} 
		else 
		{
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		
		update_post_meta( $post_id, 
		                  'war_soundy_enable_bg_sound', 
		                  $_POST[ 'war_soundy_enable_bg_sound' ] );

		$audio_file_url = $this->do_sanitize_field( $_POST[ 'war_soundy_audio_file_url' ] );
		update_post_meta( $post_id, 
		                  'war_soundy_audio_file_url', 
		                  $audio_file_url );

		$audio_title = $this->do_sanitize_field( $_POST[ 'war_soundy_audio_title' ] );

		if( trim( $audio_file_url ) == '' )
		{
			$audio_title = '';
		}
		elseif( trim( $audio_title ) == '' )
		{
			$audio_title = 'no_value';
		}
		
		update_post_meta( $post_id, 
		                  'war_soundy_audio_title', 
		                  $audio_title );
		
		update_post_meta( $post_id, 
		                  'war_soundy_autoplay', 
		                  $_POST[ 'war_soundy_autoplay' ] );
		
		update_post_meta( $post_id, 
		                  'war_soundy_loop', 
		                  $_POST[ 'war_soundy_loop' ] );
	}
	
	public function insert_audio() 
	{
		$this->post_id = get_the_ID();

		$enable_bg_sound = $this->get_meta_data( 'war_soundy_enable_bg_sound' );
		if( $enable_bg_sound != 'yes' ) return;
		
		$audio_file_url = $this->get_meta_data( 'war_soundy_audio_file_url' );
		if( $audio_file_url == '' ) return;
		$audio_type = $this->get_audio_type_from_URL( $audio_file_url );

		$this->autoplay = $this->get_meta_data( 'war_soundy_autoplay' );
		$auto_play = ( $this->autoplay == 'yes' ) ? 'autoplay' : '';

		$repeat_loop = $this->get_meta_data( 'war_soundy_loop' );
		$audio_loop = ( $repeat_loop == 'yes' ) ? 'loop' : '';

		$pp_code = $this->get_pp_button_code( 'corner' );
				
		$audio_code = <<<"EO_AUDIOCODE"

		<div style="display: none">
		<audio id="war_soundy_audio" preload="auto" $auto_play $audio_loop style="display: none;" hidden>
			<source src="$audio_file_url" type="audio/$audio_type">
		</audio>
		</div>
EO_AUDIOCODE;

		$pp_code    = str_replace( array( "\n", "\r" ), ' ', $pp_code );
		$audio_code = str_replace( array( "\n", "\r" ), ' ', $audio_code );
		wp_enqueue_scripts( 'jquery' );
		?>
		<link rel="prefetch" href="<?php echo $audio_file_url; ?>">
		<link rel="prefetch" href="<?php echo $this->button_url_play; ?>">
		<link rel="prefetch" href="<?php echo $this->button_url_pause; ?>">
		<link rel="prefetch" href="<?php echo $this->hover_url_play; ?>">
		<link rel="prefetch" href="<?php echo $this->hover_url_pause; ?>">
		<script>
			var war_soundy_pp_button_is_inserted = false;
			
			jQuery( document ).ready( function() 
			{
				<?php if( $pp_code != '' ): ?>
					if( ! war_soundy_pp_button_is_inserted )
					{
						jQuery( 'body' ).append( '<?php echo $pp_code; ?>' );
						war_soundy_pp_button_is_inserted = true;
					}
				<?php endif; ?>
				
				jQuery( 'body' ).append( '<?php echo $audio_code; ?>' );
				
				war_soundy_player = jQuery( '#war_soundy_audio' )[ 0 ];
				war_soundy_hovering = false;

				if( war_soundy_pp_button_is_inserted )
				{
					war_soundy_audio_control = jQuery( '#war_soundy_audio_control' );
	
					war_soundy_audio_control.click( 
						function() 
						{ 
							if( war_soundy_player.paused )
							{
								war_soundy_player.play();
								war_soundy_audio_control.attr( 'src', '<?php echo $this->hover_url_pause; ?>' );
							}
							else
							{
								war_soundy_player.pause();
								war_soundy_audio_control.attr( 'src', '<?php echo $this->hover_url_play; ?>' );
							}
						} );
					
					war_soundy_audio_control.hover( 
						function() 
						{ 
							war_soundy_hovering = true;
							if( war_soundy_player.paused )
							{
								war_soundy_audio_control.attr( 'src', '<?php echo $this->hover_url_play; ?>' );
							}
							else
							{
								war_soundy_audio_control.attr( 'src', '<?php echo $this->hover_url_pause; ?>' );
							}
						},
						function() 
						{ 
							war_soundy_hovering = false;
							if( war_soundy_player.paused )
							{
								jQuery( '#war_soundy_audio_control' ).attr( 'src', '<?php echo $this->button_url_play; ?>' );
							}
							else
							{
								jQuery( '#war_soundy_audio_control' ).attr( 'src', '<?php echo $this->button_url_pause; ?>' );
							}
						}
					);
					
					jQuery( '#war_soundy_audio' ).bind( 'ended' , function()
					{
						if( war_soundy_hovering )
						{
							war_soundy_audio_control.attr( 'src', '<?php echo $this->hover_url_play; ?>' );
						}
						else
						{
							war_soundy_audio_control.attr( 'src', '<?php echo $this->button_url_play; ?>' );
						}
					} );
				}
			} );
		</script>
		<?php
	}

	private $button_url_play;
	private $hover_url_play;
	private $button_url_pause;
	private $hover_url_pause;
	private $autoplay;
	
	public function get_pp_button_code( $mode )
	{
		$display_play_pause = $this->get_meta_data( 'war_soundy_display_play_pause' );
		if( $display_play_pause != 'yes' ) return '';

		$enable_bg_sound = $this->get_meta_data( 'war_soundy_enable_bg_sound' );
		if( $enable_bg_sound != 'yes' ) return '';

		$this->button_url_play  = get_option( 'war_soundy_url_play_button' );
		$this->hover_url_play   = get_option( 'war_soundy_url_play_hover' );
		$this->button_url_pause = get_option( 'war_soundy_url_pause_button' );
		$this->hover_url_pause  = get_option( 'war_soundy_url_pause_hover' );

		$button_position  = get_option( 'war_soundy_pp_position' );
		$position = ( $button_position == 'document' ) ? 'absolute' : 'fixed';

		if( $mode == 'corner' )
		{
			$button_corner    = get_option( 'war_soundy_pp_corner' );
			switch( $button_corner )
			{
				case upper_right: 
					$dim_x = 'right';
					$dim_y = 'top';
					break;
				case upper_left: 
					$dim_x = 'left';
					$dim_y = 'top';
					break;
				case bottom_right: 
					$dim_x = 'right';
					$dim_y = 'bottom';
					break;
				case bottom_left: 
					$dim_x = 'left';
					$dim_y = 'bottom';
					break;
			}
	
			$button_x = get_option( 'war_soundy_offset_x' ) .
			            get_option( 'war_soundy_offset_x_unit' );
			$button_y = get_option( 'war_soundy_offset_y' ) .
			            get_option( 'war_soundy_offset_y_unit' );
			
			$position_css_code = "position: $position; $dim_x: $button_x; $dim_y: $button_y;";
		}
		else
		{
			$position_css_code = '';
		}
		
		$audio_button_url = ( $this->autoplay == 'yes' ) ? $this->button_url_pause : $this->button_url_play;
		
		$audio_title = $this->get_meta_data( 'war_soundy_audio_title' );
		$audio_title = $audio_title ? "Playing: $audio_title" : '';
	
		$pp_code = <<<"EO_PPCODE"

		<img id="war_soundy_audio_control" 
	     src="$audio_button_url"
	     title="$audio_title"
	     style="$position_css_code cursor: pointer; z-index: 99999999;">
EO_PPCODE;

		return $pp_code;
	}
	
	public function do_sanitize_field( $value )
	{
		return htmlentities( sanitize_text_field( $value ), ENT_QUOTES, 'UTF-8' );
	}
	
	public function soundy_shortcode( $atts )
	{
		if( $atts[ 0 ] && $atts[ 0 ] == 'title' )
		{
			return soundy_get_title();
		}
		elseif( $atts[ 0 ] && $atts[ 0 ] == 'button' )
		{
			return soundy_get_button();
		}
	}
}

$war_soundy = new WarSoundy();

function soundy_get_button()
{
	global $war_soundy;
	
	$pp_code = $war_soundy->get_pp_button_code( '' );
	if( $pp_code != '' )
	{
		$pp_code = '<script>' .
		           'jQuery( "#war_soundy_audio_control" ).remove();' .
		           'war_soundy_pp_button_is_inserted = true;' .
		           '</script>' . 
		           $pp_code;
	}
	
	return $pp_code;
}

function soundy_button()
{
	$pp_code = soundy_get_button();
	echo $pp_code;
}

function soundy_get_title()
{
	global $war_soundy;
	
	$title = $war_soundy->get_meta_data( 'war_soundy_audio_title' );
	
	return $title;
}

function soundy_title()
{
	$title = soundy_get_title();
	echo $title;
}
?>