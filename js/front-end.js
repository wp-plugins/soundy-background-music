function war_setDefaultButtonURL( button_type, url )
{
	jQuery( '#war_soundy_button_url_' + button_type ).val( url );
	jQuery( '#war_soundy_img_button_url_' + button_type ).attr( 'src', url );
	
	return false;
}

function war_setDefaultAudioURL( url )
{
	var audio_type = war_getAudioTypeFromURL( url );
		 			
	jQuery( '#war_soundy_audio_file_url' ).val( url );
	jQuery( '#war_soundy_audio_player_source' ).attr( 'src', url );
	jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
	jQuery( '#war_soundy_audio_player' )[ 0 ].load();
	
	return false;
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

function war_imgUrlChanged( element )
{
	switch( element.name )
	{
		case 'war_soundy_button_url_play':
			jQuery( '#war_soundy_img_button_url_play' ).attr( 'src', element.value );
			break;
		case 'war_soundy_button_url_pause':
			jQuery( '#war_soundy_img_button_url_pause' ).attr( 'src', element.value );
			break;
	}
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
	 		tb_show( '', 'media-upload.php?type=' + field_type + '&amp;TB_iframe=true');
	
			window.send_to_editor = function( html ) 
			{
				if( field_type == 'image' )
				{
		 			var url = jQuery( 'img', html ).attr( 'src' );
		 			jQuery( '#img_' + field_name ).attr( 'src', url );
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
		 			jQuery( '#war_soundy_audio_player_source' ).attr( 'src', url );
		 			jQuery( '#war_soundy_audio_player_source' ).attr( 'type', 'audio/' + audio_type );
		 			jQuery( '#war_soundy_audio_player' )[ 0 ].load();
		 		}
		 		
		 		jQuery( '#' + field_name ).val( url );
		 		tb_remove();
			}
			
	 		return false;
		} );			 
	} );
}
