jQuery(document).ready(function($) {
	var cur_path = window.location.href;
	jQuery.ajax({
	  type : "post",
	  context: this,
	  dataType : "html",
	  url : smpvstatajx.ajaxurl,
	  data : {action: "smpvstat_add",path:cur_path, checkReq:smpvstatajx.checkReq},
	  success: function(response) {		 
		},
		complete : function(){
		}
	});///
});