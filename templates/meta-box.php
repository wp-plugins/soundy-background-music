
<script>
var war_soundy_admin = new war_SoundyAdmin(
		'meta_box',
		{
			default_audio_url:    '<?php echo $default_audio_url; ?>',
			default_audio_volume: '<?php echo $default_audio_volume; ?>',
			default_audio_title:  '<?php echo $default_audio_title; ?>'
		} );
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
		<label for="war_soundy_audio_file_url">Soundtrack</label>
	</th>
	<td>
		<?php $this->add_field_audio_file_URL( true ); ?>
	</td>
</tr>
<tr>
	<th class="war_soundy">
		<label for="war_soundy_audio_title">Audio Volume</label>
	</th>
	<td>
		<div style="margin: 5px 5px 5px 0px">
			<input type="radio" 
						 id="war_soundy_audio_volume_default" 
						 name="war_soundy_audio_volume_def" 
						 value="default" <?php echo ( $audio_volume_is_default ? 'checked' : '' ); ?>/>
			<label for="war_soundy_audio_volume_default" style="margin-right: 1em;">Default</label>
			
			<input type="radio" 
						 id="war_soundy_audio_volume_custom" 
						 name="war_soundy_audio_volume_def" 
						 value="custom" <?php echo ( $audio_volume_is_default ? '' : 'checked' ); ?>/>
			<label for="war_soundy_audio_volume_custom" style="margin-right: 1em;">Custom</label>
		</div>
		<div id="war_soundy_audio_volume_slider" style="width: 300px; display: inline-block; margin: 0 10px 0 0;"></div>
		<input type="text"
					 class="war_soundy_audio_volume"
		       value="<?php echo $audio_volume; ?>"
				       name="war_soundy_audio_volume" 
				       id="war_soundy_audio_volume" /> %
	</td>
</tr>
<tr>
	<th class="war_soundy">
		<label for="war_soundy_audio_title">Audio Title</label>
	</th>
	<td>
		<div style="margin: 5px 5px 5px 0px">
			<input type="radio" 
						 id="war_soundy_audio_title_default" 
						 name="war_soundy_audio_title_def" 
						 value="default" <?php echo ( $audio_title_is_default ? 'checked' : '' ); ?>/>
			<label for="war_soundy_audio_title_default" style="margin-right: 1em;">Default</label>
			
			<input type="radio" 
						 id="war_soundy_audio_title_custom" 
						 name="war_soundy_audio_title_def" 
						 value="custom" <?php echo ( $audio_title_is_default ? '' : 'checked' ); ?>/>
			<label for="war_soundy_audio_title_custom" style="margin-right: 1em;">Custom</label>
		</div>
		<input type="text"
					 class="war_soundy_txt_input"
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
