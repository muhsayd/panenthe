var confirmAction = function(){			
	
	if(confirm("Are you sure you perform this action?")){				
		return true;					
	}
	else
	{	
		loading_link.remove_loading();				
		return false;
	}
					
}

window.addEvent('domready', function(){
	$$(".serverAction").addEvent("click", confirmAction);
	$$(".serverForm").addEvent("submit", confirmAction);
});			
