/**
 * Panenthe VPS Management
 *
 * This is NOT Free Software
 * This software is NOT Open Source.
 * Please see panenthe.com for more information.
 *
 * Use of this software is binding of a license agreement.
 * This license agreeement may be found at panenthe.com
 *
 * Panenthe DOES NOT offer this software with any WARRANTY whatsoever.
 * Panenthe DOES NOT offer this software with any GUARANTEE whatsoever.
 *
 * @copyright Panenthe, Nullivex LLC. All Rights Reserved.
 * @author Nullivex LLC <contact@nullivex.com>
 * @license http://www.panenthe.com
 * @link http://www.panenthe.com
 *
 */

window.addEvent('domready', function(){
	loading_link = new class_loading_link();
})

var class_loading_link = new Class({
	initialize: function(){
		this.loading_img = SITE_URI+'/js/ajax-loader.gif';
		this.preload_img = new Image();
		this.preload_img.src = this.loading_img;
		this.add_listeners();
	},
	add_listeners: function(){
		var loading_func = function(e){
			var event = new Event(e);
			if(!event.target.hasClass('disableLoadingLink')){
				this.show_loading();
			}
		}
		var loading_func_bound = loading_func.bind(this);
		$$('a').addEvent('click',loading_func_bound);
		$$('form').addEvent('submit',loading_func_bound);
	},
	show_loading: function(){

		new Fx.Scroll(document, {
			offset: {
				'x': 0,
				'y': 0
			},
			duration: 'short'
		}).toTop();

		this.loading_overlay.delay(0,this);

	},

	remove_loading: function(){
		$("loading_overlay_div").destroy();
		document.body.setStyles({
			'overflow': 'auto'
		});
	},

	loading_overlay: function (){

			var overlay_div = new Element('div', {
				id: 'loading_overlay_div',
				styles: {
					'opacity' : '0.0',
					'background-color': '#000000',
					'width': '100%',
					'height': '100%',
					'position': 'absolute',
					'top': '0',
					'bottom': '0',
					'z-index': '1000'
				}
			});

			var overlay_loading_table = new Element('table', {
				'width': '100%',
				'height': '100%'
			});

			var overlay_loading_tr = new Element('tr');
			var overlay_loading_td = new Element('td',{
				styles: {
					'text-align': 'center'
				}
			});

			var overlay_loading_img = new Element('img',{
				src: this.loading_img
			});

			var overlay_loading_text = new Element('div',{
				styles: {
					'color': '#ffffff'
				}
			}).set('html','One Moment Please');

			overlay_loading_td.appendChild(overlay_loading_img);
			overlay_loading_td.appendChild(overlay_loading_text);
			overlay_loading_tr.appendChild(overlay_loading_td);
			overlay_loading_table.appendChild(overlay_loading_tr);
			overlay_div.appendChild(overlay_loading_table);


			document.body.setStyles({
				'margin': '0',
				'padding': '0',
				'width': '100%',
				'height': '100%',
				'overflow': 'hidden'
			});

			document.body.appendChild(overlay_div);

			$('loading_overlay_div').set('tween', {duration: 'short'});
			$('loading_overlay_div').tween('opacity','0.7');

	}

});
