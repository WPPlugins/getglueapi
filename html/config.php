<div class="wrap">
	<div id="poststuff" style="padding-top:30px;">
		<div style="width:25%; float:right;">
		<div class="postbox" style="padding-bottom:40px;">
			<h3><?php _e( 'Like this plugin?', 'GetGlueAPI' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'Please consider one of the following ...', 'GetGlueAPI' ); ?>
					<ul style="list-style: disc inside none;">
						<li><?php _e( 'Enable the credit link in the widget', 'GetGlueAPI' ); ?></li>
						<li><?php _e( 'Give it a rating on', 'GetGlueAPI' ); ?> <a href="http://wordpress.org/extend/plugins/getglueapi/" target="_blank">WordPress.org</a></li>
						<li><?php _e( 'Donate for development', 'GetGlueAPI' ); ?>
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">
								<input type="hidden" name="cmd" value="_s-xclick">
								<input type="hidden" name="hosted_button_id" value="GYR6PYQPTNM8E">
								<input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
								<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
							</form>
						</li>
					</ul>
				</p>
			</div>
		</div>
		<div class="postbox">
			<h3><?php _e( 'Showcase your site @NZGuru', 'GetGlueAPI' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'Tell me about your site and I will feature it on my users page...', 'GetGlueAPI' ); ?>
					<form target="_blank" method="post" action="http://nzguru.net/cool-stuff/getglueapi-plugin-for-wordpress/who-is-using-getglueapi">
						<fieldset class="options">
							<input type="hidden" value="12" id="link_category" name="link_category">
							<input type="hidden" value="hot" id="ll_customcaptchaanswer" name="ll_customcaptchaanswer">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="link_name"><?php _e('Your Sites Name', 'GetGlueAPI') ?></label>
											<input type="text" id="link_name" name="link_name" style="width:100%" />
										</th>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="link_name"><?php _e('Your Sites URL', 'GetGlueAPI') ?></label>
											<input type="text" id="link_url" name="link_url" style="width:100%" />
										</th>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="link_notes"><?php _e('Site Description', 'GetGlueAPI') ?></label>
											<textarea id="link_notes" name="link_notes" style="width:100%"></textarea>
										</th>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="ll_reciprocal"><?php _e('Link to page showing plugin', 'GetGlueAPI') ?></label>
											<input type="text" id="ll_reciprocal" name="ll_reciprocal" style="width:100%" />
											<span class="description"><?php _e('(Please ensure that you are linking back to NZGuru in at least 1 plugin)', 'GetGlueAPI') ?></span>
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<p class="submit">
							<input type="submit" name="submit" class="button-primary" value="<?php _e('Add Link', 'GetGlueAPI'); ?>" />
						</p>
					</form>
				</p>
			</div>
		</div>
		</div>
		<div id="GetGlueAPI_Feedback">
			<?php
			if( $error = $this->errors->get_error_message() ) {
				echo '<div class="error"><p>' . $error . '</p></div>';
			}
			if( $success = $this->success ) {
				echo '<div class="updated"><p>' . $success . '</p></div>';
			}
			?>
		</div>
		<?php
		if( '' == get_option( 'GetGlueAPI_oauth_token' ) ) {
			?>
			<div class="postbox" style="width:74%;">
				<h3><?php _e('Not connected to getglue&reg;', 'GetGlueAPI' ); ?></h3>
				<div class="inside">
					<form method="post" action="options.php">
						<fieldset class="options">
							<?php
							settings_fields( 'GetGlueAPISettings' );
							?>
							<input type="hidden" id="GetGlueAPI_interactions_api_calls" name="GetGlueAPI_interactions_api_calls" value="0" />
							<input type="hidden" id="GetGlueAPI_interactions_cache_time" name="GetGlueAPI_interactions_cache_time" value="0" />
							<p><?php _e( 'The process to set up OAuth authentication for your web site is a simple 3-steps.', 'GetGlueAPI'); ?></p>
							<h4><?php _e( '1. Register this site as an application with ', 'GetGlueAPI' ); ?><a href="https://getglue.com/api" target="_blank"><?php _e( 'GetGlue&reg;', 'GetGlueAPI' ); ?></a></h4>
							<ul>
								<li><?php _e( 'Do this by sending an email to api@getglue.com with your name, website, and the fact that you wish to use this Wordpress plugin' , 'GetGlueAPI' ); ?></li>
							</ul>
							<p><em><?php _e('Once you receive a reply, you will be provided with two keys.', 'GetGlueAPI'); ?></em></p>
							<h4><?php _e('2. Copy and paste your Client ID and Client Secret into the fields below' , 'GetGlueAPI'); ?></h4>
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_client_id"><?php _e('Client ID', 'GetGlueAPI') ?></label>
										</th>
										<td>
											<input type="text" id="GetGlueAPI_client_id" name="GetGlueAPI_client_id" value="<?php echo get_option('GetGlueAPI_client_id'); ?>" />
											<span class="description"><?php _e('Client ID from getglue&reg;', 'GetGlueAPI') ?></span>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_client_secret"><?php _e('Client Secret', 'GetGlueAPI') ?></label>
										</th>
										<td>
											<input type="text" id="GetGlueAPI_client_secret" name="GetGlueAPI_client_secret" value="<?php echo get_option('GetGlueAPI_client_secret'); ?>" />
											<span class="description"><?php _e('Client Secret from getglue&reg;', 'GetGlueAPI') ?></span>
										</td>
									</tr>
								</tbody>
							</table>
							<?php
							if( get_option('GetGlueAPI_client_id') != '' && get_option('GetGlueAPI_client_secret') != '' ) {
								/* Build GetGlueOAuth object with client credentials. */
								$getglue = new GetGlueAPI_API( get_option( 'GetGlueAPI_client_id' ), get_option( 'GetGlueAPI_client_secret' ));
								/* Get temporary credentials. */
								$request_token = $getglue->getRequestToken( plugins_url() . '/getglueapi/GetGlueAPI.php' );
								update_option( 'GetGlueAPI_oauth_request_token', $request_token['oauth_token'] );
								update_option( 'GetGlueAPI_oauth_request_token_secret', $request_token['oauth_token_secret'] );
								?>
								<h4><?php _e('3. Now that your have your application details, you may connect to getglue&reg;' , 'GetGlueAPI'); ?></h4>
<br/>Request token <code><?php echo get_option( 'GetGlueAPI_oauth_request_token' ); ?></code>
<br/>Request token secret <code><?php echo get_option( 'GetGlueAPI_oauth_request_token_secret' ); ?></code>
								<?php
							}
							?>
						</fieldset>
						<p class="submit">
							<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update API Keys', 'GetGlueAPI'); ?>" />
							<?php
							if( get_option('GetGlueAPI_client_id') != '' && get_option('GetGlueAPI_client_secret') != '' ) {
								?>
								<a href="<?php echo $getglue->getAuthorizeURL( $request_token['oauth_token'], plugins_url() . '/getglueapi/GetGlueAPI.php' ); ?>" class="button-primary"><?php _e('Connect to getglue&reg;', 'GetGlueAPI'); ?></a>
								<?php
							}
							?>
						</p>
					</form>
				</div>
			</div>
			<?php
		}
		else {
			?>
			<div class="postbox" style="width:74%;">
				<h3><?php _e('Connected to getglue&reg;', 'GetGlueAPI' ); ?></h3>
				<div class="inside">
					<form method="post" action="options.php">
						<fieldset class="options">
							<?php
							settings_fields( 'GetGlueAPISettings' );
							?>
							<input type="hidden" id="GetGlueAPI_interactions_api_calls" name="GetGlueAPI_interactions_api_calls" value="0" />
							<input type="hidden" id="GetGlueAPI_interactions_cache_time" name="GetGlueAPI_interactions_cache_time" value="<?php echo time(); ?>" />
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_client_id"><?php _e('Client ID', 'GetGlueAPI') ?></label>
										</th>
										<td>
											<code><?php echo get_option('GetGlueAPI_client_id'); ?></code>
											<span class="description"><?php _e('Client ID from getglue&reg;', 'GetGlueAPI') ?></span>
											<input type="hidden" id="GetGlueAPI_client_id" name="GetGlueAPI_client_id" value="<?php echo get_option('GetGlueAPI_client_id'); ?>" />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_client_secret"><?php _e('Client Secret', 'GetGlueAPI') ?></label>
										</th>
										<td>
											<code><?php echo get_option('GetGlueAPI_client_secret'); ?></code>
											<span class="description"><?php _e('Client Secret from getglue&reg;', 'GetGlueAPI') ?></span>
											<input type="hidden" id="GetGlueAPI_client_secret" name="GetGlueAPI_client_secret" value="<?php echo get_option('GetGlueAPI_client_secret'); ?>" />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_oauth_token"><?php _e( 'OAuth Token', 'GetGlueAPI' ) ?></label>
										</th>
										<td>
											<code><?php echo get_option( 'GetGlueAPI_oauth_token' ); ?></code>
											<span class="description"><?php _e('OAuth Token from getglue&reg;', 'GetGlueAPI') ?></span>
											<input type="hidden" id="GetGlueAPI_oauth_token" name="GetGlueAPI_oauth_token" value="" />
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<p class="submit">
							<input type="submit" name="Submit" class="button-primary" value="<?php _e('Disconnect from getglue&reg;', 'GetGlueAPI'); ?>" />
						</p>
					</form>
				</div>
			</div>
			<div class="postbox" style="width:74%;">
				<h3><?php _e('GetGlueAPI Stats', 'GetGlueAPI' ); ?></h3>
				<div class="inside">
					<form method="post" action="options.php">
						<fieldset class="options">
							<?php
							settings_fields( 'GetGlueAPISettings' );
							?>
							<input type="hidden" id="GetGlueAPI_client_id" name="GetGlueAPI_client_id" value="<?php echo get_option('GetGlueAPI_client_id'); ?>" />
							<input type="hidden" id="GetGlueAPI_client_secret" name="GetGlueAPI_client_secret" value="<?php echo get_option('GetGlueAPI_client_secret'); ?>" />
							<input type="hidden" id="GetGlueAPI_oauth_token" name="GetGlueAPI_oauth_token" value="<?php echo get_option('GetGlueAPI_oauth_token'); ?>" />
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_interactions_cache_time"><?php _e('Interactions', 'GetGlueAPI') ?></label>
										</th>
										<td>
											<input type="text" id="GetGlueAPI_interactions_api_calls" name="GetGlueAPI_interactions_api_calls" value="<?php echo get_option('GetGlueAPI_interactions_api_calls'); ?>" />
											<span class="description"><?php _e('last cached', 'GetGlueAPI') ?> <?php echo date( 'D, d M Y H:i:s', get_option( 'GetGlueAPI_interactions_cache_time' ) ); ?></span>
											<input type="hidden" id="GetGlueAPI_interactions_cache_time" name="GetGlueAPI_interactions_cache_time" value="<?php echo time(); ?>" />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="GetGlueAPI_interactions_cache_life"><?php _e('Interactions Cache Life', 'GetGlueAPI') ?></label>
										</th>
										<td>
											<select id="GetGlueAPI_interactions_cache_life" name="GetGlueAPI_interactions_cache_life">
												<option value="0"<?php echo ( get_option( 'GetGlueAPI_interactions_cache_life' ) == 0 ) ? 'selected="selected"' : ''; ?>><?php _e( 'Never', 'GetGlueAPI' ); ?></option>
												<option value="1"<?php echo ( get_option( 'GetGlueAPI_interactions_cache_life' ) == 1 ) ? 'selected="selected"' : ''; ?>><?php _e( '1 minute', 'GetGlueAPI' ); ?></option>
												<option value="2"<?php echo ( get_option( 'GetGlueAPI_interactions_cache_life' ) == 2 ) ? 'selected="selected"' : ''; ?>><?php _e( '2 minutes', 'GetGlueAPI' ); ?></option>
												<option value="3"<?php echo ( get_option( 'GetGlueAPI_interactions_cache_life' ) == 3 ) ? 'selected="selected"' : ''; ?>><?php _e( '3 minutes', 'GetGlueAPI' ); ?></option>
												<option value="4"<?php echo ( get_option( 'GetGlueAPI_interactions_cache_life' ) == 4 ) ? 'selected="selected"' : ''; ?>><?php _e( '4 minutes', 'GetGlueAPI' ); ?></option>
												<option value="5"<?php echo ( get_option( 'GetGlueAPI_interactions_cache_life' ) == 5 ) ? 'selected="selected"' : ''; ?>><?php _e( '5 minutes', 'GetGlueAPI' ); ?></option>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<p class="submit">
							<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Cache Settings', 'GetGlueAPI'); ?>" />
						</p>
					</form>
				</div>
			</div>
			<?php
echo '
			<div class="postbox" style="width:74%;">
				<h3>GetGlueAPI Last Interactions</h3>
				<div class="inside">
';
$getglue = new GetGlueAPI_API( get_option( 'GetGlueAPI_client_id' ), get_option( 'GetGlueAPI_client_secret' ), get_option( 'GetGlueAPI_oauth_token' ), get_option( 'GetGlueAPI_oauth_token_secret' ) );
$gguser = 'allanneagle';
$content_json = $getglue->get( 'user/objects', array( 'format' => 'json', 'userId' => $gguser, 'category' => 'all', 'page' => 1, 'numItems' => 5 ) );
$content = json_decode( $content_json );
$content = $content->interactions;
	echo '<li><pre style="height:150px; overflow-y:scroll;">';
	echo var_dump( $content );
	echo '</pre></li>';
foreach( $content as $key => $interaction ){
	$object = '';
	$time = strtotime( $interaction->timestamp );
	if( ( abs( time() - $time) ) < 86400 )
		$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
	else
		$h_time = date( __('Y/m/d'), $time);
	$timestamp = ' ' . date(__('Y/m/d H:i:s'), $time) . ' ' . $h_time;
	switch( $interaction->action ){
		case 'Sticker':{
#			$object_json = $getglue->get( 'object/get', array( 'format' => 'json', 'userID' => $gguser, 'objectId' => 'http://getglue.com/stickers/' . $interaction->stickerName ) );
#			$object = json_decode( $object_json );
			$object->icon = 'http://glueimg.s3.amazonaws.com/stickers/large/' . $interaction->stickerName . '.png';
			$object->link = 'http://getglue.com/stickers/' . $interaction->stickerName;
			$content[$key]->object = $object;
			echo '<li>Earned the ' . $interaction->title . ' ' . $interaction->action . '<br/>';
			echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
			echo '<br/><pre>';
#			echo var_dump($object);
			echo '</pre>';
			echo '</li>';
			break;
		}
		case 'Liked':
		case 'Checkin':{
			$object_json = $getglue->get( 'object/get', array( 'format' => 'json', 'objectId' => $interaction->objectKey ) );
			$object_type = explode( '/', $interaction->objectKey );
			echo '<li>' . $interaction->action . $timestamp . ' ' . ( $interaction->displayVerb ? $interaction->displayVerb : $interaction->verb ) . ' ' . $interaction->title . '<br/>';
			switch( $object_type[0] ){
				case 'tv_shows':{
					$object = json_decode( $object_json );
					$object = $object->show;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'movies':{
					$object = json_decode( $object_json );
					$object = $object->movie;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'movie_stars':{
					$object = json_decode( $object_json );
					$object = $object->star;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'topics':{
					$object = json_decode( $object_json );
					$object = $object->topic;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'recording_artists':{
					$object = json_decode( $object_json );
					$object = $object->artist;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'books':{
					$object = json_decode( $object_json );
					$object = $object->book;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'music':{
					$object = json_decode( $object_json );
					$object = $object->album;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				case 'video_games':{
					$object = json_decode( $object_json );
					$object = $object->game;
			$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
					$content[$key]->object = $object;
					echo '<a href="' . $object->link . '" target="_blank"><img width="60px" src="' . $object->icon . '" /></a>';
					echo $object->liked . ' liked, ' . $object->disliked . ' disliked, ' . $object->checkedIn . ' checked in';
					echo '<br/><pre>';
#					echo var_dump($object);
					echo '</pre>';
					echo '</li>';
					break;
				}
				default:{
					$object = json_decode( $object_json );
					echo 'The category ' . $object_type[0] . ' is not currently handled. Please send the following data to me via <a href="http://nzguru.net/contact-me" target="_blank">my contact form</a> :<br/>';
					echo '<hr/><pre>' . var_dump( $object ) . '</pre><hr/>';
					echo '</li>';
					break;
				}
			}
			break;
		}
		default:{
			echo '<li>default -> ' . $interaction->action . ' ' . $interaction->verb . ' ' . $interaction->title . '<br/>';
			echo '<pre>';
			echo var_dump( $interaction );
			echo '</pre>';
			echo '</li>';
			break;
		}
	}
}
echo '</pre>';
echo '
				</div>
			</div>
';
echo '
			<div class="postbox" style="width:74%;">
				<h3>Current Cache</h3>
				<div class="inside">';
echo '<div style="width:30%;display:inline-block;">Checkins<br/><pre style="height:200px; overflow-y:scroll;">';
$cache = get_option( 'GetGlueAPI_Checkin_cache' );
echo var_dump($cache);
echo '</pre></div>';
echo '<div style="width:30%;display:inline-block;">Liked<br/><pre style="height:200px; overflow-y:scroll;">';
$cache = get_option( 'GetGlueAPI_Liked_cache' );
echo var_dump($cache);
echo '</pre></div>';
echo '<div style="width:30%;display:inline-block;">Sticker<br/><pre style="height:200px; overflow-y:scroll;">';
$cache = get_option( 'GetGlueAPI_Sticker_cache' );
echo var_dump($cache);
echo '</pre></div>';
echo '
				</div>
			</div>
';
		}
		?>
	</div>
</div>
