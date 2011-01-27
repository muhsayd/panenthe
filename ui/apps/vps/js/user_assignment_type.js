window.addEvent('domready', function(){
	
	var assignment_type = function(value){
	
		if(value != ''){
	
			if(value == 'assign_user'){
				$("assign_user").setStyle("display","");
				$("add_user").setStyle("display","none");
			}
			else
			{
				$("assign_user").setStyle("display","none");
				$("add_user").setStyle("display","");
			}
	
		}
		
	}

	$('user_add_type').addEvent('change', function(event){
		var e = new Event(event);
		assignment_type(e.target.value);
	});
	
	assignment_type($('user_add_type').value);
	
});
