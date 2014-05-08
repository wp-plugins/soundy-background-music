<div class="wrap">
	<?php 
		$is_pro = 'false';
		$is_trial = 'false';
		$name = ' ';
		$buy = '<button id="war_soundy_pro_buy" type="button" class="war_soundy" style="margin-left: 250px;">Try Soundy PRO For Free</button>';
		$tip = '<span style="color: #2eaecc; margin-left: 125px;">Soundy PRO and its HTML5 Play/Pause Button Designer !</span>';
	?>
	<h2>Soundy<?php echo $name; ?>Plugin Settings <?php echo $buy; ?></h2>
	
	<p>All these settings are default settings for all pages and posts.<?php echo $tip; ?></p>
	
	<form method="post" action="options.php">
	<?php 
	  settings_fields( 'war_soundy' );
	?>
	<script>
		var war_soundy_admin = new war_SoundyAdmin( 
			'settings',
			{
				default_button_dimensions: '<?php echo $this->button_dimensions; ?>',
				default_play_button_url:   '<?php echo $this->play_button_url; ?>',
				default_play_hover_url:    '<?php echo $this->play_hover_url; ?>',
				default_pause_button_url:  '<?php echo $this->pause_button_url; ?>',
				default_pause_hover_url:	 '<?php echo $this->pause_hover_url; ?>',
				default_audio_url:         '<?php echo $this->audio_url; ?>',
				default_audio_title:       '<?php echo str_replace( "&#039;", "\&#039;", $this->audio_title ); ?>',
				default_audio_volume:      '<?php echo $this->audio_volume; ?>',
				is_pro:                     <?php echo $is_pro; ?>,
				is_trial:                   <?php echo $is_trial; ?>,
				plugin_url:                '<?php echo $this->plugin_url; ?>',
				soundy_pro_home_url:       '<?php echo $this->soundy_pro_home_url; ?>'
			} );
	</script>
	
	<div id="war_soundy_tabs">
	  <ul>
	    <li><a id="war_soundy_tab_label_audio_track"                  href="#war_soundy_audio_track">Audio Track</a></li>
	    <li><a id="war_soundy_tab_label_play_pause_button"            href="#war_soundy_play_pause_button">Play/Pause Button</a></li>
	    <li><a id="war_soundy_tab_label_play_pause_position_corner"   href="#war_soundy_play_pause_position_corner">Play/Pause Corner Position</a></li>
	    <li><a id="war_soundy_tab_label_play_pause_position_static"   href="#war_soundy_play_pause_position_static">Play/Pause Static Position</a></li>
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
