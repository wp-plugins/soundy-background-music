<?php

class WarSoundyFrontEnd
{
    private $soundy; // WarSoundy Object

    private $button_url_play;
    private $hover_url_play;
    private $button_url_pause;
    private $hover_url_pause;

    private $preview;
    private $post_id;
    private $user_agent_is_mobile;

    public function __construct( $soundy_object )
    {
        $this->soundy = $soundy_object;
        $this->user_agent_is_mobile = $this->soundy->check_user_agent( 'mobile' );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_front_end' ) );
        add_action( 'wp_head', array( $this, 'insert_audio' ) );
        add_shortcode( 'soundy', array( $this, 'process_shortcode' ) );
    }

    public function enqueue_scripts_front_end( $hook )
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-effects-core' );

        wp_register_script( 'soundy-front-end', $this->soundy->plugin_url . '/js/front-end.js', array( 'jquery' ) );
        wp_enqueue_script( 'soundy-front-end' );

        wp_register_style( 'jquery-ui', $this->soundy->plugin_url . '/css/jquery-ui-1.10.4/jquery-ui.css' );
        wp_register_style( 'soundy_front_end', $this->soundy->plugin_url . '/css/style-front-end.css' );

        wp_enqueue_style( 'jquery-ui' );
        wp_enqueue_style( 'soundy_front_end' );
    }

    public function activate()
    {
    }

    public function insert_audio()
    {
        $this->soundy->post_id = $this->post_id = get_the_ID();

        $audio_file_url = $this->soundy->get_meta_data( 'war_soundy_audio_file_url' );
        $audio_type     = $this->soundy->get_meta_data( 'war_soundy_audio_type' );

        $preview = $_GET[ 'war_soundy_preview' ];
        if( $preview )
        {
            $this->preview = $preview;
        }
        else
        {
            $this->preview = 'false';
            $enable_bg_sound = $this->soundy->get_meta_data( 'war_soundy_enable_bg_sound' );
            if( $enable_bg_sound != 'yes' ) return;
            if( $audio_file_url == '' ) return;
        }

        if( $this->user_agent_is_mobile && $this->soundy->disable_soundy_for_mobile ) return;

        $audio_volume = $this->soundy->get_meta_data( 'war_soundy_audio_volume' ) / 100;

        $auto_play = $this->soundy->get_meta_data( 'war_soundy_autoplay' );
        $auto_play = ( $auto_play == 'yes' ) ? 'autoplay' : '';

        $repeat_loop = $this->soundy->get_meta_data( 'war_soundy_loop' );
        $audio_loop = ( $repeat_loop == 'yes' ) ? 'loop' : '';

        $pp_code = $this->get_pp_corner_code();

        $audio_code =

            '<div style="display: none;">' .
            '  <audio id="war_soundy_audio_player" preload="auto" ' . $auto_play . ' ' . $audio_loop . '>' .
            '	   <source id="war_soundy_audio_player_source" src="' . $audio_file_url . '" type="' . $audio_type . '">' .
            '  </audio>' .
            '</div>';

        $pp_code    = str_replace( array( "\n", "\r" ), ' ', $pp_code );
        $audio_code = str_replace( array( "\n", "\r" ), ' ', $audio_code );

        $responsive_mode = get_option( 'war_soundy_responsive_mode' );
        if( $responsive_mode != 'none' )
        {
            if( $responsive_mode == 'table' )
            {
                $responsive_table_rows = array();
                for( $i = 1; $i <= $this->soundy->responsive_table_number_of_rows; $i++ )
                {
                    $window_width_min = get_option( 'war_soundy_responsive_window_width_min_' . $i );
                    $window_width_min = ( $window_width_min == '' ) ? -1 : $window_width_min;
                    $window_width_max = get_option( 'war_soundy_responsive_window_width_max_' . $i );
                    $window_width_max = ( $window_width_max == '' ) ? -1 : $window_width_max;

                    if( $window_width_min != -1 || $window_width_max != -1 )
                    {
                        $button_size = get_option( 'war_soundy_responsive_button_size_' . $i );
                        $button_size = ( $button_size == '' ) ? -1 : $button_size;
                        $offset_x = get_option( 'war_soundy_responsive_offset_x_' . $i );
                        $offset_x = ( $offset_x == '' ) ? -1 : $offset_x;
                        $offset_y = get_option( 'war_soundy_responsive_offset_y_' . $i );
                        $offset_y = ( $offset_y == '' ) ? -1 : $offset_y;
                        $responsive_table_rows[] = '{ ' .
                            'window_width_min: ' . $window_width_min . ',' .
                            'window_width_max: ' . $window_width_max . ',' .
                            'button_size: '      . $button_size . ',' .
                            'offset_x: '         . $offset_x . ',' .
                            'offset_y: '         . $offset_y .
                            '}';
                    }
                }
            }
            else // if( $responsive_mode == 'scale' )
            {
                $responsive_reference_window_width = get_option( 'war_soundy_responsive_scale_reference_window_width' );
            }
        }
        ?>
            <link rel="prefetch" href="<?php echo $audio_file_url; ?>">
            <link rel="prefetch" href="<?php echo $this->button_url_play; ?>">
            <link rel="prefetch" href="<?php echo $this->button_url_pause; ?>">
            <link rel="prefetch" href="<?php echo $this->hover_url_play; ?>">
            <link rel="prefetch" href="<?php echo $this->hover_url_pause; ?>">
            <script>
                var war_soundy_front_end = new war_SoundyFrontEnd(
                {
                    pp_code:                    '<?php echo $pp_code; ?>',
                    audio_code:                 '<?php echo $audio_code; ?>',
                    audio_volume:               <?php echo $audio_volume; ?>,
                    preview:                    '<?php echo $this->preview; ?>',
                    button_url_play_normal:     '<?php echo $this->button_url_play; ?>',
                    button_url_pause_normal:    '<?php echo $this->button_url_pause; ?>',
                    button_url_play_hover:      '<?php echo $this->hover_url_play; ?>',
                    button_url_pause_hover:     '<?php echo $this->hover_url_pause; ?>',
                    user_agent_is_IOS:          <?php echo $this->user_agent_is_IOS() ?>
                } );

                var war_soundy_responsive_mode = '<?php echo $responsive_mode; ?>';
                var war_soundy_button_corner = '<?php echo get_option( "war_soundy_pp_corner" ); ?>';
                <?php
                    if( $responsive_mode != 'none' )
                    {
                        if( $responsive_mode == 'table' )
                        {
                            echo 'var war_soundy_responsive_table_rows = new Array();';
                            foreach( $responsive_table_rows as $row )
                            {
                                echo 'war_soundy_responsive_table_rows.push( ' . $row . ' );';
                            }
                        }
                        else // if( $responsive_mode == 'scale' )
                        {
                            echo 'var war_soundy_responsive_reference_window_width = ' . $responsive_reference_window_width . ';';
                        }
                    }
                ?>
            </script>
        <?php
    }

    public function get_pp_corner_code()
    {
        if( $this->preview == 'false' )
        {
            $enable_bg_sound = $this->soundy->get_meta_data( 'war_soundy_enable_bg_sound' );
            if( $enable_bg_sound != 'yes' ) return '';

            $this->button_url_play  = get_option( 'war_soundy_url_play_button' );
            $this->hover_url_play   = get_option( 'war_soundy_url_play_hover' );
            $this->button_url_pause = get_option( 'war_soundy_url_pause_button' );
            $this->hover_url_pause  = get_option( 'war_soundy_url_pause_hover' );
        }

        $pp_corner_enable = $this->soundy->get_meta_data( 'war_soundy_enable_pp_corner' );
        if( $pp_corner_enable == 'yes' )
        {
            $button_position  = get_option( 'war_soundy_pp_position' );
            $position = ( $button_position == 'document' ) ? 'absolute' : 'fixed';

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

            $pp_code = '<img class="war_soundy_audio_control war_soundy_pp_corner" style="' . $position_css_code . '">';
        }
        else
        {
            $pp_code = '';
        }

        return $pp_code;
    }

    public function process_shortcode( $atts )
    {
        if( $atts[ 0 ] && $atts[ 0 ] == 'title' )
        {
            return soundy_get_title();
        }
        elseif( $atts[ 0 ] && $atts[ 0 ] == 'button' )
        {
            return soundy_get_button( 'pp_short_code' );
        }
    }

    public function user_agent_is_IOS()
    {
        $user_agent = strtolower ( $_SERVER[ 'HTTP_USER_AGENT' ] );

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

?>