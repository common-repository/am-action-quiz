function am_action_quiz_get_answer(obj){
	
		//check if there is an answer is selected
		if (!jQuery(obj.am_action_quiz_answer).is(':checked')) { 
		
		alert(jQuery(obj.error_message).val()); 
		return false;
		
		};		

		var form_data = jQuery(obj).serialize();
		jQuery.post(am_aqz_ajax.ajaxurl,form_data,function(data){
			if(data){
				jQuery(obj).replaceWith(data);
			};
		})
		return false;
}