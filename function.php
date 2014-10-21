<?php
function wpcsp_ajaxprocess(){
	if( $_POST["fucname"] == "file_upload" ){
		$msg = wpcsp_file_upload($_POST) ;
		$upload_list = get_wpcsp_uploadfile_list() ;
		$data = array(
					"message" => $msg, 
					"list" => $upload_list
				) ;		
		echo json_encode($data) ;
	}
	
	if( $_POST["fucname"] == "file_search" ){
		$data = wpcsp_file_search($_POST) ;
		echo $data ;
	}
	
	if( $_POST["fucname"] == "setting_save" ){
		$data = wpcsp_setting_save($_POST) ;
		echo $data ;
	}
	
	if( $_POST["fucname"] == "get_parameters" ){
		$data = wpcsp_get_parameters($_POST) ;
		echo $data ;
	}
	exit() ;
}

function wpcsp_get_parameters($param){
	$postid = $_POST["post_id"] ;
	$filename = trim($_POST["filename"]) ;
	$settings = wpcsp_get_first_class_settings() ;
	
	$options = get_option("wpcsp_settings") ;	
	if($options["classsetting"][$postid][$filename]){
		$settings = wp_parse_args( $options["classsetting"][$postid][$filename], $default_settings );
	}
	
	extract( $settings ) ;
	
	$prints_allowed = ($prints_allowed) ? $prints_allowed : 0 ;
	$print_anywhere = ($print_anywhere) ? 1 : 0 ;
	$allow_capture = ($allow_capture) ? 1 : 0 ;
	$allow_remote = ($allow_remote) ? 1 : 0 ;
	
	$params = 	" bgwidth='" . $bgwidth . "'" . 
				" bgheight='" . $bgheight . "'" .
				" prints_allowed='" . $prints_allowed . "'" .
				" print_anywhere='" . $print_anywhere . "'" .
				" allow_capture='" . $allow_capture . "'" .
				" allow_remote='" . $allow_remote . "'" .
				" background='" . $background . "'" ;
	return $params ;
}

function wpcsp_get_first_class_settings(){
	$settings = array(				
				'bgwidth'		  => '600',
				'bgheight'		  => '600',
				'prints_allowed'  => 0,
				'print_anywhere'  => 0,
				'allow_capture'   => 0,
				'allow_remote'    => 0,
				'background'	  => 'CCCCCC'
			) ;
	return 	$settings ;	
}

function wpcsp_file_upload($param){
	$file_error 	= $param["error"] ;  
	$file_errors = array( 0 => __( "There is no error, the file uploaded with success" ),
                          1 => __( "The uploaded file exceeds the upload_max_filesize directive in php.ini" ),
                          2 => __( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form" ),
                          3 => __( "The uploaded file was only partially uploaded" ),
                          4 => __( "No file was uploaded" ),
                          6 => __( "Missing a temporary folder" ),
                          7 => __( "Upload directory is not writable" ),
                          8 => __( "User not logged in" )
                   );
                   
	if ( $file_error == 0 ){
		$msg = '<div class="updated"><p><strong>'.__('File Uploaded. You must save "File Details" to insert post').'</strong></p></div>';
	}else{
		$msg = '<div class="error"><p><strong>'.__('Error').'!</strong></p><p>'.$file_errors[$file_error].'</p></div>';
	}
    return $msg ;
}

function wpcsp_file_search($param){
	// get selected file details
	if (@!empty($param['search']) && @!empty($param['post_id'])) {
    	
		$postid = $param['post_id'] ;
		$search = trim($param["search"]);
    	
		$files = _get_wpcsp_uploadfile_list() ;

    	$result = false ;
    	foreach ($files as $file)
    		if( $search == trim($file["filename"]) )$result = true ;
    	    	
		if( !$result )return "<hr /><h2>No found file</h2>" ;
				
		$file_options  = wpcsp_get_first_class_settings() ;
	                    
	    $wpcsp_options = get_option( 'wpcsp_settings' );
	    if( $wpcsp_options["classsetting"][$postid][$search] )
	    	$file_options = $wpcsp_options["classsetting"][$postid][$search] ;
	    
		extract( $file_options, EXTR_OVERWRITE );
		
	    $str = "<hr />
	    		<div class='icon32' id='icon-file'><br /></div>
		        <h2>PDF Class Settings</h2>
		        <div>
	    			<table cellpadding='0' cellspacing='0' border='0' >
	  					<tbody id='wpcsp_setting_body'> 
							  <tr> 
							    <td align='left' width='50'>&nbsp;</td>
							    <td align='left' width='40'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Number of prints allowed per session. For no printing set 0.'></td>
							    <td align='left' width='120'>Viewer Width:&nbsp;&nbsp;</td>
							    <td> 
							      <input name='bgwidth' type='text' value='$bgwidth' size='3'>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left' width='50'>&nbsp;</td>
							    <td align='left' width='40'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Number of prints allowed per session. For no printing set 0.'></td>
							    <td align='left'>Viewer Height:&nbsp;&nbsp;</td>
							    <td> 
							      <input name='bgheight' type='text' value='$bgheight' size='3'>
							    </td>
							  </tr>
	  						  <tr> 
							    <td align='left' width='50'>&nbsp;</td>
							    <td align='left' width='40'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Number of prints allowed per session. For no printing set 0.'></td>
							    <td align='left'>Prints Allowed:&nbsp;&nbsp;</td>
							    <td> 
							      <input name='prints_allowed' type='text' value='$prints_allowed' size='3'>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Check this box to disable Printscreen and screen capture when the class image loads.'></td>
							    <td align='left'>Print Anywhere:</td>
							    <td> 
							      <input name='print_anywhere' type='checkbox' value='1' $print_anywhere>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Check this box to disable Printscreen and screen capture when the class image loads.'></td>
							    <td align='left'>Allow Capture:</td>
							    <td> 
							      <input name='allow_capture' type='checkbox' value='1' $allow_capture>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Check this box to prevent viewing by remote or virtual computers when the class image loads.'></td>
							    <td align='left'>Allow Remote:</td>
							    <td> 
							      <input name='allow_remote' type='checkbox' value='1' $allow_remote>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . WPCSP_PLUGIN_URL . "images/help-24-30.png' border='0' alt='Check this box to prevent viewing by remote or virtual computers when the class image loads.'></td>
							    <td align='left'>Background:</td>
							    <td> 
							    	<input name='background' type='text' value='$background' size='10'>							    	
							    </td>
							  </tr>
						</tbody> 
					</table>
			        <p class='submit'>
			            <input type='button' value='Save' class='button-primary' id='setting_save' name='submit' />
			            <input type='button' value='Cancel' class='button-primary' id='cancel' />
			        </p>
        	</div>" ;
		return $str ;
	}
}

function wpcsp_setting_save($param){
	$postid = $param["post_id"] ;
	$name = trim($param["nname"]) ;
	$data = (array)json_decode(stripcslashes($param["set_data"])) ;
	// escape user inputs
    $data = array_map( "esc_attr", $data );
    extract( $data );
    $wpcsp_settings = get_option( 'wpcsp_settings' ); 
	if(!is_array($wpcsp_settings))$wpcsp_settings = array() ; 
	
	$datas = array ('bgwidth'         => "$bgwidth",
			'bgheight'		=> "$bgheight",
			'prints_allowed'	=> "$prints_allowed",
			'print_anywhere'	=> "$print_anywhere",                    
			'allow_capture'	=> "$allow_capture",					
			'allow_remote'	=> "$allow_remote",
			'background'		=> "$background"
             );
             
    
   	$wpcsp_settings["classsetting"][$postid][$name] = $datas ;    
        
    update_option( 'wpcsp_settings', $wpcsp_settings );

    $msg = '<div class="updated fade">
    			<strong>'.__('File Options Are Saved').'</strong><br />
    			<div style="margin-top:5px;"><a href="#" alt="'.$name.'" class="button-secondary sendtoeditor"><strong>Insert file to editor</strong></a></div>
		    </div>';
    return $msg ;
}

function _get_wpcsp_uploadfile_list(){
	$listdata = array() ;
	chmod(WPCSP_UPLOAD_PATH, 0775);
	$file_list = scandir( WPCSP_UPLOAD_PATH );
	
	foreach ($file_list as $file) {
		if( $file == "." || $file == "..")continue ;		
		$file_path = WPCSP_UPLOAD_PATH . $file ;		
		if( filetype($file_path) != "file" )continue ; 
		$ext = end(explode('.', $file));
		if( $ext != "class" )continue ;
		
		$file_path = WPCSP_UPLOAD_PATH . $file ;
		$file_name = $file;
		$file_size = filesize($file_path) ;
		$file_date = filemtime($file_path) ;
		
		if ( round ( $file_size/1024 ,0 )> 1 ) {
            $file_size = round ( $file_size/1024, 0 );
            $file_size = "$file_size KB";
        } else {
            $file_size = "$file_size B";
        }
        
        $file_date = date("n/j/Y g:h A", $file_date);
                
		$listdata[] = array(
							"filename" => $file_name,
							"filesize" => $file_size,
							"filedate" => $file_date
						) ;
	}
	return $listdata ;
}

function get_wpcsp_uploadfile_list(){
	
	$files = _get_wpcsp_uploadfile_list() ;

	foreach ($files as $file) {
		//$link = "<div class='row-actions'>
		//			<span><a href='#' alt='{$file["filename"]}' class='setdetails row-actionslink' title=''>Setting</a></span>&nbsp;|&nbsp;
		//			<span><a href='#' alt='{$file["filename"]}' class='sendtoeditor row-actionslink' title=''>Insert to post</a></span>											
		//		</div>" ;
        // prepare table row
        $table.= "<tr><td></td><td><a href='#' alt='{$file["filename"]}' class='sendtoeditor row-actionslink'>{$file["filename"]}</a></td><td width='50px'>{$file["filesize"]}</td><td width='130px'>{$file["filedate"]}</td></tr>";
	}
	
	if( !$table ){
		 $table.= '<tr><td colspan="3">'.__('No file uploaded yet.').'</td></tr>';
	}
	
	return $table ;
}



?>