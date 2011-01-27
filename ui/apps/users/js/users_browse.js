window.addEvent('domready', function(){

	new users_browse();

});

var users_browse = new Class({

	initialize: function(){

		this.set_row_listener();
		this.set_action_listener();

		this.checked = false;

	},

	set_row_listener: function(){

		$$('select.items_per_page').addEvent('change', function(){
			this.change_items_per_page();
		}.bind(this));

		$(document.body).getElements('.browse_action').addEvents({
			'click': function(e){
				this.browse_highlight_row(e.target);
			}.bind(this)
		});

	},

	set_action_listener: function(){

		$("browse_check_all").addEvent('click',function(){
			this.check_all();
		}.bind(this));

	},

	browse_highlight_row: function(e){
		el = e.getParent();
		el = el.getParent();

		el.getElements('td').each(function(element){

			if(e.checked == true){

				if(element.getParent().hasClass('odd')){
					element.tween('background-color','#e2e2e2','#b3e892');
				}
				else
				{
					element.tween('background-color','#f5f5f5','#e3ffd2');
				}

			}
			else
			{
				if(element.getParent().hasClass('odd')){
					element.tween('background-color','#b3e892','#e2e2e2');
				}
				else
				{
					element.tween('background-color','#e3ffd2','#f5f5f5');
				}

			}

		});

	},

	check_all: function(){

		$(document.body).getElements('.browse_action').each(function(el){
			if(this.checked == true){
				el.checked = false;
				this.browse_highlight_row(el);

			}
			else
			{
				el.checked = true;
				this.browse_highlight_row(el);
			}
		}.bind(this));

		if(this.checked == true){
			this.checked = false;
		}
		else
		{
			this.checked = true;
		}

	},

	change_items_per_page: function(){
		$('items_per_page').submit();
	}

});