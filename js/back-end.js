function war_initTabs()
{
	jQuery( document ).ready( function( $ ) 
	{
    if( ! sessionStorage.getItem( 'war_soundy_tab_index' ) )  
      sessionStorage.setItem( 'war_soundy_tab_index', 0 ); 

	  $( '#war_soundy_tabs' ).tabs( 
  	{ 
  		active: sessionStorage.war_soundy_tab_index,
  		activate : function( event, ui )
  							 {
          			 	 //  Get future value
          				 var new_index = ui.newTab.index();
          				 sessionStorage.setItem( 'war_soundy_tab_index', new_index );
          			 } 
    } );
	} );
}

function war_initSoundTrack( default_url )
{
	jQuery( document ).ready( function( $ ) 
	{
		if( $( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked' ) )
		{
			$( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
		}
		
		$( '#war_soundy_audio_file_url' ).click( function()
		{
			$( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked', true );
			$( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
		} );

		$( '#war_soundy_audio_file_url' ).change( function()
		{
			$( 'input[name=war_soundy_soundtrack][value=custom]' ).prop( 'checked', true );
			$( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
		} );

		$( '#war_soundy_soundtrack_default' ).change( function() // called when Default radio button is clicked
		{
			var audio_type = war_getAudioTypeFromURL( default_url );
			
			var player_was_playing = ! jQuery( '#war_soundy_audio_player' )[ 0 ].paused;
				 			
			jQuery( '#war_soundy_audio_file_url' ).val( default_url );
			jQuery( '#war_soundy_audio_player_source' ).attr( 'src', default_url );
			jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
			jQuery( '#war_soundy_audio_player' )[ 0 ].load();
			
			if( player_was_playing ) jQuery( '#war_soundy_audio_player' )[ 0 ].play();
			$( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '' );
		} );

		$( '#war_soundy_soundtrack_custom' ).change( function() // called when Custom radio button is clicked
		{
			$( '#war_soundy_audio_file_url' ).css( 'backgroundColor', '#c0e7f0' );
		} );
	} );
}

function war_initAudioVolume( is_meta_box, default_volume )
{
	jQuery( document ).ready( function( $ ) 
	{
    var audio_player = $( '#war_soundy_audio_player' ).get( 0 );
    var audio_volume_jquery = $( '#war_soundy_audio_volume' );
    var audio_volume = audio_volume_jquery.val();
    audio_player.volume = audio_volume / 100;
    var audio_volume_slider_jquery = $( '#war_soundy_audio_volume_slider' );

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
			if( $( 'input[name=war_soundy_audio_volume_def][value=custom]' ).prop( 'checked' ) )
			{
				audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
			}

			audio_volume_jquery.click( function()
			{
				$('input[name=war_soundy_audio_volume_def][value=custom]').prop( 'checked', true );
				audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
			} );

			var audio_volume_jquery_default = $( '#war_soundy_audio_volume_default' );
			audio_volume_jquery_default.change( function() // called when Default radio button is clicked
			{
  		  audio_volume_jquery.val( default_volume );
				audio_volume_slider_jquery.slider( 'value', default_volume );
				audio_player.volume = default_volume / 100;
				audio_volume_jquery.css( 'backgroundColor', '' );
			} );
			
			audio_volume_slider_jquery.on( "slidestart", function( event, ui )
			{
				$('input[name=war_soundy_audio_volume_def][value=custom]').prop( 'checked', true );
				audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
			} );
			
			var audio_volume_jquery_custom = $( '#war_soundy_audio_volume_custom' );
			audio_volume_jquery_custom.change( function() // called when Custom radio button is clicked
			{
				audio_volume_jquery.css( 'backgroundColor', '#c0e7f0' );
				audio_volume_slider_jquery.css( 'backgroundColor', '#c0e7f0' );
			} );
		}
	} );
}

function war_initAudioTitle( default_title )
{
	jQuery( document ).ready( function( $ ) 
	{
		if( $( 'input[name=war_soundy_audio_title_def][value=custom]' ).prop( 'checked' ) )
		{
			$( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
		}
		
		$( '#war_soundy_audio_title' ).click( function()
		{
			$( 'input[name=war_soundy_audio_title_def][value=custom]' ).prop( 'checked', true );
			$( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
		} );

		$( '#war_soundy_audio_title_default' ).change( function() // called when Default radio button is clicked
		{
			jQuery( '#war_soundy_audio_title' ).val( default_title );
			$( '#war_soundy_audio_title' ).css( 'backgroundColor', '' );
		} );

		$( '#war_soundy_audio_title_custom' ).change( function() // called when Custom radio button is clicked
		{
			$( '#war_soundy_audio_title' ).css( 'backgroundColor', '#c0e7f0' );
		} );
	} );
}

function war_setDefaultButtonURL( button_type, url )
{
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

function war_setDefaultButtons( dimensions, default_dimensions, url_play_button,
                                           										  url_play_hover,
                                           										  url_pause_button,
                                           										  url_pause_hover )
{
	url_play_button  = url_play_button.replace( default_dimensions, dimensions );
	url_play_hover   = url_play_hover.replace( default_dimensions, dimensions );
	url_pause_button = url_pause_button.replace( default_dimensions, dimensions );
	url_pause_hover  = url_pause_hover.replace( default_dimensions, dimensions );

	jQuery( '#war_soundy_url_play_button' ).val( url_play_button );
	jQuery( '#war_soundy_url_play_button_img' ).attr( 'src', url_play_button );

	jQuery( '#war_soundy_url_play_hover' ).val( url_play_hover );
	jQuery( '#war_soundy_url_play_hover_img' ).attr( 'src', url_play_hover );

	jQuery( '#war_soundy_url_pause_button' ).val( url_pause_button );
	jQuery( '#war_soundy_url_pause_button_img' ).attr( 'src', url_pause_button );

	jQuery( '#war_soundy_url_pause_hover' ).val( url_pause_hover );
	jQuery( '#war_soundy_url_pause_hover_img' ).attr( 'src', url_pause_hover );
}

function war_setDefaultAudio( url, title, volume )
{
	var audio_type = war_getAudioTypeFromURL( url );
	
	var player_was_playing = ! jQuery( '#war_soundy_audio_player' )[ 0 ].paused;
		 			
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
}

function war_lengthUnitChanged( element )
{
	var element_value = element.options[ element.selectedIndex ].value;
	var unit_map = { 
							 		 'px' : '(pixels)', 
		           		 '%'  : '(percentage)', 
		           		 'in' : '(inches)',
		           		 'mm' : '(millimeters)',
		           		 'cm' : '(centimeters)'
		         		 };
	switch( element.name )
	{
		case 'war_soundy_offset_x_unit':
			jQuery( '#war_soundy_unit_comment_x' ).html( unit_map[ element_value ] );
			break;
		case 'war_soundy_offset_y_unit':
			jQuery( '#war_soundy_unit_comment_y' ).html( unit_map[ element_value ] );
			break;
	}
}

function war_ppPositionChanged( element )
{
	var element_value = element.options[ element.selectedIndex ].value;
	var position_map = { 
							 		 'document' : '(absolute position)', 
		           		 'window'   : '(fixed position)'
		         		 };
	jQuery( '#war_soundy_pp_comment' ).html( position_map[ element_value ] );
}

function war_imgUrlChanged( element )
{
	jQuery( '#' + element.name + '_img' ).attr( 'src', element.value );
}

function war_getAudioTypeFromURL( url )
{
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

function war_audioUrlChanged( element )
{
	var url = element.value;	
	var audio_type = war_getAudioTypeFromURL( url );
		 			
	jQuery( '#war_soundy_audio_player_source' ).attr( 'src', url );
	jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
	jQuery( '#war_soundy_audio_player' )[ 0 ].load();
}

function war_bindMediaUploader( field_name, button_name, field_type )
{
	jQuery( document ).ready( function()
	{
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
		 		
		 		jQuery( '#' + field_name ).val( url );
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
	} );
}
