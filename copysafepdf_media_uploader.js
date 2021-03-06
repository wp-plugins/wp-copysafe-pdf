jQuery(document).ready(function() {
	
	var file_name = null;
	var clicktrigger = false ;
	
    jQuery("#TB_ajaxContent").css({"width" : "650px", "height" : "100%"});
    
    jQuery("#cancel").live("click", function(){ jQuery('#file_details').html(""); });
    
    jQuery('.sendtoeditor').live("click", function() {
    	if( clicktrigger )return ;
    	clicktrigger = true ;
    	jQuery(this).attr("disabled", true) ;
    	var nname = jQuery(this).attr('alt') ;
    	wpcsp_process_setting('sendeditor', 'start') ;
    	ajaxdata = {
				action		: 'wpcsp_ajaxprocess',
				fucname		: 'get_parameters', 
				filename	: nname,
				post_id		: jQuery("#postid").val()
			 };
		jQuery.post(ajaxurl, ajaxdata, function( param ) {
			var file = "[copysafepdf name='"+nname+"'" + param + "]" ;
	        send_to_editor(file);
	        wpcsp_process_setting('sendeditor', 'end') ;
	        clicktrigger = true ;
		});
		return false;
    }) ;
    
    jQuery("#wpcsp_div .ui-tabs-anchor").click(function(){
    	var iid = jQuery(this).attr("id") ;
    	iid = iid.substring(0, iid.length-3);
    	jQuery("#wpcsp_div .ui-tabs-panel").hide() ;
    	jQuery("#"+iid).show() ;
    	jQuery(this).parents(".ui-tabs-nav").children(".ui-state-default").removeClass("ui-state-active") ;
    	jQuery(this).parent().addClass("ui-state-active") ;
    });
    
    //----------------------------------------
    var wpcsp_string_adjust = function(s, n){
    	var s_length = s.length ;
    	if(s_length <= n )return s;
    	var c_n = Math.floor(n/2) ;
    	var e_n = s_length - n + 3 + c_n ;
    	s = s.substr(0, c_n) + "..." + s.substr(e_n);
    	return s ;
    }
    var pluginurl = jQuery("#plugin-url").val() ;
	var plugindir = jQuery("#plugin-dir").val() ;
	var upload_path = jQuery("#upload-path").val() ;
	var max_size = jQuery("#upload-max-size").val() ;
	var prequeue = "" ;
	
	var wpcsp_process_setting = function(frm, status){
		if( status == "start" )jQuery("#wpcsp_ajax_process").show() ;
		if( status == "end" )jQuery("#wpcsp_ajax_process").hide() ;
		if( frm == "load" ){
    		if( status == "start" ){
        		jQuery("#wpcsp_message").html("") ;
        		jQuery('input:button').attr("disabled", true);
        	}
    		if( status == "end" ){    			
				jQuery('#file_select').uploadify('cancel', prequeue);
				prequeue = "" ;
				jQuery("#custom-queue").html("No file chosen") ;
				jQuery('input:button').attr("disabled", false);
        	}
    	}
		
    	if( frm == "search" ){
    		if( status == "start" ){
        		jQuery("#search").attr("disabled", true);
        	}
    		if( status == "end" ){     			
    			jQuery("#search").attr("disabled", false);
        	}
    	}
    	
    	if( frm == "setting" ){
    		if( status == "start" ){        		
        		
        	}
    		if( status == "end" ){   			
    			
        	}
    	}    	
    } 
	
    jQuery('#file_select').uploadify({ 
    	'method'   		 : 'post',
		'swf'       	 : pluginurl+'lib/uploadify/uploadify.swf',
		'uploader'       : pluginurl+'lib/uploadify/uploadify.php',
		'formData'		 : {'upload_path' : upload_path, 'token_timestamp': jQuery("#token_timestamp").val(), 'token': jQuery("#token").val()},
		'height'		 : 20,
		'width'			 : 104,
		'buttonImage'  	 : pluginurl+'lib/uploadify/file_select.png',		
		'multi'          : false,
		'auto'           : false,
		'fileExt'        : '*.class;*.CLASS',
		'queueID'        : 'upload-queue',	
		'removeCompleted': true,
		'removeTimeout'  : 0.1,
		'fileObjName' 	 : 'wpcsp_file',
		'fileSizeLimit'  : max_size + 'KB',
		'onSelect' 	 : function(file) {			
    		jQuery("#custom-queue").html(wpcsp_string_adjust(file.name, 30)) ;
    		if(prequeue)jQuery('#file_select').uploadify('cancel', prequeue);
    		prequeue = file.id;
		},
		'onUploadSuccess' : function(file, data, response) {
			var file_name = file.name ;
			ajaxdata = {
					action			: 'wpcsp_ajaxprocess',
					fucname			: 'file_upload', 
					error			: data
				 };
			jQuery.post(ajaxurl, ajaxdata, function( param ) {
				wpcsp_process_setting("load", "end") ;
				var contents = jQuery.parseJSON(param) ;
				jQuery( "#wpcsp_message" ).html(contents["message"]) ;
				jQuery( "#wpcsp_upload_list" ).html(contents["list"]) ;				
	            jQuery( "#tabs-2-bt" ).trigger("click") ;
				jQuery( "#wpcsp_searchfile" ).val(file_name);
	            jQuery( "#search" ).trigger("click");
			});
        },
		'onUploadError'  : function (file, errorCode, errorMsg, errorString) {
        	if(errorCode == -280)return false;
			jQuery("#custom-queue").html(errorString) ;
			jQuery("#wpcsp_ajax_process").hide() ;
		}
	});
    
    jQuery("#upload").click(function(){ 
    	if( !prequeue )return ;
    	jQuery('#file_select').uploadify('upload'); 
    	wpcsp_process_setting("load", "start") ;
    }) ;
    
	jQuery("#search").click(function(){
		file_name = jQuery("#wpcsp_searchfile").val();
		var postid = jQuery("#postid").val();
		if ( !file_name ) {
			alert ('Type a file name');
			jQuery("#wpcsp_searchfile").focus(); 
		}else {
			ajaxdata = {
					action	: 'wpcsp_ajaxprocess',
					fucname	: 'file_search', 
					search	: file_name, 
					post_id	: postid
				 };
			wpcsp_process_setting("search", "start") ;
			jQuery.post(ajaxurl, ajaxdata, function( param ) {				
				wpcsp_process_setting("search", "end") ;
				jQuery('#file_details').html(param);				
			});
		}
	});
    
	jQuery('.setdetails').live("click", function() {
		jQuery( "#tabs-2-bt" ).trigger("click") ;
        jQuery("#wpcsp_searchfile").val(jQuery(this).attr('alt'));
        jQuery("#search").trigger("click");
        return false;
    });
	
	jQuery("#setting_save").live("click", function(){
		var setData = {} ;
		jQuery("#wpcsp_setting_body input").each(function(){
			var nname = jQuery(this).attr("name") ;
			if(nname == "print_anywhere" || nname == "allow_capture" || nname == "allow_remote" ){
				setData[nname] = (jQuery(this).attr("checked") == "checked" ) ? "checked" : "" ;
			}else{
				setData[nname] = jQuery(this).val() ;
			}
		}) ;
		ajaxdata = {
				action		: 'wpcsp_ajaxprocess',
				fucname		: 'setting_save', 
				post_id		: jQuery("#postid").val(),
				nname		: jQuery("#wpcsp_searchfile").val(),
				set_data	: jQuery.toJSON(setData)								
			 };
		wpcsp_process_setting("setting", "start") ;
		jQuery.post(ajaxurl, ajaxdata, function( param ) {
			jQuery( "#wpcsp_message" ).html(param);
			wpcsp_process_setting("setting", "end") ;
			jQuery('a.sendtoeditor').click();
		});
	});
	
	jQuery("#wpcsp_setting_body img").live("click", function(){
		alert(jQuery(this).attr("alt")) ;
	});
	
});