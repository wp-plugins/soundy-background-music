function war_SoundyAdmin( mode, args )
{
	var _this = this;

	jQuery( document ).ready( function() 
	{
		if( mode == 'settings' )
		{
			_this.initSettingsTabs( args );
		}
		else
		{
			_this.initMetaBox( args );
		}
	} );
}

war_SoundyAdmin.prototype.initSettingsTabs = function( args )
{
	var _this = this;

	_this.default_button_dimensions = args.default_button_dimensions;
	_this.default_play_button_url   = args.default_play_button_url;
	_this.default_play_hover_url    = args.default_play_hover_url;
	_this.default_pause_button_url  = args.default_pause_button_url;
	_this.default_pause_hover_url   = args.default_pause_hover_url;
	_this.default_audio_url         = args.default_audio_url;
	_this.default_audio_title       = args.default_audio_title;
	_this.default_audio_volume      = args.default_audio_volume;
	_this.is_pro                    = args.is_pro;
	_this.is_trial                  = args.is_trial;
	_this.soundy_pro_home_url       = args.soundy_pro_home_url;

    if( ! sessionStorage.getItem( 'war_soundy_tab_index' ) )  
      sessionStorage.setItem( 'war_soundy_tab_index', 0 ); 

	  jQuery( '#war_soundy_tabs' ).tabs( 
  	{ 
  		active: sessionStorage.war_soundy_tab_index,
  		activate : function( event, ui )
  							 {
          			 	 //  Get future value
          				 var new_index = ui.newTab.index();
          				 sessionStorage.setItem( 'war_soundy_tab_index', new_index );
          			 } 
    } );

		// Prevent Enter Key to submit the form:
  	jQuery( document ).keydown( function( event )
  	{
    	if( event.keyCode == 13 ) 
    	{
        event.preventDefault();
        if( jQuery( event.target ).is( 'input' ) ) 
        {
        	jQuery( event.target ).change();
        }
      	return false;
    	}
  	} );
	
	_this.bindMediaUploader( 'war_soundy_audio_file_url',   'war_audio_library_button',        'audio' );
	_this.bindMediaUploader( 'war_soundy_url_play_button',  'img_play_button_library_button',  'image' );
	_this.bindMediaUploader( 'war_soundy_url_play_hover',   'img_play_hover_library_button',   'image' );
	_this.bindMediaUploader( 'war_soundy_url_pause_button', 'img_pause_button_library_button', 'image' );
	_this.bindMediaUploader( 'war_soundy_url_pause_hover',  'img_pause_hover_library_button',  'image' );
	_this.initBuySoundyPro();
	_this.initAudioFileURL();
	_this.initAudioVolume();
	_this.initDefaultAudio();
	_this.initPlayPauseImagesToUse();
	_this.initButtonImgUrls();
	_this.initSwapNormalHover();
	_this.initDefaultButtons();
	_this.initImgPreviewHere();
	_this.initImgPreviewInContextDefault();
	_this.initPlayPausePosition();
	_this.initLengthUnits();
	_this.initImgPreviewInContextPosition();
}

war_SoundyAdmin.prototype.initMetaBox = function( args )
{
	var _this = this;
	
	_this.default_audio_url    = args.default_audio_url;
	_this.default_audio_title  = args.default_audio_title;
	_this.default_audio_volume = args.default_audio_volume;
	
	_this.bindMediaUploader( 'war_soundy_audio_file_url', 'war_audio_library_button', 'audio' );
	_this.initSoundTrack( _this.default_audio_url );
	_this.initAudioVolume( true );
	_this.initAudioTitle();
}

war_SoundyAdmin.prototype.initBuySoundyPro = function()
{
	var _this = this;
	
	if( _this.is_pro && ! _this.is_trial ) return;
	
	var jquery_pro_buy = jQuery( '#war_soundy_pro_buy' );
	jquery_pro_buy.click( function()
	{
		window.open( _this.soundy_pro_home_url, 'soundy_pro_home' );
	} );
}

war_SoundyAdmin.prototype.initSoundTrack = function()
{
	var _this = this;
	
	if( jQuery( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked' ) )
	{
		jQuery( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
	}
	
	jQuery( '#war_soundy_audio_file_url' ).click( function()
	{
		jQuery( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked', true );
		jQuery( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
	} );

	jQuery( '#war_soundy_audio_file_url' ).change( function()
	{
		jQuery( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked', true );
		jQuery( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
	} );

	jQuery( '#war_soundy_soundtrack_default' ).change( function() // called when Default radio button is clicked
	{
		var audio_type = _this.getAudioTypeFromURL( _this.default_audio_url );
		
		var player_was_playing = ! jQuery( '#war_soundy_audio_player' )[ 0 ].paused;
			 			
		jQuery( '#war_soundy_audio_file_url' ).val( _this.default_audio_url );
		jQuery( '#war_soundy_audio_player_source' ).attr( 'src', _this.default_audio_url );
		jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
		jQuery( '#war_soundy_audio_player' )[ 0 ].load();
		
		if( player_was_playing ) jQuery( '#war_soundy_audio_player' )[ 0 ].play();
		jQuery( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '' );
	} );

	jQuery( '#war_soundy_soundtrack_custom' ).change( function() // called when Custom radio button is clicked
	{
		jQuery( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
	} );
}

war_SoundyAdmin.prototype.initAudioVolume = function( is_meta_box )
{
	var _this = this;

  var audio_player = jQuery( '#war_soundy_audio_player' ).get( 0 );
  var audio_volume_jquery = jQuery( '#war_soundy_audio_volume' );
  var audio_volume = audio_volume_jquery.val();
  audio_player.volume = audio_volume / 100;
  var audio_volume_slider_jquery = jQuery( '#war_soundy_audio_volume_slider' );

  audio_volume_slider_jquery.slider( 
	{ 
		min:     0,
		max:     100,
		value:   audio_volume,
		range:   'min',
		animate: true,
		slide:   function( event, ui )
		       	 {
		       		 audio_player.volume = ui.value / 100;
		       		 audio_volume_jquery.val( ui.value );
		       	 }
  } );

	audio_volume_jquery.change( function()
															{
																audio_volume_slider_jquery.slider( 'value', this.value );
		       											audio_player.volume = this.value / 100;
															} );

	if( is_meta_box )
	{
		if( jQuery( 'input[name=war_soundy_audio_volume_def][value=custom]' ).prop( 'checked' ) )
		{
			audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
		}

		audio_volume_jquery.click( function()
		{
			jQuery( 'input[name=war_soundy_audio_volume_def][value=custom]' ).prop( 'checked', true );
			audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
		} );

		var audio_volume_jquery_default = jQuery( '#war_soundy_audio_volume_default' );
		audio_volume_jquery_default.change( function() // called when Default radio button is clicked
		{
		  audio_volume_jquery.val( _this.default_audio_volume );
			audio_volume_slider_jquery.slider( 'value', _this.default_audio_volume );
			audio_player.volume = _this.default_audio_volume / 100;
			audio_volume_jquery.css( 'backgroundColor', '' );
		} );
		
		audio_volume_slider_jquery.on( "slidestart", function( event, ui )
		{
			jQuery( 'input[name=war_soundy_audio_volume_def][value=custom]' ).prop( 'checked', true );
			audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
		} );
		
		var audio_volume_jquery_custom = jQuery( '#war_soundy_audio_volume_custom' );
		audio_volume_jquery_custom.change( function() // called when Custom radio button is clicked
		{
			audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
			audio_volume_slider_jquery.css( 'backgroundColor', '#c0e7f0' );
		} );
	}
}

war_SoundyAdmin.prototype.initAudioTitle = function()
{
	var _this = this;

	if( jQuery( 'input[name=war_soundy_audio_title_def][value=custom]' ).prop( 'checked' ) )
	{
		jQuery( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
	}
	
	jQuery( '#war_soundy_audio_title' ).click( function()
	{
		jQuery( 'input[name=war_soundy_audio_title_def][value=custom]' ).prop( 'checked', true );
		jQuery( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
	} );

	jQuery( '#war_soundy_audio_title_default' ).change( function() // called when Default radio button is clicked
	{
		jQuery( '#war_soundy_audio_title' ).val( _this.default_audio_title );
		jQuery( '#war_soundy_audio_title' ).css( 'backgroundColor', '' );
	} );

	jQuery( '#war_soundy_audio_title_custom' ).change( function() // called when Custom radio button is clicked
	{
		jQuery( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
	} );
}

// Not used anymore:
war_SoundyAdmin.prototype.setDefaultButtonURL = function( button_type, url )
{
	var _this = this;

	jQuery( '#war_soundy_url_' + button_type ).val( url );
	jQuery( '#war_soundy_url_' + button_type + '_img' ).attr( 'src', url );

	switch( button_type )
	{
		case 'play_button':
			var button_name = 'Play Button';
			break;
		case 'play_hover':
			var button_name = 'Play Hover';
			break;
		case 'pause_button':
			var button_name = 'Pause Button';
			break;
		case 'pause_hover':
			var button_name = 'Pause Hover';
			break;
	}

	alert( button_name + ' Image URL reset to default.\n' +
	       'You still have to save the changes.' );
	
	if( event.preventDefault ) event.preventDefault(); else event.returnValue = false;
}

war_SoundyAdmin.prototype.initSwapNormalHover = function()
{
	jQuery( '#war_soundy_button_swap_normal_hover' ).click( function()
	{
		var url_play_button = jQuery( '#war_soundy_url_play_button' ).val();
		var url_play_hover  = jQuery( '#war_soundy_url_play_hover' ).val();
		jQuery( '#war_soundy_url_play_button' ).val( url_play_hover );
		jQuery( '#war_soundy_url_play_button' ).change();
		jQuery( '#war_soundy_url_play_hover' ).val(  url_play_button );
		jQuery( '#war_soundy_url_play_hover' ).change();
		
		var url_pause_button = jQuery( '#war_soundy_url_pause_button' ).val();
		var url_pause_hover  = jQuery( '#war_soundy_url_pause_hover' ).val();
		jQuery( '#war_soundy_url_pause_button' ).val( url_pause_hover );
		jQuery( '#war_soundy_url_pause_button' ).change();
		jQuery( '#war_soundy_url_pause_hover' ).val(  url_pause_button );		
		jQuery( '#war_soundy_url_pause_hover' ).change();
	} );
}

war_SoundyAdmin.prototype.initDefaultButtons = function() 
{
	var _this = this;

	var dims = [ 24, 32, 48, 64 ];
	for( var index in dims )
	{
		var dim = dims[ index ];
		jQuery( '#button_default_buttons_' + dim ).click( function()
		{
			var dimensions = this.value;
			var url_play_button  = _this.default_play_button_url.replace(  _this.default_button_dimensions, dimensions );
			var url_play_hover   = _this.default_play_hover_url.replace(   _this.default_button_dimensions, dimensions );
			var url_pause_button = _this.default_pause_button_url.replace( _this.default_button_dimensions, dimensions );
			var url_pause_hover  = _this.default_pause_hover_url.replace(  _this.default_button_dimensions, dimensions );
	
			jQuery( '#war_soundy_url_play_button' ).val( url_play_button );
			jQuery( '#war_soundy_url_play_button' ).change();
			jQuery( '#war_soundy_url_play_button_img' ).attr( 'src', url_play_button );
		
			jQuery( '#war_soundy_url_play_hover' ).val( url_play_hover );
			jQuery( '#war_soundy_url_play_hover' ).change();
			jQuery( '#war_soundy_url_play_hover_img' ).attr( 'src', url_play_hover );
		
			jQuery( '#war_soundy_url_pause_button' ).val( url_pause_button );
			jQuery( '#war_soundy_url_pause_button' ).change();
			jQuery( '#war_soundy_url_pause_button_img' ).attr( 'src', url_pause_button );
		
			jQuery( '#war_soundy_url_pause_hover' ).val( url_pause_hover );
			jQuery( '#war_soundy_url_pause_hover' ).change();
			jQuery( '#war_soundy_url_pause_hover_img' ).attr( 'src', url_pause_hover );
		} );
	}
}

war_SoundyAdmin.prototype.initImgPreviewHere = function() 
{
	var _this = this;

	var jquery_img_preview_here = jQuery( '#war_soundy_img_preview_here' );
	
	_this.img_url_play_button  = jQuery( '#war_soundy_url_play_button' ).val();
	_this.img_url_play_hover   = jQuery( '#war_soundy_url_play_hover' ).val();
	_this.img_url_pause_button = jQuery( '#war_soundy_url_pause_button' ).val();
	_this.img_url_pause_hover  = jQuery( '#war_soundy_url_pause_hover' ).val();

	//jquery_img_preview_here.css( 'background-image', 'url(' + jquery_img_data_grid.val() + ')' );
	jquery_img_preview_here.attr( 'src', _this.img_url_play_button );
	jquery_img_preview_here.fadeIn();
	
	var hovering    = false;
	var is_play_img = true;
	
	function displayImgPreviewHere()
	{
		if( hovering )
		{
			if( is_play_img )
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_play_hover );
			}
			else
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_pause_hover );
			}
		}
		else
		{
			if( is_play_img )
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_play_button );
			}
			else
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_pause_button );
			}
		}
	}

	var types = [ 'play_button', 'play_hover', 'pause_button', 'pause_hover' ];
	for( var index in types )
	{
		var type = types[ index ];
		jQuery( '#war_soundy_url_' + type ).change( function() 
		{
			var url = this.value;
			var type = this.id.replace( 'war_soundy_url_', '' );
			eval( '_this.img_url_' + type + '= url' );
			displayImgPreviewHere();
		} );
	}

	jquery_img_preview_here.hover( 
		function ()
		{
			hovering = true;
			if( is_play_img )
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_play_hover );
			}
			else
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_pause_hover );
			}
		},
		function ()
		{
			hovering = false;
			if( is_play_img )
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_play_button );
			}
			else
			{
				jquery_img_preview_here.attr( 'src', _this.img_url_pause_button );
			}
		} );

	jquery_img_preview_here.click( function ()
	{
		if( is_play_img )
		{
			jquery_img_preview_here.attr( 'src', _this.img_url_pause_hover );
			is_play_img = false;
		}
		else
		{
			jquery_img_preview_here.attr( 'src', _this.img_url_play_hover );
			is_play_img = true;
		}
	} );
}

war_SoundyAdmin.prototype.initImgPreviewInContextDefault = function()
{
	var _this = this;
	
	var jquery_button_preview_in_context = jQuery( '#war_soundy_button_preview_in_context_default' );
	var jquery_page_preview_url          = jQuery( '#war_soundy_page_preview_url_default' );
	
	jquery_button_preview_in_context.click( function ()
	{
		var page_url = jquery_page_preview_url.val();
		window.open( page_url + '?war_soundy_preview=default', 'soundy_preview' );
	} );
}

war_SoundyAdmin.prototype.initImgPreviewInContextPosition = function()
{
	var _this = this;
	
	var jquery_button_preview_in_context = jQuery( '#war_soundy_button_preview_in_context_position' );
	var jquery_page_preview_url          = jQuery( '#war_soundy_page_preview_url_position' );
	
	if( ( ! _this.is_pro ) || _this.is_trial )
	{
		var preview = 'default';
	}
	else
	{
		var preview = 'position';
	}
	
	jquery_button_preview_in_context.click( function ()
	{
		var page_url = jquery_page_preview_url.val();
		window.open( page_url + '?war_soundy_preview=' + preview, 'soundy_preview' );
	} );
}

war_SoundyAdmin.prototype.initDefaultAudio = function()
{
	var _this = this;

	var url        = _this.default_audio_url;
	var title      = _this.default_audio_title;
	var volume     = _this.default_audio_volume;
	var audio_type = _this.getAudioTypeFromURL( url );
	
	var player_was_playing = ! jQuery( '#war_soundy_audio_player' )[ 0 ].paused;
	
	jQuery( '#war_audio_default_button' ).click( function()
	{
		jQuery( '#war_soundy_audio_file_url' ).val( url );
		jQuery( '#war_soundy_audio_title' ).val( title );
		jQuery( '#war_soundy_audio_player_source' ).attr( 'src', url );
		jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
		jQuery( '#war_soundy_audio_player' )[ 0 ].load();
		jQuery( '#war_soundy_audio_player' )[ 0 ].volume = volume / 100;
		jQuery( '#war_soundy_audio_volume' ).val( volume );
		jQuery( '#war_soundy_audio_volume_slider' ).slider( 'value', volume );
		
		if( player_was_playing ) jQuery( '#war_soundy_audio_player' )[ 0 ].play();
		
		alert( 'Audio File URL reset to default.\n' +
		       'Audio Title reset to default.\n' + 
		       'Audio Volume reset to default.\n' + 
		       'You still have to save the changes.' );
	} );
}

war_SoundyAdmin.prototype.initLengthUnits = function()
{
	var _this = this;

	var unit_map = 
	{ 
		'px' : '(pixels)', 
		'%'  : '(percentage)', 
		'in' : '(inches)',
		'mm' : '(millimeters)',
		'cm' : '(centimeters)'
	};

	jQuery( '#war_soundy_offset_x_unit' ).change( function()
	{
		var unit = this.options[ this.selectedIndex ].value;
		jQuery( '#war_soundy_unit_comment_x' ).html( unit_map[ unit ] );
	} );

	jQuery( '#war_soundy_offset_y_unit' ).change( function()
	{
		var unit = this.options[ this.selectedIndex ].value;
		jQuery( '#war_soundy_unit_comment_y' ).html( unit_map[ unit ] );
	} );
}

war_SoundyAdmin.prototype.initPlayPausePosition = function()
{
	var _this = this;

	var position_map = 
	{ 
		'document' : '(absolute positioning: button will scroll with page content)', 
		'window'   : '(fixed positioning: button will NOT scroll with page content)'
	};

	jQuery( '#war_soundy_pp_position' ).change( function()
	{
		var position_type = this.options[ this.selectedIndex ].value;
		jQuery( '#war_soundy_pp_comment' ).html( position_map[ position_type ] );
	} );
}

war_SoundyAdmin.prototype.initButtonImgUrls = function()
{
	var _this = this;

	var types = [ 'play_button', 'play_hover', 'pause_button', 'pause_hover' ];
	for( var index in types )
	{
		var type = types[ index ];
		jQuery( '#war_soundy_url_' + type ).change( function() 
		{
			var url = this.value;
			jQuery( '#' + this.id + '_img' ).attr( 'src', url );
		} );
	}
}

war_SoundyAdmin.prototype.getAudioTypeFromURL = function( url )
{
	var _this = this;

	var file_extension = url.substr( url.lastIndexOf( '.' ) + 1 );
	var audio_type = '';
	
	switch( file_extension )
	{
		case 'mp3':
		case 'mpg':
		case 'mpeg':
			audio_type = 'mpeg';
			break;
		case 'ogg':
			audio_type = 'ogg';
			break;
		case 'wav':
		case 'wave':
			audio_type = 'wav';
			break;
	}
	
	return audio_type;
}

war_SoundyAdmin.prototype.initAudioFileURL = function( url )
{
	var _this = this;

	jQuery( '#war_soundy_audio_file_url').change( function()
	{
		var url = this.value;	
		var audio_type = _this.getAudioTypeFromURL( url );
			 			
		jQuery( '#war_soundy_audio_player_source' ).attr( 'src', url );
		jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
		jQuery( '#war_soundy_audio_player' )[ 0 ].load();
	} );
}

war_SoundyAdmin.prototype.bindMediaUploader = function( field_name, button_name, field_type )
{
	var _this = this;

	jQuery( '#' + button_name ).click( function() 
	{
		jQuery( '#TB_window' ).html( '' ); // to avoid multiple title bars
 		tb_show( '', 'media-upload.php?type=' + field_type + '&amp;TB_iframe=true');

		window.send_to_editor = function( html )
		{
			if( field_type == 'image' )
			{
	 			var url = jQuery( 'img', html ).attr( 'src' );
	 			jQuery( '#' + field_name + '_img' ).attr( 'src', url );
	 		}
	 		else if( field_type == 'audio' )
	 		{
	 			var url = jQuery( html ).attr( 'href' );
	 			var file_extension = url.substr( url.lastIndexOf( '.' ) + 1 );
	 			switch( file_extension )
	 			{
	 				case 'mp3':
	 				case 'mpeg':
	 					var audio_type = 'mpeg';
	 					break;
	 				case 'ogg':
	 					var audio_type = 'ogg';
	 					break;
	 				case 'wav':
	 					var audio_type = 'wav';
	 					break;
	 				default:
	 					alert( 'Audio field_type Error' );
	 					return;
	 			}
	 			var player_was_playing = ! jQuery( '#war_soundy_audio_player' )[ 0 ].paused;
	 			jQuery( '#war_soundy_audio_player_source' ).attr( 'src', url );
	 			jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
	 			jQuery( '#war_soundy_audio_player' )[ 0 ].load();
	 			if( player_was_playing ) jQuery( '#war_soundy_audio_player' )[ 0 ].play();

	 			var title = jQuery( html ).text().trim();
	 			if( title != '' )
	 			{
	 				jQuery( '#war_soundy_audio_title' ).val( title );
	 			} 
	 		}
	 		
	 		var jq = jQuery( '#' + field_name );
	 		jq.val( url );
	 		jq.change();
	 		
	 		tb_remove();
	 		if( jQuery( '#war_soundy_soundtrack_default' ).length ) // war_soundy_soundtrack_default exists if meta box
	 		{
	 			jQuery( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked', true );
				jQuery( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
	 			jQuery( 'input[name=war_soundy_audio_title_def][value=custom]' ).prop( 'checked', true );
				jQuery( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
	 		}
		}
		
 		return false;
	} );			 
}

war_SoundyAdmin.prototype.initPlayPauseImagesToUse = function()
{
	var _this = this;

	_this.pp_images_to_use = jQuery( 'input[name=war_soundy_pp_images_to_use]:checked' ).val();
	
	jQuery( 'input[name=war_soundy_pp_images_to_use]' ).change( function()
	{
		_this.pp_images_to_use = jQuery( 'input[name=war_soundy_pp_images_to_use]:checked' ).val();
	} );
}
