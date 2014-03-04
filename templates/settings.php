<div class="wrap"> 
	<h2>Soundy Plugin Settings</h2>
	
	<p>All these settings are default settings for all pages and posts</p>
	
	<form method="post" action="options.php">
	<?php settings_fields( 'war_soundy' ); ?>
	<script>
	<?php
		if( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] == 'true' )
		{
			?>
	    if( ! sessionStorage.getItem( 'war_soundy_tab_index' ) )  
	      sessionStorage.setItem( 'war_soundy_tab_index', 0 ); 
		  <?php
		}
		else
		{
			?>
			sessionStorage.setItem( 'war_soundy_tab_index', 0 );
		  <?php
		}
	?>
	jQuery( document ).ready( function( $ ) {
	  $( "#tabs" ).tabs( 
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
	</script>
	
	<div id="tabs">
	  <ul>
	    <li><a href="#war_soundy_audio_track">Audio Track</a></li>
	    <li><a href="#war_soundy_play_pause_button">Play/Pause Button</a></li>
	    <li><a href="#war_soundy_play_pause_position_corner">Play/Pause Corner Position</a></li>
	    <li><a href="#war_soundy_play_pause_position_static">Play/Pause Static Position</a></li>
	  </ul>
	  <div id="war_soundy_audio_track">
	  	<?php $this->do_settings_section( 'soundy', 'war_soundy_settings_section_audio_track' ); ?>
	  </div>
	  <div id="war_soundy_play_pause_button">
	  	<?php $this->do_settings_section( 'soundy', 'war_soundy_settings_section_play_pause_button' ); ?>
	  </div>
	  <div id="war_soundy_play_pause_position_corner">
	  	<?php $this->do_settings_section( 'soundy', 'war_soundy_settings_section_play_pause_position_corner' ); ?>
	  </div>
	  <div id="war_soundy_play_pause_position_static">
	  	<?php $this->do_settings_section( 'soundy', 'war_soundy_settings_section_play_pause_position_static' ); ?>
	  </div>
	</div>
		
	<?php submit_button(); ?>
	</form>
</div>
