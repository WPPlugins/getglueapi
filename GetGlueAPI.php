<?php
/*
=== GetGlueAPI ===

Plugin Name: GetGlueAPI
Plugin URI: http://nzguru.net/cool-stuff/getglueapi-plugin-for-wordpress
Description: The 1st plugin to integrate the getglue&reg; API into Wordpress to show your checkins and badges
Author URI: http://nzguru.net
Author: the Guru
Version: 1.0.7
License: GPL2
	Copyright 2011  the Guru  (email : admin[at]nzguru.net)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/** code required to handle the getglue OAuth callback .. ugly, but the best I can do for now */
if( !function_exists( 'get_bloginfo' ) ) {
	require_once( 'OAuth.php' );
	require_once( '../../../wp-config.php' );
	$getglue = new GetGlueAPI_API( get_option( 'GetGlueAPI_client_id' ), get_option( 'GetGlueAPI_client_secret' ), get_option( 'GetGlueAPI_oauth_request_token' ), get_option( 'GetGlueAPI_oauth_request_token_secret' ) );
	$access_token = $getglue->getAccessToken( $_REQUEST['oauth_verifier'] );
	update_option( 'GetGlueAPI_oauth_token', $access_token['oauth_token'] );
	update_option( 'GetGlueAPI_oauth_token_secret', $access_token['oauth_token_secret'] );
	wp_redirect( get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=GetGlueAPI.php' );
}

class GetGlueAPIException extends Exception {}

class GetGlueAPI {
	function __construct() {
		if( '' == get_option( 'GetGlueAPI_oauth_token' ) ) {
			$message = sprintf( __( 'getglue&reg; requires authentication by OAuth. You will need to <a href="%s">update your settings</a> to complete installation of GetGlueAPI.', 'GetGlueAPI'), admin_url( 'options-general.php?page=GetGlueAPI.php' ) );
			add_action( 'admin_notices', create_function( '', "if ( ! current_user_can( 'manage_options' ) ) { return; } else { _e( '<div class=\"error\"><p>$message</p></div>' );}" ) );
		}
		add_action( 'admin_menu', array( $this, 'insert_admin_menu_link' ) );
#		add_action( 'init', array( $this, 'route_actions' ), 2 );
		add_action( 'init', array( $this, 'set_language' ), 1 );
		add_action( 'wp_print_styles', array( $this, 'styles' ) );
		add_action( 'wp_print_scripts', array( $this, 'scripts' ) );
		add_action( 'widgets_init', array( $this, 'initialize_widget' ) );
		add_filter( 'plugin_action_links', array( $this, 'insert_settings_link' ), 10, 2 ); 
		add_shortcode( 'getglue_interactions', array( 'GetGlueAPIInteractionsShortcode', 'interactions_shortcode' ) );
		$this->errors = new WP_Error();
	}

	function insert_admin_menu_link() {
		$page = add_submenu_page( 'options-general.php', __( 'GetGlueAPI Settings', 'GetGlueAPI' ), __( 'GetGlueAPI', 'GetGlueAPI' ), 'edit_plugins', 'GetGlueAPI', array( $this, 'config_page_html' ) );
#		add_action( 'admin_print_scripts-' . $page, array( $this, 'admin_scripts' ) );
#		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_styles' ) );
		add_action( 'admin_init', array( $this, 'register_admin_settings' ) );
	}

	function insert_settings_link( $links, $file ) {
		static $this_plugin;
		if( !$this_plugin )
			$this_plugin = plugin_basename( __FILE__ );
		if( $file == $this_plugin ) {
			$settings_link = '<a href="options-general.php?page=GetGlueAPI">' . __( 'Settings', 'GetGlueAPI' ) . '</a>'; 
			array_unshift( $links, $settings_link ); 
		}
		return $links; 
	}
	
	function admin_scripts() {
#		// wp_enqueue_script allows us to include JavaScript files.
#		wp_enqueue_script(
#			'GetGlueAPI_Admin', // This is a unique identifier for this .js file. This is how WordPress can tell if a .js file has already been loaded.
#			WP_PLUGIN_URL.'/GetGlueAPI/js/admin.js', // This is the path to the .js file
#			array( 'jquery' ) // This is an array of JavaScript files that this .js file depends on. If these files are not loaded, then WordPress will make sure to load them before your .js file. My file depends on jquery, so by entering the identifier "jquery" here, it will automatically be included. There's no need for me to include the jquery files in my plugin, because WordPress already includes the library. For a complete list of scripts available in WordPress can be found here: http://codex.wordpress.org/Function_Reference/wp_enqueue_script
#		);
#		
#		// wp_localize_script allows us to pass some variables that need to be generated with PHP into our JavaScript. For example, any text we use in our Javascript file needs to be run through our gettext function so that it can be internationalized (see chapter 4.0). Also for security purposes, when we use AJAX we need to provide our .js file with a security code, called an "nonce", to ensure that the AJAX request is coming from a valid source. We pass this variable to our JS file using the wp_localize_script function as well.
#		wp_localize_script(
#			'GetGlueAPI_Admin', // This is the unique identifier of the Javascript file that that we are localizing. This should match the unique identifier above in wp_enqueue_script
#			'GetGlueAPI', // This is the name of the object that is going to store all the variables that we set in the next parameter. So in our Javascript file, the first setting below can be accessed using GetGlueAPI.DeleteFindAndReplaceWord
#			array( // This is an array of variables that we want to send to our Javascript file. The first two are nonce security keys that we'll use in our AJAX functions to make sure the request are legit.
#				// This is an error message that we use in our JavaScript file, notice that it's wrapped in the  __() function, so that if we translate our plugin into another langauge, this error message will also be translated.
#				'AjaxError' => __('An error occurred. Please try your action again. If the problem persists please contact the plugin developer.','GetGlueAPI')
#			)
#		);
	}
	
	function admin_styles() {
#		// wp_enqueue_style lets us add CSS files to our plugin the same way we do with JavaScript files
#		wp_enqueue_style(
#			'GetGlueAPI_Admin', // Unique identifier
#			WP_PLUGIN_URL . '/GetGlueAPI/css/admin.css' // Path to the CSS file
#			// Another parameter could be added here to list dependant CSS files (e.g. a CSS Framework)
#		);
	}
	
	function styles() {
		wp_register_style( 'GetGlueAPI_CSS', WP_PLUGIN_URL . '/getglueapi/css/GetGlueAPI.css' );
		wp_enqueue_style( 'GetGlueAPI_CSS' );
	}
	
	function scripts() {
#	  wp_deregister_script( 'jquerytools' );
#	  wp_register_script( 'jquerytools', 'http://cdn.jquerytools.org/1.2.6/all/jquery.tools.min.js' );
#	  wp_enqueue_script( 'jquerytools' );
#	  wp_deregister_script( 'googlemaps' );
#		wp_register_script( 'googlemaps', 'http://maps.googleapis.com/maps/api/js?sensor=false' );
#	  wp_enqueue_script( 'googlemaps' );
	  wp_enqueue_script( 'jquery' );
	  wp_deregister_script( 'mobilyslider' );
		wp_register_script( 'mobilyslider', WP_PLUGIN_URL . '/getglueapi/js/mobilyslider.js', 'jquery' );
	  wp_enqueue_script( 'mobilyslider' );
	}
	
	function register_admin_settings() {
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_client_id' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_client_secret' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_oauth_request_token' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_oauth_request_token_secret' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_oauth_token' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_oauth_token_secret' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Checkin_api_calls' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Checkin_cache' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Checkin_cache_time' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Checkin_cache_life' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Sticker_api_calls' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Sticker_cache' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Sticker_cache_time' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Sticker_cache_life' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Liked_api_calls' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Liked_cache' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Liked_cache_time' );
		register_setting( 'GetGlueAPISettings', 'GetGlueAPI_Liked_cache_life' );
	}
	
	function config_page_html() {
		$content = '';
		ob_start(); // This function begins output buffering, this means that any echo or output that is generated after this function will be captured by php instead of being sent directly to the user.
			require_once( 'html/config.php' ); // This function includes my configuration page html. Open the file html/config.php to see how to format a configuration page to save options.
			$content = ob_get_contents(); // This function takes all the html retreived from html/config.php and stores it in the variable $content
		ob_end_clean(); // This function ends the output buffering
		echo $content; // Now I simply echo the content out to the user
	}

	function route_actions() {
#		if( !isset( $_REQUEST['GetGlueAPI_Action'] ) ) return false;
#		$action = $_REQUEST['GetGlueAPI_Action'];
#		if( $action ) { // This code verifies the nonce value that was sent with the action. This is  avery important check that ensures that a malicious user cannot use your plugin to compromise the WordPress installation. Without this check, your plugin introduces a serious security vulnerability to whomever uses the plugin.
#			check_admin_referer( $action );
#		}
#		// Pass the action name to our function that executes the actions
#		$result = $this->do_action( $action );
#		// If it was an Ajax call, then pass our action to the function that will generate the updated html
#		if( $this->is_ajax() ) {
#			$this->ajax_response( $action );
#		}
	}
	
	function do_action( $action ) {
#		$result = false;
#		switch( $action ) { // Check which action was requested, and send the required POST variables to the function to make it happen
#			case 'AddFindAndReplaceWord':
#				$result = $this->AddFindAndReplaceWord($_POST['GetGlueAPI_Find'], $_POST['GetGlueAPI_Replace']);
#			break;
#			case 'DeleteFindAndReplaceWord':
#				$result = $this->DeleteFindAndReplaceWord($_POST['GetGlueAPI_Id']);
#			break;
#			case 'AddFactCheck':
#				$result = $this->AddFactCheck($_POST['GetGlueAPI_PostId'], $_POST['GetGlueAPI_Fact'], $_POST['GetGlueAPI_Comment'], $_POST['GetGlueAPI_Source']);
#			break;
#		}
#		return $result;
	}
	
	function ajax_response( $action ) {
#		// This object will contain all the data we need to pass to our JavaScript file to update the page. This PHP variable will be converted into JavaScript Object Notation (JSON) so that our JavaScript file can use this variable.
#		$data = array();
#		/* If errors were triggered get them and add them to the error element of data array*/
#		if( $this->errors->get_error_message() ) {
#			$data['error'] = $this->errors->get_error_message();
#		}
#		elseif( $this->success ) { // If no errors were found, and a success message was set, add it to the success element of the data array
#			$data['success'] = $this->success;
#		}
#		// Check which action was requested, and send the required POST variable to the function in order to get the updated HTML elements that we need to update
#		switch( $action ) {
#			case 'AddFindAndReplaceWord':
#				$data['html'] = $this->RuleListHtml();
#			break;
#			case 'DeleteFindAndReplaceWord':
#				$data['html'] = $this->RuleListHtml();
#			break;
#			case 'AddFactCheck':
#				$data['html'] = $this->FactCheckListHtml($_POST['GetGlueAPI_PostId']);
#			break;
#		}
#		/* Die here to stop WordPress from returning a page, instead we want it to just return the $data variable in JSON format, so our jQuery function can use it. See js/admin.js or js/fact_check.js to see how we use this variable to update the page. */
#		die( json_encode( $data ) );
	}
	
	function is_ajax() {
#		if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'] ) {
#			return true;
#		}
#		return false;
	}

	function set_language() {
		load_plugin_textdomain( 'GetGlueAPI', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
	
	function initialize_widget() {
		register_widget( 'GetGlueAPIInteractionsWidget' );
	}

}

class GetGlueAPIInteractionsShortcode {
	function __construct() {
		add_action( 'init', array( __CLASS__, 'register_script' ) );
	}

	function register_script() {
	  wp_enqueue_script( 'jquery' );
	  wp_deregister_script( 'mobilyslider' );
		wp_register_script( 'mobilyslider', WP_PLUGIN_URL . '/getglueapi/js/mobilyslider.js', 'jquery' );
	  wp_enqueue_script( 'mobilyslider' );
		wp_register_style( 'GetGlueAPI_CSS',  WP_PLUGIN_URL . '/getglueapi/css/GetGlueAPI.css'  );
		wp_enqueue_style( 'GetGlueAPI_CSS' );
	}
 
	function interactions_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'type'       => 'Checkin',
			'width'      => 300,
			'stats'      => 0,
			'limit'      => 0,
			'list'       => 0,
			'autoscroll' => 0,
			'id'         => 1
		), $atts );
		return display_getglue_interactions( $atts );
	}

}

class GetGlueAPIInteractionsWidget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'description' => __( 'This widget displays your getglue&reg; interactions.', 'GetGlueAPI' ) );
		parent::WP_Widget( 'GetGlueAPIInteractionsWidget', __( 'GetGlueAPI Interactions', 'GetGlueAPI' ), $widget_ops );
		/* This function checks if this widget is currently added to any sidebars. If your widget requires external JavaScript or CSS, you should only include it if the widget is actually active. Otherwise, you'll be slowing down page loads by including these external files, when they aren't even being used! */
		if( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'template_redirect', array( $this, 'widget_external' ) );
		}
	}

	function widget_external() {
	  wp_enqueue_script( 'jquery' );
	  wp_deregister_script( 'mobilyslider' );
		wp_register_script( 'mobilyslider', WP_PLUGIN_URL . '/getglueapi/js/mobilyslider.js', 'jquery' );
	  wp_enqueue_script( 'mobilyslider' );
		wp_register_style( 'GetGlueAPI_CSS',  WP_PLUGIN_URL . '/getglueapi/css/GetGlueAPI.css'  );
		wp_enqueue_style( 'GetGlueAPI_CSS' );
	}
	
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'user'       => '',
			'type'       => 'Checkin',
			'title'      => 'GetGlue Interactions',
			'byline'     => 'what I am doing',
			'width'      => 300,
			'limit'      => 3,
			'stats'      => 1,
			'list'       => 0,
			'autoscroll' => 5000,
			'id'         => 1,
			'link'       => 1
		) );
		echo '
			<p>
			<label for="' . $this->get_field_id( 'user' ) . '">' . __( 'GetGlue Username', 'GetGlueAPI' ) . '</label>
			<input type="text" id="' . $this->get_field_id( 'user' ) . '" name="' . $this->get_field_name( 'user' ) . '" value="' . attribute_escape( $instance['user'] ) . '" class="widefat" />
			</p>
			<p>
			<label for="' . $this->get_field_id( 'type' ) . '">' . __( 'Data Type', 'GetGlueAPI' ) . '</label>
			<select id="' . $this->get_field_id( 'type' ) . '" name="' . $this->get_field_name( 'type' ) . '">
				<option value="Checkin"';
		echo ( 'Checkin' == $instance['type'] ) ? 'selected="selected"' : '';
		echo '>' . __( 'Checkins', 'GetGlueAPI' ) . '</option>
				<option value="Liked"';
		echo ( 'Liked' == $instance['type'] ) ? 'selected="selected"' : '';
		echo '>' . __( 'Likes', 'GetGlueAPI' ) . '</option>
				<option value="Sticker"';
		echo ( 'Sticker' == $instance['type'] ) ? 'selected="selected"' : '';
		echo '>' . __( 'Stickers', 'GetGlueAPI' ) . '</option>
			</select>
			</p>
			<p>
			<label for="' . $this->get_field_id( 'title' ) . '">' . __( 'Widget Title', 'GetGlueAPI' ) . '</label>
			<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' . attribute_escape( $instance['title'] ) . '" class="widefat" />
			</p>
			<p>
			<label for="' . $this->get_field_id( 'byline' ) . '">' . __( 'Widget Byline', 'GetGlueAPI' ) . '</label>
			<input type="text" id="' . $this->get_field_id( 'byline' ) . '" name="' . $this->get_field_name( 'byline' ) . '" value="' . attribute_escape( $instance['byline'] ).'" class="widefat" />
			</p>
			<p>
			<label for="' . $this->get_field_id( 'width' ) . '">' . __( 'Widget Width', 'GetGlueAPI' ) . ' <code>' . __( '(in px)', 'GetGlueAPI' ) . '</code></label>
			<input type="text" id="' . $this->get_field_id( 'width' ) . '" name="' . $this->get_field_name( 'width' ) . '" value="' . attribute_escape( $instance['width'] ) . '" class="widefat" />
			</p>
			<p>
			<label for="' . $this->get_field_id( 'limit' ) . '">' . __( 'Qty Limit', 'GetGlueAPI' ) . ' <code>' . __( '(max 30)', 'GetGlueAPI' ) . '</code></label>
			<input type="text" id="' . $this->get_field_id( 'limit' ) . '" name="' . $this->get_field_name( 'limit' ) . '" value="' . attribute_escape( $instance['limit'] ).'" class="widefat" />
			</p>
			<p>
				<input type="checkbox" id="' . $this->get_field_id( 'stats' ) . '" name="' . $this->get_field_name( 'stats' ) . '" value="1" ';
		echo ( true == $instance['stats'] ) ? 'checked="checked"' : '';
		echo ' />
				<label for="' . $this->get_field_id( 'stats' ) . '">' . __( 'Show Stats', 'GetGlueAPI' ) . '</label>
				<br/>
				<input type="checkbox" id="' . $this->get_field_id( 'list' ) . '" name="' . $this->get_field_name( 'list' ) . '" value="1" ';
		echo ( true == $instance['list'] ) ? 'checked="checked"' : '';
		echo ' />
				<label for="' . $this->get_field_id( 'list' ) . '">' . __( 'List instead of scrolling box', 'GetGlueAPI' ) . '</label>
			</p>
			<p>
			<label for="' . $this->get_field_id( 'autoscroll' ) . '">' . __( 'Autoscroll Rate', 'GetGlueAPI' ) . ' <code>' . __( '(in milliseconds, 0 to disable, ignored if list)', 'GetGlueAPI' ) . '</code></label>
			<input type="text" id="' . $this->get_field_id( 'autoscroll' ) . '" name="' . $this->get_field_name( 'autoscroll' ) . '" value="' . attribute_escape( $instance['autoscroll'] ) . '" class="widefat" />
			</p>
			<p>
			<label for="' . $this->get_field_id( 'id' ) . '">' . __( 'Unique ID', 'GetGlueAPI' ) . '</label>
			<input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" value="' . attribute_escape( $instance['id'] ) . '" class="widefat" />
			</p>
			<p>
				<input type="checkbox" id="' . $this->get_field_id( 'link' ) . '" name="' . $this->get_field_name( 'link' ) . '" value="1" ';
		echo ( true == $instance['link'] ) ? 'checked="checked"' : '';
		echo ' />
				<label for="' . $this->get_field_id( 'link' ) . '">' . __( 'Link back to NZGuru (I appreciate it)', 'GetGlueAPI' ) . '</label>
			</p>
			';
	}
	
	function update( $new_instance, $old_instance ) {
		$instance['user']       = $new_instance['user'];
		$instance['type']       = $new_instance['type'];
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['byline']     = strip_tags( $new_instance['byline'] );
		$instance['width']      = intval( $new_instance['width'] );
		$instance['limit']      = max( 1, min( 30, intval( $new_instance['limit'] ) ) );
		$instance['stats']      = intval( $new_instance['stats'] );
		$instance['list']       = intval( $new_instance['list'] );
		$instance['autoscroll'] = intval( $new_instance['autoscroll'] );
		$instance['id']         = intval( $new_instance['id'] );
		$instance['link']       = intval( $new_instance['link'] );
		return $instance;
	}

	function widget( $args, $instance ) {
		global $GetGlueAPI;
		echo $args['before_widget'];
		if( $instance['title'] )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		if( $instance['byline'] )
			echo '<div class="entry-meta">' . $instance['byline'] . '</div>';
		echo display_getglue_interactions( $instance );
		if( $instance['link'] )
			echo '<div style="font-style:italic;font-size:0.6em;text-align:right;">' . __( 'powered by', 'GetGlueAPI' ) . ' <a href="http://nzguru.net/cool-stuff/getglueapi-plugin-for-wordpress" target="_blank">GetGlueAPI</a></span>';
		echo $args['after_widget'];
	}

}

/* Load OAuth lib. You can find it at http://oauth.net */
require_once('OAuth.php');

class GetGlueAPI_API {
  /* Contains the last HTTP status code returned. */
  public $http_code;
  /* Contains the last API call. */
  public $url;
  /* Set up the API root URL. */
  public $host = "http://api.getglue.com/v2/";
  /* Set timeout default. */
  public $timeout = 30;
  /* Set connect timeout. */
  public $connecttimeout = 30; 
  /* Verify SSL Cert. */
  public $ssl_verifypeer = FALSE;
  /* Respons format. */
#  public $format = 'json';
  /* Decode returned json data. */
  public $decode_json = TRUE;
  /* Contains the last HTTP headers returned. */
  public $http_info;
  /* Set the useragnet. */
  public $useragent = 'GetGlueOAuth v0.2.0-beta2';

  /**
   * Set API URLS
   */
  function requestTokenURL() { return 'https://api.getglue.com/oauth/request_token'; }
  function authorizeURL()    { return 'http://getglue.com/oauth/authorize'; }
  function accessTokenURL()  { return 'https://api.getglue.com/oauth/access_token'; }
  function authenticateURL() { return 'http://getglue.com/oauth/authorize'; }

  /**
   * Debug helpers
   */
  function lastStatusCode() { return $this->http_status; }
  function lastAPICall() { return $this->last_api_call; }

  /**
   * construct GetGlueOAuth object
   */
  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    } else {
      $this->token = NULL;
    }
  }


  /**
   * Get a request_token from GetGlue
   *
   * @returns a key/value array containing oauth_token and oauth_token_secret
   */
  function getRequestToken($oauth_callback = NULL) {
    $parameters = array();
    if (!empty($oauth_callback)) {
      $parameters['oauth_callback'] = $oauth_callback;
    } 
    $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * Get the authorize URL
   *
   * @returns a string
   */
  function getAuthorizeURL($token, $callback = '', $sign_in_with_twitter = TRUE) {
    if (is_array($token)) {
      $token = $token['oauth_token'];
    }
    if (empty($sign_in_with_twitter)) {
      return $this->authorizeURL() . '?oauth_token=' . $token . ( !empty($callback) ? '&oauth_callback=' . $callback : '' );
    } else {
       return $this->authenticateURL() . '?oauth_token=' . $token . ( !empty($callback) ? '&oauth_callback=' . $callback : '' );
    }
  }

  /**
   * Exchange request token and secret for an access token and
   * secret, to sign API calls.
   *
   * @returns array("oauth_token" => "the-access-token",
   *                "oauth_token_secret" => "the-access-secret",
   *                "user_id" => "9436992",
   *                "screen_name" => "abraham")
   */
  function getAccessToken($oauth_verifier = FALSE) {
    $parameters = array();
    if (!empty($oauth_verifier)) {
      $parameters['oauth_verifier'] = $oauth_verifier;
    }
    $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * One time exchange of username and password for access token and secret.
   *
   * @returns array("oauth_token" => "the-access-token",
   *                "oauth_token_secret" => "the-access-secret",
   *                "user_id" => "9436992",
   *                "screen_name" => "abraham",
   *                "x_auth_expires" => "0")
   */  
  function getXAuthToken($username, $password) {
    $parameters = array();
    $parameters['x_auth_username'] = $username;
    $parameters['x_auth_password'] = $password;
    $parameters['x_auth_mode'] = 'client_auth';
    $request = $this->oAuthRequest($this->accessTokenURL(), 'POST', $parameters);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * GET wrapper for oAuthRequest.
   */
  function get($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'GET', $parameters);
#    if ($this->format === 'json' && $this->decode_json) {
#      return json_decode($response);
#    }
    return $response;
  }
  
  /**
   * POST wrapper for oAuthRequest.
   */
  function post($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'POST', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response);
    }
    return $response;
  }

  /**
   * DELETE wrapper for oAuthReqeust.
   */
  function delete($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'DELETE', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response);
    }
    return $response;
  }

  /**
   * Format and sign an OAuth / API request
   */
  function oAuthRequest($url, $method, $parameters) {
    if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}";//.{$this->format}";
    }
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);
    switch ($method) {
    case 'GET':
      return $this->http($request->to_url(), 'GET');
    default:
      return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
    }
  }

  /**
   * Make an HTTP request
   *
   * @return API results
   */
  function http($url, $method, $postfields = NULL) {
    $this->http_info = array();
    $ci = curl_init();
    /* Curl settings */
		if( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			curl_setopt( $ci, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		}
		else {
			// Handle the useragent like we are Google Chrome
			curl_setopt( $ci, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.X.Y.Z Safari/525.13.' );
		}
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }

    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
    $this->url = $url;
    curl_close ($ci);
    return $response;
  }

  /**
   * Get the header info to store.
   */
  function getHeader($ch, $header) {
    $i = strpos($header, ':');
    if (!empty($i)) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->http_header[$key] = $value;
    }
    return strlen($header);
  }
}

function display_getglue_interactions( $options ) {
	global $wp_plugin_dir, $wp_plugin_url;
	$image_path = plugin_dir_path( __FILE__ ) . '/images/';
	$image_url = plugin_dir_url( __FILE__ ) . '/images/';
	$gguser = $options['user'];
	$cache = get_option( 'GetGlueAPI_' . $options['type'] . '_cache' );
	$cache_life = get_option( 'GetGlueAPI_' . $options['type'] . '_cache_life' );
	$cache_time = get_option( 'GetGlueAPI_' . $options['type'] . '_cache_time' );
	if( '' == $cache || 0 == $cache_life || time() > $cache_time + ( 60 * $cache_life ) ) {
		// Load the GetGlue API library
		$getglue = new GetGlueAPI_API( get_option( 'GetGlueAPI_client_id' ), get_option( 'GetGlueAPI_client_secret' ), get_option( 'GetGlueAPI_oauth_token' ), get_option( 'GetGlueAPI_oauth_token_secret' ) );
		$interactions_json = $getglue->get( 'user/objects', array( 'format' => 'json', 'userId' => $gguser, 'action' => $options['type'], 'category' => 'all', 'page' => 1, 'numItems' => $options['limit'] ) );
		$interactions = json_decode( $interactions_json );
		$interactions = $interactions->interactions;
		foreach( $interactions as $key => $interaction ){
			$object = '';
			switch( $interaction->action ){
				case 'Sticker':{
					$object->icon = 'http://glueimg.s3.amazonaws.com/stickers/large/' . $interaction->stickerName . '.png';
					$object->link = 'http://getglue.com/stickers/' . $interaction->stickerName;
					$interactions[$key]->object = $object;
					break;
				}
				case 'Liked':
				case 'Checkin':{
					$object_json = $getglue->get( 'object/get', array( 'format' => 'json', 'objectId' => $interaction->objectKey ) );
					$object_type = explode( '/', $interaction->objectKey );
					switch( $object_type[0] ){
						case 'tv_shows':{
							$object = json_decode( $object_json );
							$object = $object->show;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'movies':{
							$object = json_decode( $object_json );
							$object = $object->movie;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'movie_stars':{
							$object = json_decode( $object_json );
							$object = $object->star;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'topics':{
							$object = json_decode( $object_json );
							$object = $object->topic;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'recording_artists':{
							$object = json_decode( $object_json );
							$object = $object->artist;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'books':{
							$object = json_decode( $object_json );
							$object = $object->book;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'music':{
							$object = json_decode( $object_json );
							$object = $object->album;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
						case 'video_games':{
							$object = json_decode( $object_json );
							$object = $object->game;
							$object->icon = 'http://adaptiveblue.img.s3.amazonaws.com/' . $interaction->objectKey . '/small';
							$interactions[$key]->object = $object;
							break;
						}
					}
					break;
				}
			}
		}
#		if( 200 == $venues->meta->code ) {
			update_option( 'GetGlueAPI_' . $options['type'] . '_cache', $interactions );
			update_option( 'GetGlueAPI_' . $options['type'] . '_cache_time', time() );
			update_option( 'GetGlueAPI_' . $options['type'] . '_api_calls', get_option( 'GetGlueAPI_' . $options['type'] . '_api_calls' ) + 1 );
#		}
#		else
#			$venues = $cache;
	}
	else
		$interactions = $cache;
	$html = ''; $i = 0; $last_interaction = '';
	$html .= '<div id="GetGlueAPI_interactions' . $options['id'] . '" class="GetGlueAPI_interactions" style="width: ' . $options['width'] . 'px">';
	$height = 67 + ( 40 * $options['stats'] );// + ( 20 * ( $options['type'] == 'Sticker' ) )
	if( !$options['list'] ) {
		$html .= '
<script type="text/javascript">
jQuery(document).ready(function($) {
	$("#GetGlueAPI_interactions' . $options['id'] . '_slider").mobilyslider({
		content: "#GetGlueAPI_sliderContent' . $options['id'] . '", // class for slides container
		children: ".GetGlueAPI_item", // selector for children elements
		transition: "fade", // transition: horizontal, vertical, fade
		animationSpeed: 300, // slide transition speed (miliseconds)
		autoplay: ' . ( $options['autoscroll'] > 0 ? 'true' : 'false' ) . ',
		autoplaySpeed: ' . $options['autoscroll'] . ', // time between transitions (miliseconds)
		pauseOnHover: true, // stop animation while hovering
		bullets: false, // generate pagination (true/false, class: sliderBullets)
		arrows: true, // generate next and previous arrow (true/false, class: sliderArrows)
		arrowsHide: true, // show arrows only on hover
		arrowsClass: "GetGlueAPI_sliderArrows", // class name for previous button
		prev: "GetGlueAPI_sliderPrev", // class name for previous button
		next: "GetGlueAPI_sliderNext", // class name for next button
		animationStart: function(){}, // call the function on start transition
		animationComplete: function(){}, // call the function when transition completed
		id: ' . $options['id'] . '
	});
});
</script>
		';
		$html .= '<div id="GetGlueAPI_interactions' . $options['id'] . '_slider" class="GetGlueAPI_slider" style="height: ' . $height . 'px; width: ' . $options['width'] . 'px"><div id="GetGlueAPI_sliderContent' . $options['id'] . '" class="GetGlueAPI_sliderContent" style="height: ' . $height . 'px; width: ' . $options['width'] . 'px">';
	}
	else {
		$html .= '<div id="GetGlueAPI_interactions' . $options['id'] . '_list" class="GetGlueAPI_list" style="width: ' . $options['width'] . 'px"><div id="GetGlueAPI_listContent' . $options['id'] . '" class="GetGlueAPI_listContent" style="width: ' . $options['width'] . 'px">';
	}
	foreach( $interactions as $interaction ) {
		if(!isset($interaction->objectKey)) $interaction->objectKey=$interaction->sourceObjectKey;
		if( $interaction->objectKey != $last_interaction ) {
			$last_interaction = $interaction->objectKey;
			$i++;
			if( $options['list'] )
				$html .= '<div class="GetGlueAPI_item" style="position: inherit; width: ' . ( $options['width'] - 12 ) . 'px">';
			else
				$html .= '<div class="GetGlueAPI_item" style="position: absolute; height: ' . ( $height - 15 ) . 'px; width: ' . ( $options['width'] - 12 ) . 'px">';
			$html .= display_getglue_interaction( $options, $interaction );
			$html .= '</div>';
			if( $i == $options['limit'] )
				break;
		}
	}
	$html .= '</div></div>';
	return $html . '</div>';
}

function display_getglue_interaction( $options, $data ) {
	$interaction = '';
		$interaction .= '<a href="' . $data->object->link . '" target="_blank"><img width="60px" src="' . $data->object->icon . '" alt="' . $data->object->title . '" title="' . $data->object->title . '"></a>';
	$interaction .= '<div class="GetGlueAPI_title"><a href="http://getglue.com/' . ( 'Sticker' == $data->action ? $data->sourceObjectKey : $data->objectKey ) . '" target="_blank">' . ( $data->displayVerb ? $data->displayVerb : $data->verb ) . ' ' . ( 'Fan' == $data->title ? $data->sourceTitle . ' ' : '' ) . $data->title . '</a></div>';
	if( $data->timestamp ) {
		$time = strtotime( $data->timestamp );
		if( ( abs( time() - $time) ) < 86400 )
			$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
		else
			$h_time = date( __('Y/m/d'), $time);
		$interaction .= sprintf( __('%s', 'GetGlueAPI'), ' <div class="GetGlueAPI_timestamp"><abbr title="' . date(__('Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></div>' );
	}
	if( $options['stats'] ) {
		$interaction .= '<div class="GetGlueAPI_stats"' . ( 212 > $options['width'] ? ' style="margin-left:0;"' : '' ) . '>';
		$statclass = 'statsdigits';
		if( max( $data->object->liked, $data->object->disliked, $data->object->checkedIn ) > 999 )
			$statclass = 'stats4digits';
		if( max( $data->object->liked, $data->object->disliked, $data->object->checkedIn ) > 9999 )
			$statclass = 'stats5digits';
		if( max( $data->object->liked, $data->object->disliked, $data->object->checkedIn ) > 99999 )
			$statclass = 'stats6digits';
		if( max( $data->object->liked, $data->object->disliked, $data->object->checkedIn ) > 999999 )
			$statclass = 'stats7digits';
		$interaction .= '<div class="GetGlueAPI_stat">' . __( 'LIKED', 'GetGlueAPI' ) . '<strong><span class="GetGlueAPI_' . $statclass . '">' . number_format( $data->object->liked, 0, '', ',' ) . '</span></strong></div>';
		$interaction .= '<div class="GetGlueAPI_stat">' . __( 'DISLIKED', 'GetGlueAPI' ) . '<strong><span class="GetGlueAPI_' . $statclass . '">' . number_format( $data->object->disliked, 0, '', ',' ) . '</span></strong></div>';
		$interaction .= '<div class="GetGlueAPI_stat" style="border-right:none;margin-left:4px;">' . __( 'CHECKED IN', 'GetGlueAPI' ) . '<strong><span class="GetGlueAPI_' . $statclass . '">' . number_format( $data->object->checkedIn, 0, '', ',' ) . '</span></strong></div>';
		$interaction .= '</div>';//stats
	}
	return $interaction;
}










/**
* TidyJSON
*
* A simple class for cleaning up poorly formatted JSON strings. No validation
* is performed; if you pass in bogus data, you will get bogus output.
*/

class TidyJSON {
	protected static $default_config = array(
	'indent' => ' ',
	'space' => ' ',
	);

	protected static $string_chars = array('"', "'");
	protected static $esc_string_chars = array("\\\"", "\\'");
	protected static $white_chars = array(" ", "\t", "\n", "\r");

	/**
	* tidy
	* @param string $json JSON-formatted string you'd like to tidy
	* @param array $config Optional configuration values
	*/
	public static function tidy($json, $config = null) {
		$config = self::get_config($config);
		$out = '';
		$level = 0;
		$strchr = null;
		$c = null;
		for ($x = 0; $x < strlen($json); $x++) {
			$lc = $c;
			$c = substr($json, $x, 1);
			if ($strchr === null) {
				if (in_array($c, self::$white_chars))
				continue;
				if (in_array($c, self::$string_chars)) {
					$strchr = $c;
				} else {
					if ($c == '{' || $c == '[') {
						$eol = true;
						$level++;
					} elseif ($c == '}' || $c == ']') {
						$level--;
						$out .= "\n" . self::indent($level, $config);
					} elseif ($c == ',') {
						$eol = true;
					} elseif ($c == ':') {
						$c .= $config['space'];
					}
				}
			} else {
				if ($c === $strchr && !in_array($lc.$c, self::$esc_string_chars)) {
					$strchr = null;
				}
			}
			$out .= $c;
			if ($eol) {
				$eol = false;
				$out .= "\n" . self::indent($level, $config);
			}
		}

		// Remove trailing whitespace
		while (in_array(substr($out, -1), self::$white_chars)) {
			$out = substr($out, 0, -1);
		}

		return $out;
	}

	protected static function indent($level, $config) {
		$out = '';
		for ($x = 0; $x < $level; $x++) $out .= $config['indent'];
		return $out;
	}

	protected static function get_config($config = null) {
		return is_array($config) ? array_merge(self::$default_config, $config) : self::$default_config;
	}
}

$GetGlueAPI = new GetGlueAPI(); 
?>