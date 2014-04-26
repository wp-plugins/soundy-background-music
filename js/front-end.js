function war_SoundyFrontEnd(
	pp_code,
	audio_code,
	audio_volume,
	preview,
	button_url_play_normal,
	button_url_pause_normal,
	button_url_play_hover,
	button_url_pause_hover,
	user_agent_is_IOS ) 
{
	var _this = this;
	
	_this.pp_code                 = pp_code;
	_this.audio_code              = audio_code;
	_this.audio_volume            = audio_volume;
	_this.preview                 = preview;
	_this.button_url_play_normal  = button_url_play_normal;
	_this.button_url_pause_normal = button_url_pause_normal;
	_this.button_url_play_hover   = button_url_play_hover;
	_this.button_url_pause_hover  = button_url_pause_hover;
	_this.user_agent_is_IOS       = user_agent_is_IOS;

	_this.pp_button_is_inserted   = false;
	_this.hovering                = false;
	
	jQuery( document ).ready( function() 
	{
		if( _this.pp_code && ! _this.pp_button_is_inserted )
		{
			jQuery( 'body' ).append( _this.pp_code );
			_this.pp_button_is_inserted = true;
		}
		
		jQuery( 'body' ).append( _this.audio_code );
		
		_this.audio_control = jQuery( '#war_soundy_audio_control' );
		_this.audio_player  = jQuery( '#war_soundy_audio_player' );
		_this.audio_player_element  = _this.audio_player[ 0 ];
		
		_this.audio_player_element.volume = audio_volume;

		if( _this.preview != 'false' )
		{
			// Workaround for the preview player hanging:
			var opener_player = window.opener.jQuery( '#war_soundy_audio_player' )[ 0 ];
			opener_player.play();
			opener_player.pause();

			if( _this.preview == 'position' )
			{
				var pp_images_to_use = window.opener.jQuery( 'input[name=war_soundy_pp_images_to_use]:checked' ).val();
				
				if( pp_images_to_use == 'none' )
				{
					_this.preview = 'default';
				}
				else
				{
					_this.preview = pp_images_to_use;
				}
			}

			if( _this.preview == 'designer' )
			{
				_this.button_url_play_normal  = window.opener.war_pp_design.img_data_play_normal;
				_this.button_url_pause_normal = window.opener.war_pp_design.img_data_pause_normal;
				_this.button_url_play_hover   = window.opener.war_pp_design.img_data_play_hover;
				_this.button_url_pause_hover  = window.opener.war_pp_design.img_data_pause_hover;
			}
			else if( _this.preview == 'default' )
			{
				_this.button_url_play_normal  = window.opener.jQuery( '#war_soundy_url_play_button' ).val();
				_this.button_url_pause_normal = window.opener.jQuery( '#war_soundy_url_pause_button' ).val();
				_this.button_url_play_hover   = window.opener.jQuery( '#war_soundy_url_play_hover' ).val();
				_this.button_url_pause_hover  = window.opener.jQuery( '#war_soundy_url_pause_hover' ).val();
			}
			
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
							
		if( _this.pp_button_is_inserted )
		{
			_this.initPPButton();
		}
		
		/*
		jQuery( window ).bind( 'beforeunload', function()
		{
			var volume = _this.audio_player.prop( 'volume' );
			for( var i = 10000000000; i > 0; i-- )
				{
					// 1 slow      3:  middle     6: fast
					volume -= 0.00000001 * 1;
					if( volume < 0 ) break;
					if( ( i % 10000 ) == 0 )
					{
						_this.audio_player.prop( 'volume', volume );
					}
				}
		} );
		*/
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
}