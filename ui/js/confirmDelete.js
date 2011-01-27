var confirmDelete = function(){			
	
	if(confirm("Are you sure you want to delete this item. It CANNOT be undone.")){				
		return true;					
	}
	else
	{	
		loading_link.remove_loading();				
		return false;
	}
					
}

window.addEvent('domready', function(){
	$$(".actionLink").addEvent("click", confirmDelete);
	$$(".actionForm").addEvent("submit", confirmDelete);
});			
