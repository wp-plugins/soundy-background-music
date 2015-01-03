function war_SoundyFrontEnd( args )
{
	var _this = this;

	_this.pp_code                 = args.pp_code;
	_this.audio_code              = args.audio_code;
	_this.audio_volume            = args.audio_volume;
	_this.preview                 = args.preview;
	_this.button_url_play_normal  = args.button_url_play_normal;
	_this.button_url_pause_normal = args.button_url_pause_normal;
	_this.button_url_play_hover   = args.button_url_play_hover;
	_this.button_url_pause_hover  = args.button_url_pause_hover;
	_this.user_agent_is_IOS       = args.user_agent_is_IOS;

	_this.hovering                = false;

    jQuery.noConflict();
	jQuery( document ).ready( function()
	{
        if( _this.pp_code )
        {
            if( ! jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).length )
            {
                jQuery( 'body' ).append( _this.pp_code );
            }
        }

        if( ! jQuery( '#war_soundy_audio_player' ).length )
        {
            jQuery( 'body' ).append( _this.audio_code );
        }

		_this.audio_control = jQuery( '.war_soundy_audio_control' );
		_this.audio_player  = jQuery( '#war_soundy_audio_player' );
		_this.audio_player_element  = _this.audio_player[ 0 ];

		_this.audio_player_element.volume = _this.audio_volume;

		if( _this.preview != 'false' )
		{
			// Workaround for the preview player hanging:
			var opener_player = window.opener.jQuery( '#war_soundy_audio_player' )[ 0 ];
			opener_player.play();
			opener_player.pause();

            _this.button_url_play_normal  = window.opener.jQuery( '#war_soundy_url_play_button' ).val();
            _this.button_url_pause_normal = window.opener.jQuery( '#war_soundy_url_pause_button' ).val();
            _this.button_url_play_hover   = window.opener.jQuery( '#war_soundy_url_play_hover' ).val();
            _this.button_url_pause_hover  = window.opener.jQuery( '#war_soundy_url_pause_hover' ).val();

			if( _this.audio_player_element.autoplay )
			{
				_this.audio_player_element.load();
				_this.audio_player_element.play();
				_this.audio_control.attr( 'src', _this.button_url_pause_normal );
			}
			else
			{
				_this.audio_control.attr( 'src', _this.button_url_play_normal );
			}
			
			var position = window.opener.jQuery( '#war_soundy_pp_position' ).children( 'option:selected' ).val();
			if( position == 'document' )
			{
				_this.audio_control.css( 'position', 'absolute' );
			}
			else
			{
				_this.audio_control.css( 'position', 'fixed' );
			}

			var pp_corner = window.opener.jQuery( '#war_soundy_pp_corner' ).children( 'option:selected' ).val();
			var offset_x  = window.opener.jQuery( '#war_soundy_offset_x' ).val() + window.opener.jQuery( '#war_soundy_offset_x_unit' ).val();
			var offset_y  = window.opener.jQuery( '#war_soundy_offset_y' ).val() + window.opener.jQuery( '#war_soundy_offset_y_unit' ).val();
			switch( pp_corner )
			{
				case 'upper_right': 
					_this.audio_control.css( 'top',    offset_y );
					_this.audio_control.css( 'right',  offset_x );
					_this.audio_control.css( 'bottom', '' );
					_this.audio_control.css( 'left',   '' );
					break;
				case 'upper_left': 
					_this.audio_control.css( 'top',    offset_y );
					_this.audio_control.css( 'right',  '' );
					_this.audio_control.css( 'bottom', '' );
					_this.audio_control.css( 'left',   offset_x );
					break;
				case 'bottom_right': 
					_this.audio_control.css( 'top',    '' );
					_this.audio_control.css( 'right',  offset_x );
					_this.audio_control.css( 'bottom', offset_y );
					_this.audio_control.css( 'left',   '' );
					break;
				case 'bottom_left': 
					_this.audio_control.css( 'top',    '' );
					_this.audio_control.css( 'right',  '' );
					_this.audio_control.css( 'bottom', offset_y );
					_this.audio_control.css( 'left',   offset_x );
					break;
			}
		}

		_this.initPPButton();
	} );
}

war_SoundyFrontEnd.prototype.animateVolume = function( audio_player_element, new_volume )
{
	var _this = this;
	
	var volume = audio_player_element.volume;
	
	function iterateVolume()
	{
		var volume_diff = Math.abs( volume - new_volume );
		if( volume_diff > 0.1 && volume < new_volume )
		{
			volume += 0.1;
		}
		else if( volume_diff > 0.1 && volume > new_volume )
		{
			volume -= 0.1;
		}
		else
		{
			clearInterval( interval_id );
			return;
		}
		audio_player_element.volume = volume;
	}
	
	var interval_id = setInterval( iterateVolume, 400 );
}


war_SoundyFrontEnd.prototype.initPPButton = function()
{
	var _this = this;

    if( _this.preview == 'false' )
    {
        var is_pp_template_tag = jQuery( '.war_soundy_pp_template_tag' ).length > 0;
        var is_pp_short_code   = jQuery( '.war_soundy_pp_short_code' ).length > 0;

        if( is_pp_short_code )
        {
            jQuery( '.war_soundy_pp_template_tag' ).hide();
            jQuery( '.war_soundy_pp_corner' ).hide();
        }
        else if( is_pp_template_tag )
        {
            jQuery( '.war_soundy_pp_corner' ).hide();
        }
    }
    if( _this.audio_player_element.paused )
    {
        _this.audio_control.attr( 'src', _this.button_url_play_normal );
    }
    else
    {
        _this.audio_control.attr( 'src', _this.button_url_pause_normal );
    }

	_this.audio_control.click( 
		function() 
		{ 
			if( _this.audio_player_element.paused )
			{
				_this.audio_player_element.play();
				_this.audio_control.attr( 'src', _this.button_url_pause_hover );
			}
			else
			{
				_this.audio_player_element.pause();
				_this.audio_control.attr( 'src', _this.button_url_play_hover );
			}
		} );
		
	_this.audio_control.hover( 
		function() 
		{ 
			_this.hovering = true;
			if( _this.audio_player_element.paused )
			{
				_this.audio_control.attr( 'src', _this.button_url_play_hover );
			}
			else
			{
				_this.audio_control.attr( 'src', _this.button_url_pause_hover );
			}
		},
		function() 
		{ 
			_this.hovering = false;
			if( _this.audio_player_element.paused )
			{
				_this.audio_control.attr( 'src', _this.button_url_play_normal );
			}
			else
			{
				_this.audio_control.attr( 'src', _this.button_url_pause_normal );
			}
		}
	);
		
	_this.audio_player.bind( 'ended' , function()
	{
		if( _this.hovering )
		{
			_this.audio_control.attr( 'src', _this.button_url_play_hover );
		}
		else
		{
			_this.audio_control.attr( 'src', _this.button_url_play_normal );
		}
	} );

	_this.audio_player.bind( 'play' , function()
    {
		if( _this.hovering )
		{
			_this.audio_control.attr( 'src', _this.button_url_pause_hover );
		}
		else
		{
			_this.audio_control.attr( 'src', _this.button_url_pause_normal );
		}
	} );
		
	if( _this.audio_player_element.autoplay )
	{
		if( _this.user_agent_is_IOS )
		{
			if( _this.hovering )
			{
				_this.audio_control.attr( 'src', _this.button_url_play_hover );
			}
			else
			{
				_this.audio_control.attr( 'src', _this.button_url_play_normal );
			}
		}
	}

    if( war_soundy_responsive_mode != 'none' )
    {
        var window_width = jQuery( window ).width();

        switch( war_soundy_button_corner )
        {
            case 'upper_right':
            default:
                var prop_x = 'right';
                var prop_y = 'top';
                break;
            case 'upper_left':
                var prop_x = 'left';
                var prop_y = 'top';
                break;
            case 'bottom_right':
                var prop_x = 'right';
                var prop_y = 'bottom';
                break;
            case 'bottom_left':
                var prop_x = 'left';
                var prop_y = 'bottom';
                break;
        }

        if( war_soundy_responsive_mode == 'table' )
        {
            for( var index in war_soundy_responsive_table_rows )
            {
                var row = war_soundy_responsive_table_rows[ index ];
                if( row.button_size != -1 )
                {
                    if( row.window_width_min != -1 && row.window_width_max != -1 )
                    {
                        if( row.window_width_min <= window_width && window_width <= row.window_width_max )
                        {
                            jQuery( '.war_soundy_audio_control' ).css( 'width', row.button_size );
                        }
                    }
                    else if( row.window_width_min != -1 )
                    {
                        if( row.window_width_min <= window_width )
                        {
                            jQuery( '.war_soundy_audio_control' ).css( 'width', row.button_size );
                        }
                    }
                    else if( row.window_width_max != -1 )
                    {
                        if( window_width <= row.window_width_max )
                        {
                            jQuery( '.war_soundy_audio_control' ).css( 'width', row.button_size );
                        }
                    }
                }
            }

            if( jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).length )
            {
                for( var index in war_soundy_responsive_table_rows )
                {
                    var row = war_soundy_responsive_table_rows[ index ];
                    if( row.offset_x != -1 || row.offset_y != -1 )
                    {
                        if( row.window_width_min != -1 && row.window_width_max != -1 )
                        {
                            if( row.window_width_min <= window_width && window_width <= row.window_width_max )
                            {
                                if( row.offset_x != -1 ) jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).css( prop_x, row.offset_x );
                                if( row.offset_y != -1 ) jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).css( prop_y, row.offset_y );
                            }
                        }
                        else if( row.window_width_min != -1 )
                        {
                            if( row.window_width_min <= window_width )
                            {
                                if( row.offset_x != -1 ) jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).css( prop_x, row.offset_x );
                                if( row.offset_y != -1 ) jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).css( prop_y, row.offset_y );
                            }
                        }
                        else if( row.window_width_max != -1 )
                        {
                            if( window_width <= row.window_width_max )
                            {
                                if( row.offset_x != -1 ) jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).css( prop_x, row.offset_x );
                                if( row.offset_y != -1 ) jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' ).css( prop_y, row.offset_y );
                            }
                        }
                    }
                }
            }
        }
        else // if( war_soundy_responsive_mode == 'scale' )
        {
            var scale_factor = ( window_width * 0.7 / war_soundy_responsive_reference_window_width ) + 0.3;

            jQuery( '.war_soundy_audio_control' ).load
            (
                function()
                {
                    var button_size = jQuery( this ).width();
                    var responsive_button_size = Math.round( button_size * scale_factor );
                    jQuery( this ).css( 'width', responsive_button_size );

                    var jquery_corner_button = jQuery( '.war_soundy_audio_control.war_soundy_pp_corner' );
                    if( jquery_corner_button.length )
                    {
                        var offset_x = jquery_corner_button.css( prop_x );
                        offset_x = offset_x.replace( 'px', '' );
                        var offset_y = jquery_corner_button.css( prop_y );
                        offset_y = offset_y.replace( 'px', '' );
                        var responsive_offset_x = Math.round( offset_x * scale_factor );
                        var responsive_offset_y = Math.round( offset_y * scale_factor );

                        jquery_corner_button.css( prop_x, responsive_offset_x );
                        jquery_corner_button.css( prop_y, responsive_offset_y );
                    }

                    jQuery( '.war_soundy_audio_control' ).unbind( 'load' );
                }
            );
        }
    }
}