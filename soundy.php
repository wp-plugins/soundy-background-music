<?php
/**
 * @package Soundy_Background_Music
 * @version 2.0
 */
/*
Plugin Name: Soundy Background Music
Plugin URI: http://webartisan.ch/en/products/soundy-free/
Description: This plugin allows administrators and authors to set a background sound on any post or page.
Version: 2.0
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
	private $soundy_version                   = '2.0';
	private $soundy_type                      = 'free';
	private $soundy_subtype                   = '';
	private $soundy_free_home_wp_url          = 'http://wordpress.org/plugins/soundy-background-music/';
	private $soundy_pro_home_url              = 'http://webartisan.ch/en/products/soundy-pro/';
	private $disable_soundy_for_mobile        = false;
	private $use_own_jquery_lib_on_front_end  = true;
	private $enable_bg_sound                  = 'no';
	private $audio_url                        = '/audio/valse.mp3';
	private $audio_volume                     = '80';
	private $audio_title                      = 'Valse - Anonymous (1870)';
	private $autoplay                         = 'yes';
	private $loop                             = 'yes';
	private $pp_images_to_use                 = 'default';
	private $pp_position                      = 'window';
	private $pp_corner                        = 'upper_right';
	private $offset_x                         = 35;
	private $offset_x_unit                    = 'px';
	private $offset_y                         = 35;
	private $offset_y_unit                    = 'px';
	private $play_button_url                  = '/images/buttons/48x48/play-square-grey.png';
	private $play_hover_url                   = '/images/buttons/48x48/play-square-blue.png';
	private $pause_button_url                 = '/images/buttons/48x48/pause-square-grey.png';
	private $pause_hover_url                  = '/images/buttons/48x48/pause-square-blue.png';
	private $button_dimensions                = '48x48';
	private $page_preview_url                 = '';
	private $pp_design; // WarSoundyPlayPauseDesign Object
	private $user_agent_is_mobile;
	private $post_id;
	
	public  $plugin_name;
	public  $plugin_url;
	public  $plugin_path;
	public  $plugin_path_file = __FILE__;

	private	$units = array( 
				               		'px' => '(pixels)', 
				                	'%'  => '(percentage)', 
				                	'in' => '(inches)',
				                	'mm' => '(millimeters)',
				                	'cm' => '(centimeters)'
				                );

	public function __construct()  
	{
		$this->user_agent_is_mobile = $this->check_user_agent( 'mobile' );
		
		$this->plugin_path = dirname( __FILE__ );
		$this->plugin_name = substr( $this->plugin_path, strrpos( $this->plugin_path, '/' ) + 1 );
		$this->plugin_url  = WP_PLUGIN_URL . '/' . $this->plugin_name;

		$this->audio_url        = $this->plugin_url . $this->audio_url;
		$this->play_button_url  = $this->plugin_url . $this->play_button_url;
		$this->play_hover_url   = $this->plugin_url . $this->play_hover_url;
		$this->pause_button_url = $this->plugin_url . $this->pause_button_url;
		$this->pause_hover_url  = $this->plugin_url . $this->pause_hover_url;

		if( is_admin() )
		{
			register_activation_hook( __FILE__, array( $this, 'activate' ) ); 
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	
			add_action( 'admin_menu', array( $this, 'add_plugin_settings_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) ); 
			
			add_filter( 'plugin_action_links', array( $this, 'add_settings_link_to_plugins_page_soundy_entry' ) );
			if( $this->soundy_type == 'free' || $this->soundy_subtype == 'trial' )
			{
				add_filter( 'plugin_row_meta', array( $this, 'add_pro_buy_link_to_plugins_page_soundy_entry' ), 10, 2 );
			}
			
			$uri = $_SERVER[ 'REQUEST_URI' ];
			$is_edit_post =  ( strpos( $uri, '/wp-admin/post.php' ) == 0 ) ||
		      						 ( strpos( $uri, '/wp-admin/post-new.php' ) == 0 );
		  
		  if( ( isset( $_GET['page'] ) && ( $_GET['page'] == 'soundy' ) ) || $is_edit_post )
		  {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
				// to get 'Insert into Post' Button in Upload Dialog:
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
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_front_end' ) );
			add_action( 'wp_head', array( $this, 'insert_audio' ) );
			add_shortcode( 'soundy', array( $this, 'soundy_shortcode' ) );
		}
		
		$soundy_type    = get_option( 'war_soundy_type' );
		$soundy_version = get_option( 'war_soundy_version' );
		if( ( $soundy_type != $this->soundy_type ) || ( $soundy_version != $this->soundy_version ) )
		{
			update_option( 'war_soundy_version', $this->soundy_version );
			
			$volume = get_option( 'war_soundy_audio_volume' );
			if( $volume == '' )
			{
				update_option( 'war_soundy_audio_volume', $this->audio_volume );
			}

			$display_play_pause = get_option( 'war_soundy_display_play_pause' );
			delete_option( 'war_soundy_display_play_pause' );
			$pp_images_to_use = get_option( 'war_soundy_pp_images_to_use' );
			if( ! $pp_images_to_use )
			{
				if( $display_play_pause == 'yes' || $display_play_pause == '' )
				{
					update_option( 'war_soundy_pp_images_to_use', 'default');
				}
				else if( $display_play_pause == 'no' )
				{
					update_option( 'war_soundy_pp_images_to_use', 'none' );
				}
				else
				{
					update_option( 'war_soundy_pp_images_to_use', 'default');
				}
			}

			$image_play_normal  = get_option( 'war_soundy_url_play_button' );
			$image_play_hover   = get_option( 'war_soundy_url_play_hover' );
			$image_pause_normal = get_option( 'war_soundy_url_pause_button' );
			$image_pause_hover  = get_option( 'war_soundy_url_pause_hover' );
				
			$image_play_normal  = str_replace( 'soundy-music-pro', 'soundy-background-music', $image_play_normal );
			$image_play_hover   = str_replace( 'soundy-music-pro', 'soundy-background-music', $image_play_hover );
			$image_pause_normal = str_replace( 'soundy-music-pro', 'soundy-background-music', $image_pause_normal );
			$image_pause_hover  = str_replace( 'soundy-music-pro', 'soundy-background-music', $image_pause_hover );
			
			update_option( 'war_soundy_url_play_button',  $image_play_normal );
			update_option( 'war_soundy_url_play_hover',   $image_play_hover );
			update_option( 'war_soundy_url_pause_button', $image_pause_normal );
			update_option( 'war_soundy_url_pause_hover',  $image_pause_hover );

			$audio_url  = get_option( 'war_soundy_audio_file_url' );
			$audio_url  = str_replace( 'soundy-music-pro', 'soundy-background-music', $audio_url );
			update_option( 'war_soundy_audio_file_url', $audio_url );
		}
	}  

	public function activate() 
	{
		add_option( 'war_soundy_type',               $this->soundy_type ); 
		add_option( 'war_soundy_version',            $this->soundy_version ); 
		add_option( 'war_soundy_enable_bg_sound',    $this->enable_bg_sound ); 
		add_option( 'war_soundy_audio_file_url',     $this->audio_url ); 
		add_option( 'war_soundy_audio_volume',       $this->audio_volume ); 
		add_option( 'war_soundy_audio_title',        $this->audio_title ); 
		add_option( 'war_soundy_autoplay',           $this->autoplay ); 
		add_option( 'war_soundy_loop',               $this->loop ); 
		add_option( 'war_soundy_pp_images_to_use',   $this->pp_images_to_use ); 
		add_option( 'war_soundy_url_play_button',    $this->play_button_url ); 
		add_option( 'war_soundy_url_play_hover',     $this->play_hover_url ); 
		add_option( 'war_soundy_url_pause_button',   $this->pause_button_url ); 
		add_option( 'war_soundy_url_pause_hover',    $this->pause_hover_url ); 
		add_option( 'war_soundy_pp_position',        $this->pp_position ); 
		add_option( 'war_soundy_pp_corner',          $this->pp_corner ); 
		add_option( 'war_soundy_offset_x',           $this->offset_x ); 
		add_option( 'war_soundy_offset_x_unit',      $this->offset_x_unit ); 
		add_option( 'war_soundy_offset_y',           $this->offset_y ); 
		add_option( 'war_soundy_offset_y_unit',      $this->offset_y_unit ); 
		add_option( 'war_soundy_page_preview_url', 	 $this->page_preview_url );
	}
	
	public function deactivate() 
	{
	}

	public function add_plugin_settings_menu() 
	{
		$html_page_title = 'Soundy';
		$settings_entry_name = $html_page_title;
		add_options_page( $html_page_title, $settings_entry_name, 'manage_options', 'soundy', array( $this, 'create_plugin_settings_page' ) ); 
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
		// to get 'Insert into Post' Button in Upload Dialog:
		$args[ 'send' ] = true;
		return $args;
	}
	
	public function admin_scripts( $hook )
	{
		/*
		wp_deregister_script( 'jquery' ); 
		wp_deregister_script( 'jquery-ui-core' ); 
		wp_deregister_script( 'jquery-ui-widget' ); 
		wp_deregister_script( 'jquery-ui-mouse' ); 
		wp_deregister_script( 'jquery-ui-tabs' ); 
		wp_deregister_script( 'jquery-ui-slider' ); 

		wp_register_script( 'jquery', 
		                    $this->plugin_url . '/js/jquery-ui-1.10.4/jquery-1.10.2.js' );
		wp_register_script( 'jquery-ui-core', 
		                    $this->plugin_url . '/js/jquery-ui-1.10.4/jquery.ui.core.js' );
		wp_register_script( 'jquery-ui-widget', 
		                    $this->plugin_url . '/js/jquery-ui-1.10.4/jquery.ui.widget.js' );
		wp_register_script( 'jquery-ui-mouse', 
		                    $this->plugin_url . '/js/jquery-ui-1.10.4/jquery.ui.mouse.js' );
		wp_register_script( 'jquery-ui-tabs', 
		                    $this->plugin_url . '/js/jquery-ui-1.10.4/jquery.ui.tabs.js' );
		wp_register_script( 'jquery-ui-slider', 
		                    $this->plugin_url . '/js/jquery-ui-1.10.4/jquery.ui.slider.js' );
		*/
		
		wp_register_script( 'soundy-back-end', $this->plugin_url . '/js/back-end.js', array( 'jquery', 'media-upload', 'thickbox' ) );
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-slider' );

		wp_enqueue_script( 'soundy-back-end' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'button-upload' );
		
		wp_register_style( 'jquery-ui', $this->plugin_url . '/css/jquery-ui-1.10.4/jquery-ui.css' );
		wp_register_style( 'soundy', $this->plugin_url . '/css/style-back-end.css' );
		if( $this->check_user_agent( 'firefox' ) )
		{
			wp_register_style( 'soundy-firefox', $this->plugin_url . '/css/style-back-end-firefox.css' );
		}
		
		wp_enqueue_style( 'soundy' );
		wp_enqueue_style( 'soundy-firefox' );
		
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'thickbox' );
	}
	
	public function enqueue_scripts_front_end( $hook )
	{
		if( $this->use_own_jquery_lib_on_front_end )
		{
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', $this->plugin_url . '/js/jquery-ui-1.10.4/jquery-1.10.2.js' );
			wp_enqueue_script( 'jquery' );
		}
		
		wp_register_script( 'soundy-front-end', $this->plugin_url . '/js/front-end.js', array( 'jquery' ) );		
		wp_enqueue_script( 'soundy-front-end' );
		
		wp_register_style( 'soundy', $this->plugin_url . '/css/style-front-end.css' );		
		wp_enqueue_style( 'soundy' );
	}	
		
	public function add_settings_link_to_plugins_page_soundy_entry( $links ) 
	{ 
		$settings_link = '<a href="options-general.php?page=soundy">Settings</a>'; 
		array_unshift( $links, $settings_link ); 
		return $links; 
	}
	
	public function add_pro_buy_link_to_plugins_page_soundy_entry( $links, $file ) 
	{
		$plugin_name = plugin_basename( __FILE__ );
		
		if ( strpos( $file, $plugin_name ) !== false ) 
		{
			if( $this->soundy_subtype == 'trial' )
			{
				$link_title = 'Upgrade to Soundy PRO';
				$pro_link = '<a href="' . $this->soundy_pro_home_url . '" target="_blank" class="war_soundy_hit_link">' . $link_title . '</a>';		
			}
			else
			{
				$link_title = 'Try Soundy PRO for Free';
				$pro_link = '<a href="' . $this->soundy_pro_home_url . '" target="_blank" class="war_soundy_hit_link">' . $link_title . '</a>' .
				            ' with its Play/Pause Button Designer';		
			}
			$link = array_shift( $links );
			array_unshift( $links, $pro_link );
			array_unshift( $links, $link );
			
			if( $this->soundy_type == 'free' )
			{
				$free_wp_link_title = 'WordPress.org Plugin Page';
				$free_wp_link = '<a href="' . $this->soundy_free_home_wp_url . '" target="_blank">' . $free_wp_link_title . '</a>';
				array_push( $links, $free_wp_link );
			}
		}
		
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
    
		register_setting( 'war_soundy', 'war_soundy_audio_volume' );
    add_settings_field( 
	    'war_soundy_audio_volume',
	    'Audio Volume',
	    array( $this, 'add_settings_field_audio_volume' ),
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

    add_settings_field( 
	    'war_soundy_reset',
	    'Default Audio',
	    array( $this, 'add_settings_field_default_audio' ),
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
    
		register_setting( 'war_soundy', 'war_soundy_pp_images_to_use' ); 
    add_settings_field( 
	    'war_soundy_pp_images_to_use',
	    'Play/Pause Button Images',
	    array( $this, 'add_settings_field_pp_images_to_use' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button'
		);

		register_setting( 'war_soundy', 'war_soundy_url_play_button', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_play_button',
	    'Play Normal URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'play_button' )
		);

		register_setting( 'war_soundy', 'war_soundy_url_play_hover', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_play_hover',
	    'Play Hover URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'play_hover' )
		);

		register_setting( 'war_soundy', 'war_soundy_url_pause_button', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_pause_button',
	    'Pause Normal URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'pause_button' )
		);

		register_setting( 'war_soundy', 'war_soundy_url_pause_hover', array( $this, 'do_sanitize_field' ) ); 
    add_settings_field( 
	    'war_soundy_url_pause_hover',
	    'Pause Hover URL',
	    array( $this, 'add_settings_field_url_pp_button' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'pause_hover' )
		);

    add_settings_field
    ( 
	    'war_soundy_swap_normal_hover',
	    'Swap Normal &lt;-&gt; Hover',
	    array( $this, 'add_settings_field_swap_normal_hover' ),
	    'soundy',                       
	    'war_soundy_settings_section_play_pause_button',
	    array( 'pause_hover' )
		);

    add_settings_field
    (
	    'war_soundy_reset_default_buttons',
	    'Default Buttons',
	    array( $this, 'add_settings_field_default_buttons' ),
	    'soundy',
	    'war_soundy_settings_section_play_pause_button',
	    array( 'pause_hover' )
    );

    add_settings_field
    (
	    'war_soundy_img_preview_here',
	    'Button Preview',
	    array( $this, 'add_settings_field_img_preview_here' ),
	    'soundy',
	    'war_soundy_settings_section_play_pause_button'
    );

    add_settings_field
    ( 
			'war_soundy_preview_in_context_default',
			'Preview in Context',
			array( $this, 'add_settings_field_preview_in_context_default' ),
			'soundy',                       
			'war_soundy_settings_section_play_pause_button'
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

    add_settings_field(
	    'war_soundy_preview_in_context_position',
	    'Preview in Context',
	    array( $this, 'add_settings_field_preview_in_context_position' ),
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
		$this->add_field_audio_file_URL( false );
	}

	private function add_field_audio_file_URL( $is_meta_box ) 
	{
		if( $is_meta_box )
		{
			$file_url  = $this->get_meta_data( 'war_soundy_audio_file_url', true );
			if( $file_url == 'default' )
			{
				$soundtrack = 'default';
				$file_url = get_option( 'war_soundy_audio_file_url' );
			}
			else
			{
				$soundtrack = 'custom';
			}
			$file_type = pathinfo( $file_url, PATHINFO_EXTENSION );
		
			?>
			<div style="margin: 5px 5px 5px 0px">
				<input type="radio" 
							 id="war_soundy_soundtrack_default" 
							 name="war_soundy_soundtrack" 
							 value="default" <?php echo ( $soundtrack == 'default' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_soundtrack_default" style="margin-right: 1em;">Default</label>
				
				<input type="radio" 
							 id="war_soundy_soundtrack_custom" 
							 name="war_soundy_soundtrack" 
							 value="custom" <?php echo ( $soundtrack == 'custom' ? 'checked' : '' ); ?>/>
				<label for="war_soundy_soundtrack_custom" style="margin-right: 1em;">Custom</label>
			</div>
			<?php
		}
		else
		{
			$file_url  = get_option( 'war_soundy_audio_file_url' );
			$file_type = pathinfo( $file_url, PATHINFO_EXTENSION );
		}
		?>
		<input id="war_soundy_audio_file_url" 
		       name="war_soundy_audio_file_url" 
		       type="text" 
		       value="<?php echo $file_url; ?>"
		       class="war_soundy_txt_input" />
		<br>
		<div style="margin-top: 5px;">
	    <button id="war_audio_library_button" 
	            type="button" 
	            class="war_soundy" />Media Library</button>
			<audio id="war_soundy_audio_player" 
				     class="war_soundy"
				     controls
					   style="margin-right: 10px;">
				<source id="war_soundy_audio_player_source"
					      src="<?php echo $file_url; ?>" 
					      type="audio/<?php echo $file_type; ?>">
		  </audio>
		</div>
	  <?php     
	}
	
	public function add_settings_field_audio_volume( $args )
	{
		?>
		<div id="war_soundy_audio_volume_slider" style="width: 300px; display: inline-block; margin-right: 10px;"></div>
		<input type="text"
					 class="war_soundy_audio_volume"
		       value="<?php echo get_option( 'war_soundy_audio_volume' ); ?>"
		       name="war_soundy_audio_volume" 
		       id="war_soundy_audio_volume" /> %
		<?php     
	}
	
	public function add_settings_field_audio_title( $args )
	{
		?>
		<input type="text"
					 class="war_soundy_txt_input"
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

	public function add_settings_field_default_audio( $args ) 
	{
		?>
    <button id="war_audio_default_button" 
            type="button" 
            class="war_soundy" />Reset</button>
		<?php     
	}
	
	public function add_settings_field_pp_images_to_use( $args )
	{
		$pp_images_to_use = get_option( 'war_soundy_pp_images_to_use' );
		if( $pp_images_to_use == 'designer' && $this->soundy_type != 'pro' && $this->soundy_subtype != 'full' )
		{
			$pp_images_to_use = 'default';
		}
		?>
		<input type="radio" 
					 id="war_soundy_pp_images_to_use_default" 
					 name="war_soundy_pp_images_to_use" 
					 value="default" 
					 style="margin: 5px 0 5px 0;" <?php echo ( $pp_images_to_use == 'default' ? 'checked' : '' ); ?>/>
		<label for="war_soundy_pp_images_to_use_default" 
		       style="margin-top: 0;">Use button images defined in this Play/Pause Button tab</label>
		<br>
		<input type="radio" 
					 id="war_soundy_pp_images_to_use_none" 
					 name="war_soundy_pp_images_to_use" 
					 value="none" 
					 style="margin: 5px 0 5px 0;" <?php echo ( $pp_images_to_use == 'none' ? 'checked' : '' ); ?>/>
		<label for="war_soundy_pp_images_to_use_none" 
		       style="margin-top: 0;">Do not display any Play/Pause Button</label>
		<?php     
	}
	
	public function add_settings_field_url_pp_button( $args )
	{
		$type = $args[ 0 ];
		?>
		<input id="war_soundy_url_<?php echo $type; ?>" 
		       name="war_soundy_url_<?php echo $type; ?>" 
		       type="text" 
		       class="war_soundy_txt_input" 
		       value="<?php echo get_option( "war_soundy_url_$type" ); ?>" />
		<div style="margin-top: 5px;">
	    <button id="img_<?php echo $type; ?>_library_button" 
	            type="button" 
	            value="Media Library" 
	            class="war_soundy_button_media_library_pp_button" />Media Library</button>
	    <img id="war_soundy_url_<?php echo $type; ?>_img" 
	         src="<?php echo get_option( "war_soundy_url_$type" ); ?>"
	         class="war_soundy" >
		</div>
    <?php     
	}

	public function add_settings_field_swap_normal_hover( $args )
	{
		?>
		<button id="war_soundy_button_swap_normal_hover"
		        type="button"
						class="war_soundy">Swap</button>
		<?php
	}
	
	public function add_settings_field_default_buttons( $args )
	{
		?>
    <button id="button_default_buttons_24" 
            type="button" 
            value="24x24"
            class="war_soundy"
            style="margin-right: 10px;" />24x24</button>
    <button id="button_default_buttons_32" 
            type="button" 
            value="32x32"
            class="war_soundy"
            style="margin-right: 10px;" />32x32</button>
    <button id="button_default_buttons_48" 
            type="button" 
            value="48x48"
            class="war_soundy"
            style="margin-right: 10px;" />48x48</button>
    <button id="button_default_buttons_64" 
            type="button" 
            value="64x64"
            class="war_soundy"
            style="margin-right: 10px;" />64x64</button>
		<?php     
	}

	public function add_settings_field_img_preview_here( $args )
	{
		?>
		<img id="war_soundy_img_preview_here">
		<?php     
	}

	public function add_settings_field_preview_in_context_default( $args )
	{
		?>
			<span id="war_soundy_page_preview_label"
			      class="war_soundy_page_preview_label">Page:</span>
			<select id="war_soundy_page_preview_url_default"
			        class="war_soundy_page_preview_url">
			 	<?php $this->add_page_preview_url_options() ?>
			</select>
			<br>
			<button id="war_soundy_button_preview_in_context_default"
				      type="button"
					    style="margin-top: 8px;"
				      class="war_soundy">Preview</button>
			<?php     
		}

		public function add_settings_field_preview_in_context_position( $args )
		{
			?>
				<span id="war_soundy_page_preview_label"
			  	    class="war_soundy_page_preview_label">Page:</span>
				<select id="war_soundy_page_preview_url_position"
			        	class="war_soundy_page_preview_url">
				<?php $this->add_page_preview_url_options() ?>
				</select>
				<br>
				<button id="war_soundy_button_preview_in_context_position"
					      type="button"
					      style="margin-top: 8px;"
				        class="war_soundy">Preview</button>
				<?php     
			}

		private function add_page_preview_url_options()
	{
		echo '<option value="/">Select Page</option>';
		$page_preview_url = get_option( 'war_soundy_page_preview_url' );
		$pages = get_pages();
		foreach ( $pages as $page )
		{
			$page_link = get_page_link( $page->ID );
			$option = '<option value="' . $page_link . '"';
			if( $page_link == $page_preview_url )
			{
				$option .= ' selected>';
			}
			else
			{
				$option .= '>';
			}
			$option .= $page->post_title;
			$option .= '</option>';
			echo $option;
		}
	}
	
	public function add_settings_field_pp_position( $args )
	{
		$pp_position = get_option( 'war_soundy_pp_position' );
		$pp_corner   = get_option( 'war_soundy_pp_corner' );
		
		$pp_comment = $pp_position == 'document' ? 
									'(absolute positioning: button will scroll with page content)' : 
									'(fixed positioning: button will NOT scroll with page content)';
		
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
		<select id="war_soundy_pp_position"
						name="war_soundy_pp_position">
			<?php echo $options_position; ?>
		</select>
		<span id="war_soundy_pp_comment"><?php echo $pp_comment; ?></span>
		<br>
		<select id="war_soundy_pp_corner"
						name="war_soundy_pp_corner"
						style="margin-top: 8px;">
			<?php echo $options_corner; ?>
		</select>
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
		<select id="war_soundy_offset_x_unit"
						name="war_soundy_offset_x_unit">
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
		<select id="war_soundy_offset_y_unit"
						name="war_soundy_offset_y_unit">
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
		<p>If you have posts using the button shortcode and these posts are bulk displayed in blog pages, you will get into troubles. A workaround to this multiple buttons issue is to limit the number of posts per blog page to 1 in the Settings &gt; Reading page in the admin area.</p>
		<?php
	}
		
	public function add_meta_box( $post ) 
	{ 
		$screen = get_current_screen();
		
		add_meta_box( 'soundy-meta-box', 
		              'Soundy Background Music', 
		              array( $this, 'render_meta_box' ), 
		              $screen->post_type,
		              'normal',
		              'high' );
	}
	
	public function get_meta_data( $meta_data_name, $can_be_default = false )
	{
		$meta_data = get_post_meta( $this->post_id, $meta_data_name, true );			

		if( $meta_data == 'no_value' )
		{
			$meta_data = '';
		}
		
		if( ( $meta_data == '' || $meta_data == 'default' ) && $can_be_default )
		{
			$meta_data = 'default';
		}
		elseif( $meta_data == '' || $meta_data == 'default' )
		{
			$meta_data = get_option( $meta_data_name );
		}
				
		return $meta_data;
	}
	
	public function render_meta_box( $post ) 
	{
		$this->post_id = $post->ID;
		
		$enable_bg_sound      = $this->get_meta_data( 'war_soundy_enable_bg_sound', true );
		$audio_volume         = $this->get_meta_data( 'war_soundy_audio_volume', true );
		$audio_title          = $this->get_meta_data( 'war_soundy_audio_title', true );
		$autoplay             = $this->get_meta_data( 'war_soundy_autoplay', true );
		$loop                 = $this->get_meta_data( 'war_soundy_loop', true );

		$default_audio_url    = get_option( 'war_soundy_audio_file_url' );
		$default_audio_volume = get_option( 'war_soundy_audio_volume' );
		$default_audio_title  = get_option( 'war_soundy_audio_title' );

		if( $audio_volume == 'default' )
		{
			$audio_volume_is_default = true;
			$audio_volume = $default_audio_volume;
		}
		else
		{
			$audio_volume_is_default = false;
		}

		if( $audio_title == 'default' )
		{
			$audio_title_is_default = true;
			$audio_title = $default_audio_title;
		}
		else
		{
			$audio_title_is_default = false;
		}
		
		include( sprintf( "%s/templates/meta-box.php", dirname( __FILE__ ) ) ); 
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

		$soundtrack = $_POST[ 'war_soundy_soundtrack' ];
		if( $soundtrack == 'default' )
		{
			$audio_file_url = 'default';
		}
		else
		{
			$audio_file_url = $this->do_sanitize_field( $_POST[ 'war_soundy_audio_file_url' ] );
		}

		update_post_meta( $post_id, 
		                  'war_soundy_audio_file_url', 
		                  $audio_file_url );

		$audio_volume_def = $_POST[ 'war_soundy_audio_volume_def' ];
		if( $audio_volume_def == 'default' )
		{
			$audio_volume = 'default';
		}
		else
		{
			$audio_volume = $_POST[ 'war_soundy_audio_volume' ];
		}

		update_post_meta( $post_id, 
		                  'war_soundy_audio_volume', 
		                  $audio_volume );

		$audio_title_def = $_POST[ 'war_soundy_audio_title_def' ];
		if( $audio_title_def == 'default' )
		{
			$audio_title = 'default';
		}
		else
		{
			$audio_title = $this->do_sanitize_field( $_POST[ 'war_soundy_audio_title' ] );
		}

		if( trim( $audio_title ) == '' )
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
	
		$audio_file_url = $this->get_meta_data( 'war_soundy_audio_file_url' );

		$preview = $_GET[ 'war_soundy_preview' ];
		if( $preview )
		{
			$this->preview = $preview;
		}
		else
		{		
			$this->preview = 'false';
			$enable_bg_sound = $this->get_meta_data( 'war_soundy_enable_bg_sound' );
			if( $enable_bg_sound != 'yes' ) return;		
			if( $audio_file_url == '' ) return;
		}
			
		if( $this->user_agent_is_mobile && $this->disable_soundy_for_mobile ) return;
		
		$audio_type = $this->get_audio_type_from_URL( $audio_file_url );

		$audio_volume = $this->get_meta_data( 'war_soundy_audio_volume' ) / 100;

		$this->autoplay = $this->get_meta_data( 'war_soundy_autoplay' );
		$auto_play = ( $this->autoplay == 'yes' ) ? 'autoplay' : '';

		$repeat_loop = $this->get_meta_data( 'war_soundy_loop' );
		$audio_loop = ( $repeat_loop == 'yes' ) ? 'loop' : '';

		$pp_code = $this->get_pp_button_code( 'corner' );
				
		$audio_code = 

			'<div style="display: none;">' .
			'  <audio id="war_soundy_audio_player" preload="auto" ' . $auto_play . ' ' . $audio_loop . '>' .
			'	   <source id="war_soundy_audio_player_source" src="' . $audio_file_url . '" type="audio/' . $audio_type . '">' .
			'  </audio>' .
			'</div>';

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
		var war_soundy_front_end = new war_SoundyFrontEnd(
			'<?php echo $pp_code; ?>',
			'<?php echo $audio_code; ?>',
			<?php echo $audio_volume; ?>,
			'<?php echo $this->preview; ?>',
			'<?php echo $this->button_url_play; ?>',
			'<?php echo $this->button_url_pause; ?>',
			'<?php echo $this->hover_url_play; ?>',
			'<?php echo $this->hover_url_pause; ?>',
			<?php echo $this->user_agent_is_IOS() ?>
				);
		</script>
		<?php
	}

	private $button_url_play;
	private $hover_url_play;
	private $button_url_pause;
	private $hover_url_pause;
	
	public function get_pp_button_code( $mode )
	{
		if( $this->preview == 'false' )
		{
			$pp_images_to_use = $this->get_meta_data( 'war_soundy_pp_images_to_use' );
			if( $pp_images_to_use == 'designer' && $this->soundy_type != 'pro' && $this->soundy_subtype != 'full' )
			{
				$pp_images_to_use = 'default';
			}
			
			if( $pp_images_to_use == 'none' ) return '';
	
			$enable_bg_sound = $this->get_meta_data( 'war_soundy_enable_bg_sound' );
			if( $enable_bg_sound != 'yes' ) return '';
	
			if( $pp_images_to_use == 'default' )
			{
				$this->button_url_play  = get_option( 'war_soundy_url_play_button' );
				$this->hover_url_play   = get_option( 'war_soundy_url_play_hover' );
				$this->button_url_pause = get_option( 'war_soundy_url_pause_button' );
				$this->hover_url_pause  = get_option( 'war_soundy_url_pause_hover' );
			}
		}
		else
		{
			$this->button_url_play  = '';
			$this->hover_url_play   = '';
			$this->button_url_pause = '';
			$this->hover_url_pause  = '';
		}

		$button_position  = get_option( 'war_soundy_pp_position' );
		$position = ( $button_position == 'document' ) ? 'absolute' : 'fixed';

		if( $mode == 'corner' )
		{
			$button_corner = get_option( 'war_soundy_pp_corner' );
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
	
		$pp_code = 

			'<img id="war_soundy_audio_control"' .
		  '     src="' . $audio_button_url . '"' .
		  '     title="' . $audio_title . '"' .
		  '     style="' . $position_css_code . ' cursor: pointer; z-index: 99999999;">';

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
	
	public function check_user_agent ( $type = NULL ) 
	{
    $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
    if ( $type == 'bot' ) 
    {
      // matches popular bots
      if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) 
      {
        return true;
        // watchmouse|pingdom\.com are "uptime services"
      }
    } 
    elseif ( $type == 'browser' ) 
    {
      // matches core browser types
      if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) )
      {
      	return true;
      }
    } 
    elseif ( $type == 'mobile' )
    {
      // matches popular mobile devices that have small screens and/or touch inputs
      // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
      // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
      if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) 
      {
        // these are the most common
        return true;
      } 
      elseif ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) 
      {
        // these are less common, and might not be worth checking
        return true;
      }
    }
    elseif( $type == 'firefox' )
    {
    	if ( strpos( $user_agent, 'firefox' ) !== false )
      {
      	return true;
      }
    }
    return false;
	}
	
	public function user_agent_is_IOS() 
	{
    $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
    
		if( preg_match ( "/ipod|iphone|ipad/", $user_agent ) )
		{
			return 'true';
		}
		else
		{
			return 'false';
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
		           'war_soundy_front_end.pp_button_is_inserted = true;' .
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