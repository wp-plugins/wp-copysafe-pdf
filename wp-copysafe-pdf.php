<?php
/*
  Plugin Name: CopySafe PDF Protection
  Plugin URI: http://www.artistscope.com/copysafe_pdf_protection_wordpress_plugin.asp
  Description: This Wordpress plugin enables sites using CopySafe PDF to easily add protected PDF for display in all popular web browsers.
  Author: ArtistScope
  Version: 1.0
  Author URI: http://www.artistscope.com/

  Copyright 2014 ArtistScope Pty Limited


  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// ================================================================================ //
//                                                                                  //
//  WARNING : DONT CHANGE ANYTHING BELOW IF YOU DONT KNOW WHAT YOU ARE DOING        //
//                                                                                  //
// ================================================================================ //
# set script max execution time to 5mins
set_time_limit(300);

// ============================================================================================================================
# register WordPress menus
function wpcsp_admin_menus() {
	add_menu_page('CopySafe PDF', 'CopySafe PDF', 'publish_posts', 'wpcsp_list');
	add_submenu_page('wpcsp_list', 'CopySafe PDF List Files', 'List Files', 'publish_posts', 'wpcsp_list', 'wpcsp_admin_page_list');
	add_submenu_page('wpcsp_list', 'CopySafe PDF Settings', 'Settings', 'publish_posts', 'wpcsp_settings', 'wpcsp_admin_page_settings');
}

// ============================================================================================================================
# "List" Page
function wpcsp_admin_page_list() {
	$files = _get_wpcsp_uploadfile_list();

	foreach ($files as $file) {
		$link = "<div class='row-actions'>
					<span><a href='admin.php?page=wpcsp_list&cspfilename={$file["filename"]}&action=cspdel' title=''>Delete</a></span>											
				</div>";
		// prepare table row
		$table.= "<tr><td></td><td>{$file["filename"]} {$link}</td><td>{$file["filesize"]}</td><td>{$file["filedate"]}</td></tr>";
	}

	if (!$table) {
		$table.= '<tr><td colspan="3">' . __('No file uploaded yet.') . '</td></tr>';
	}
	?>
	<div class="wrap">
		<div class="icon32" id="icon-file"><br /></div>
		<?php echo $msg; ?>
		<h2>List PDF Class Files</h2>
		<div id="col-container" style="width:700px;">        
			<div class="col-wrap">
				<h3>Uploaded PDF Class Files</h3>
				<table class="wp-list-table widefat">
					<thead>
						<tr><th width="5px">&nbsp;</th><th>File</th><th>Size</th><th>Date</th></tr>
					</thead>
					<tbody>
						<?php echo $table; ?>
					</tbody>
					<tfoot>
						<tr><th>&nbsp;</th><th>File</th><th>Size</th><th>Date</th></tr>
					</tfoot>
				</table>
			</div>        
		</div>
		<div class="clear"></div>
	</div>
	<?php
}

// ============================================================================================================================
# "Settings" page
function wpcsp_admin_page_settings() {
	$msg = '';
	if (!empty($_POST)) {
		$wpcsw_options = get_option('wpcsp_settings');
		extract($_POST, EXTR_OVERWRITE);

		if (!$upload_path)
			$upload_path = 'wp-content/uploads/copysafe-pdf/';
		$upload_path = str_replace("\\", "/", stripcslashes($upload_path));
		if (substr($upload_path, -1) != "/")
			$upload_path .= "/";

		$wpcsw_options['settings'] = array(
		    'admin_only' => $admin_only,
		    'upload_path' => $upload_path,
		    'mode' => $mode,
		    'max_size' => (int) $max_size,
		    'language' => $language,
		    'background' => $background,
		    'ie' => $ie,
		    'ff' => $ff,
		    'ch' => $ch,
		    'nav' => $nav,
		    'op' => $op,
		    'sa' => $sa
		);

		$upload_path = ABSPATH . $upload_path;
		if (!is_dir($upload_path))
			mkdir($upload_path, 0, true);

		update_option('wpcsp_settings', $wpcsw_options);
		$msg = '<div class="updated"><p><strong>' . __('Settings Saved') . '</strong></p></div>';
	}

	$wpcsp_options = get_option('wpcsp_settings');
	if ($wpcsp_options["settings"])
		extract($wpcsp_options["settings"], EXTR_OVERWRITE);
	$select = '<option value="demo">Demo Mode</option><option value="licensed">Licensed</option><option value="debug">Debugging Mode</option>';
	$select = str_replace('value="' . $mode . '"', 'value="' . $mode . '" selected', $select);

	$lnguageOptions = array(
	    "0c01" => "Arabic",
	    "0004" => "Chinese (simplified)",
	    "0404" => "Chinese (traditional)",
	    "041a" => "Croatian",
	    "0405" => "Czech",
	    "0413" => "Dutch",
	    "" => "English",
	    "0464" => "Filipino",
	    "000c" => "French",
	    "0007" => "German",
	    "0408" => "Greek",
	    "040d" => "Hebrew",
	    "0439" => "Hindi",
	    "000e" => "Hungarian",
	    "0421" => "Indonesian",
	    "0410" => "Italian",
	    "0411" => "Japanese",
	    "0412" => "Korean",
	    "043e" => "Malay",
	    "0415" => "Polish",
	    "0416" => "Portuguese (BR)",
	    "0816" => "Portuguese (PT)",
	    "0419" => "Russian",
	    "0c0a" => "Spanish",
	    "041e" => "Thai",
	    "041f" => "Turkish",
	    "002a" => "Vietnamese"
	);
	foreach ($lnguageOptions as $k => $v) {
		$chk = str_replace("value='$language'", "value='$language' selected", "value='$k'");
		$lnguageOptionStr .= "<option $chk >$v</option>";
	}
	?>
	<style type="text/css">#wpcsp_page_setting img{cursor:pointer;}</style>
	<div class="wrap">
		<div class="icon32" id="icon-settings"><br /></div>
		<?php echo $msg; ?>
		<h2>Default Settings</h2>
		<form action="" method="post">
			<input type="hidden" value="<?php echo $security; ?>" name="wpcsp_wpnonce" id="wpcsp_wpnonce" />
			<table cellpadding='1' cellspacing='0' border='0' id='wpcsp_page_setting'>
				<p><strong>Default settings applied to all protected PDF pages:</strong></p>
				<tbody> 
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow admin only for new uploads.'></td>
						<td align="left">Allow Admin Only:</td>
						<td align="left"><input name="admin_only" type="checkbox" value="checked" <?php echo $admin_only; ?>></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Path to the upload folder for PDF.'>
						<td align="left">Upload Folder:</td>
						<td align="left"><input value="<?php echo $upload_path; ?>" name="upload_path" class="regular-text code" type="text"></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Set the mode to use. Use Licensed if you have licensed images. Otherise set for Demo or Debug mode.'>
						<td align="left">Mode</td>
						<td align="left"><select name="mode">
								<?php echo $select; ?>
							</select>
						</td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Set the maximum allowed file size for PDF uploads.'>
						<td align="left">Max File Size:</td>
						<td align="left"><input value="<?php echo $max_size; ?>" name="max_size" class="regular-text" style="width:70px;text-align:right;" type="text">
							&nbsp;KB</td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Set the language that is used in the viewer toolbar and messages. Default is English.'>
						<td align="left">Language:</td>
						<td align="left"><select name="language">
								<?php echo $lnguageOptionStr; ?> 
							</select></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Set the color for the unused space in the PDF viewer.'></td>
						<td align="left">Page color:</td>
						<td align="left"><input value="<?php echo $background; ?>" name="background" type="text" size="8"></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow visitors using the Internet Explorer web browser to access this page.'></td>
						<td align="left">Allow IE:</td>
						<td align="left"><input name="ie" type="checkbox" value="checked" <?php echo $ie; ?>></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow visitors using the Firefox web browser to access this page.'></td>
						<td align="left">Allow Firefox:</td>
						<td align="left"><input name="ff" type="checkbox" value="checked" <?php echo $ff; ?>></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow visitors using the Chrome web browser to access this page.'></td>
						<td align="left">Allow Chrome:</td>
						<td align="left"><input name="ch" type="checkbox" value="checked" <?php echo $ch; ?>></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow visitors using the Netscape Navigator web browser to access this page.'></td>
						<td align="left">Allow Navigator:&nbsp;&nbsp;</td>
						<td align="left"><input name="nav" type="checkbox" value="checked" <?php echo $nav; ?>></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow visitors using the Opera web browser to access this page.'></td>
						<td align="left">Allow Opera:</td>
						<td align="left"><input name="op" type="checkbox" value="checked" <?php echo $op; ?>></td>
					</tr>
					<tr> 
						<td align='left' width='50'>&nbsp;</td>
						<td align='left' width='30'><img src='<?php echo WPCSP_PLUGIN_URL; ?>images/help-24-30.png' border='0' alt='Allow visitors using the Safari web browser to access this page.'></td>
						<td align="left">Allow Safari:</td>
						<td align="left"><input name="sa" type="checkbox" value="checked" <?php echo $sa; ?>></td>
					</tr>
				</tbody> 
			</table>
			<p class="submit">
				<input type="submit" value="Save Settings" class="button-primary" id="submit" name="submit">
			</p>
		</form>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<script type='text/javascript'> 
		jQuery(document).ready(function() {
			jQuery("#wpcsp_page_setting img").click(function(){
				alert(jQuery(this).attr("alt")) ;
			});
		});
	</script>
	<?php
}

// ============================================================================================================================
# convert shortcode to html output
function wpcsp_shortcode($atts) {
	global $post;
	$postid = $post->ID;
	$filename = $atts["name"];

	if (!file_exists(WPCSP_UPLOAD_PATH . $filename))
		return "<div style='padding:5px 10px;background-color:#fffbcc'><strong>File($filename) don't exist</strong></div>";

	$settings = wpcsp_get_first_class_settings();


	// get plugin options
	$wpcsp_options = get_option('wpcsp_settings');
	if ($wpcsp_options["settings"])
		$settings = wp_parse_args($wpcsp_options["settings"], $settings);

	if ($wpcsp_options["classsetting"][$postid][$filename])
		$settings = wp_parse_args($wpcsp_options["classsetting"][$postid][$filename], $settings);

	$settings = wp_parse_args($atts, $settings);

	extract($settings);

	$msie = ($ie) ? '1' : '0';
	$firefox = ($ff) ? '1' : '0';
	$chrome = ($ch) ? '1' : '0';
	$navigator = ($nav) ? '1' : '0';
	$opera = ($op) ? '1' : '0';
	$safari = ($sa) ? '1' : '0';

	$print_anywhere = ($print_anywhere) ? '1' : '0';
	$allow_capture = ($allow_capture) ? '1' : '0';
	$allow_remote = ($allow_remote) ? '1' : '0';

	$plugin_url = WPCSP_PLUGIN_URL;
	$plugin_path = WPCSP_PLUGIN_PATH;
	$upload_path = WPCSP_UPLOAD_PATH;
	$upload_url = WPCSP_UPLOAD_URL;
	// display output
	$output = <<<html
     <script type="text/javascript">
		var wpcsp_plugin_url = "$plugin_url" ;
		var wpcsp_upload_url = "$upload_url" ;
	 </script>
	 <script type="text/javascript">
	<!-- hide JavaScript from non-JavaScript browsers
		var m_bpDebugging = false;
		var m_szMode = "$mode";
		var m_szClassName = "$name";
		var m_szImageFolder = "$upload_url";		//  path from root with / on both ends
		var m_bpPrintsAllowed = "$prints_allowed";
		var m_bpPrintAnywhere = "$print_anywhere";
		var m_bpAllowCapture = "$allow_capture";
		var m_bpAllowRemote = "$allow_remote";
		var m_bpLanguage = "$language";
		var m_bpBackground = "$background";			// background colour without the #
		var m_bpWidth = "$bgwidth";				// width of PDF display in pixels
		var m_bpHeight = "$bgheight";			// height of PDF display in pixels

		var m_bpChrome = "$chrome";	
		var m_bpFx = "$firefox";			// all firefox browsers from version 5 and later
		var m_bpNav = "$navigator";
		var m_bpOpera = "$opera";
		var m_bpSafari = "$safari";
		var m_bpMSIE = "$msie";

		if (m_szMode == "debug") {
			m_bpDebugging = true;
		}
		// -->
	 </script>
	 <script src="{$plugin_url}wp-copysafe-pdf.js" type="text/javascript"></script>
     <div>
		 <script type="text/javascript">
			<!-- hide JavaScript from non-JavaScript browsers
			if ((m_szMode == "licensed") || (m_szMode == "debug")) {
				insertCopysafePDF("$name");
			}
			else {
				document.writeln("<img src='{$plugin_url}images/demo_placeholder.jpg' border='0' alt='Demo mode'>");
			}
			// -->
		 </script>
     </div>
html;

	return $output;
}

// ============================================================================================================================
# delete short code
function wpcsp_delete_shortcode() {
	// get all posts
	$posts_array = get_posts();
	foreach ($posts_array as $post) {
		// delete short code
		$post->post_content = wpcsp_deactivate_shortcode($post->post_content);
		// update post
		wp_update_post($post);
	}
}

// ============================================================================================================================
# deactivate short code
function wpcsp_deactivate_shortcode($content) {
	// delete short code
	$content = preg_replace('/\[copysafepdf name="[^"]+"\]\[\/copysafepdf\]/s', '', $content);
	return $content;
}

// ============================================================================================================================
# search short code in post content and get post ids
function wpcsp_search_shortcode($file_name) {
	// get all posts
	$posts = get_posts();
	$IDs = false;
	foreach ($posts as $post) {
		$file_name = preg_quote($file_name, '\\');
		preg_match('/\[copysafepdf name="' . $file_name . '"\]\[\/copysafepdf\]/s', $post->post_content, $matches);
		if (is_array($matches) && isset($matches[1])) {
			$IDs[] = $post->ID;
		}
	}
	return $IDs;
}

// ============================================================================================================================
# delete file options
function wpcsp_delete_file_options($file_name) {
	$file_name = trim($file_name);
	$wpcsp_options = get_option('wpcsp_settings');
	foreach ($wpcsp_options["classsetting"] as $k => $arr) {
		if ($wpcsp_options["classsetting"][$k][$file_name]) {
			unset($wpcsp_options["classsetting"][$k][$file_name]);
			if (!count($wpcsp_options["classsetting"][$k]))
				unset($wpcsp_options["classsetting"][$k]);
		}
	}
	update_option('wpcsp_settings', $wpcsp_options);
}

// ============================================================================================================================
# install media buttons
function wpcsp_media_buttons($context) {
	global $post_ID;
	// generate token for links
	$token = wp_create_nonce('wpcsp_token');
	$url = plugin_dir_url(__FILE__) . 'media-upload.php?post_id=' . $post_ID . '&wpcsp_token=' . $token . '&TB_iframe=1';
	$url = admin_url('?wpcsp-popup=file_upload&post_id=' . $post_ID);
	return $context.="<a href='$url' class='thickbox' id='wpcsp_link' title='CopySafe PDF'><img src='" . plugin_dir_url(__FILE__) . "/images/copysafepdfbutton.png'></a>";
}

// ============================================================================================================================
# browser detector js file
function wpcsp_load_js() {
	// load custom JS file
	//wp_enqueue_script( 'wpcsp-browser-detector', plugins_url( '/browser_detection.js', __FILE__), array( 'jquery' ) );
}

// ============================================================================================================================
# admin page scripts
function wpcsp_admin_load_js() {
	// load jquery suggest plugin
	wp_enqueue_script('suggest');
}

// ============================================================================================================================
# admin page styles
function wpcsp_admin_load_styles() {
	// register custom CSS file & load
	wp_register_style('wpcsp-style', plugins_url('/wp-copysafe-pdf.css', __FILE__));
	wp_enqueue_style('wpcsp-style');
}

function wpcsp_is_admin_postpage() {
	$chk = false;
	$ppage = end(explode("/", $_SERVER["SCRIPT_NAME"]));
	if ($ppage == "post-new.php" || $ppage == "post.php")
		return true;
}

function wpcsp_includecss_js() {
	if (!wpcsp_is_admin_postpage())
		return;
	global $wp_popup_upload_lib;
	if ($wp_popup_upload_lib)
		return;
	$wp_popup_upload_lib = true;
	echo "<link rel='stylesheet' href='http://code.jquery.com/ui/1.9.2/themes/redmond/jquery-ui.css' type='text/css' />";
	echo "<link rel='stylesheet' href='" . WPCSP_PLUGIN_URL . "lib/uploadify/uploadify.css' type='text/css' />";
	// wp_enqueue_script( 'jquery.uploadify');
	wp_enqueue_script('jquery');
	wp_enqueue_script('uploadify.min', false, array('jquery'));
	wp_enqueue_script('jquery.json', false, array('jquery'));
}

// ============================================================================================================================
# setup plugin
function wpcsp_setup() {
	//----add codding---- 
	$options = get_option("wpcsp_settings");
	define('WPCSP_PLUGIN_PATH', str_replace("\\", "/", plugin_dir_path(__FILE__))); //use for include files to other files
	define('WPCSP_PLUGIN_URL', plugins_url('/', __FILE__));
	define('WPCSP_UPLOAD_PATH', str_replace("\\", "/", ABSPATH . $options["settings"]["upload_path"])); //use for include files to other files
	define('WPCSP_UPLOAD_URL', site_url($options["settings"]["upload_path"]));

	include(WPCSP_PLUGIN_PATH . "login-status.php");
	include(WPCSP_PLUGIN_PATH . "function.php");
	add_action('admin_head', 'wpcsp_includecss_js');
	add_action('wp_ajax_wpcsp_ajaxprocess', 'wpcsp_ajaxprocess');

	if ($_GET['page'] == 'wpcsp_list' && $_GET['cspfilename'] && $_GET['action'] == 'cspdel') {
		wpcsp_delete_file_options($_GET['cspfilename']);
		if (file_exists(WPCSP_UPLOAD_PATH . $_GET['cspfilename']))
			unlink(WPCSP_UPLOAD_PATH . $_GET['cspfilename']);
		wp_redirect('admin.php?page=wpcsp_list');
	}

	if (isset($_GET['wpcsp-popup']) && $_GET["wpcsp-popup"] == "file_upload") {
		require_once( WPCSP_PLUGIN_PATH . "popup_load.php" );
		exit();
	}
	//=============================	
	// load js file
	add_action('wp_enqueue_scripts', 'wpcsp_load_js');

	// load admin CSS
	add_action('admin_print_styles', 'wpcsp_admin_load_styles');

	// add short code
	add_shortcode('copysafepdf', 'wpcsp_shortcode');

	// if user logged in
	if (is_user_logged_in()) {
		// install admin menu
		add_action('admin_menu', 'wpcsp_admin_menus');

		// check user capability
		if (current_user_can('edit_posts')) {
			// load admin JS
			add_action('admin_print_scripts', 'wpcsp_admin_load_js');
			// load media button
			add_action('media_buttons_context', 'wpcsp_media_buttons');
		}
	}

	// wp_register_script( 'jquery.uploadify', WPCSP_PLUGIN_URL . 'lib/uploadify/jquery.min.js');
	wp_register_script('uploadify.min', WPCSP_PLUGIN_URL . 'lib/uploadify/jquery.uploadify.min.js');
	wp_register_script('jquery.json', WPCSP_PLUGIN_URL . 'lib/jquery.json-2.3.js');
}

// ============================================================================================================================
# runs when plugin activated
function wpcsp_activate() {
	// if this is first activation, setup plugin options
	if (!get_option('wpcsp_settings')) {
		// set plugin folder
		$upload_dir = 'wp-content/uploads/copysafe-pdf/';

		// set default options
		$wpcsp_options['settings'] = array(
		    'admin_only' => "checked",
		    'upload_path' => $upload_dir,
		    'mode' => "demo",
		    'max_size' => 100,
		    'language' => "",
		    'background' => "EEEEEE",
		    'ie' => "checked",
		    'ff' => "checked",
		    'ch' => "checked",
		    'nav' => "checked",
		    'op' => "checked",
		    'sa' => "checked"
		);

		update_option('wpcsp_settings', $wpcsp_options);

		$upload_dir = ABSPATH . $upload_dir;
		if (!is_dir($upload_dir))
			mkdir($upload_dir, 0, true);
		// create upload directory if it is not exist
	}
}

// ============================================================================================================================
# runs when plugin deactivated
function wpcsp_deactivate() {
	// remove text editor short code
	remove_shortcode('copysafepdf');
}

// ============================================================================================================================
# runs when plugin deleted.
function wpcsp_uninstall() {
	// delete all uploaded files
	$default_upload_dir = ABSPATH . 'wp-content/uploads/copysafe-pdf/';
	if (is_dir($default_upload_dir)) {
		$dir = scandir($default_upload_dir);
		foreach ($dir as $file) {
			if ($file != '.' || $file != '..') {
				unlink($default_upload_dir . $file);
			}
		}
		rmdir($default_upload_dir);
	}

	// delete upload directory    
	$options = get_option("wpcsp_settings");

	if ($options["settings"]["upload_path"]) {
		$upload_path = ABSPATH . $options["settings"]["upload_path"];
		if (is_dir($upload_path)) {
			$dir = scandir($upload_path);
			foreach ($dir as $file) {
				if ($file != '.' || $file != '..') {
					unlink($upload_path . '/' . $file);
				}
			}
			// delete upload directory
			rmdir($upload_path);
		}
	}

	// delete plugin options
	delete_option('wpcsp_settings');

	// unregister short code
	remove_shortcode('copysafepdf');

	// delete short code from post content
	wpcsp_delete_shortcode();
}

// ============================================================================================================================
# register plugin hooks
register_activation_hook(__FILE__, 'wpcsp_activate'); // run when activated
register_deactivation_hook(__FILE__, 'wpcsp_deactivate'); // run when deactivated
register_uninstall_hook(__FILE__, 'wpcsp_uninstall'); // run when uninstalled

add_action('init', 'wpcsp_setup');
?>