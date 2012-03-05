/*
 * This is CS specific tree behavior that is a jQuery plugin.
 *
 * I didn't want to hack up the original plug code.
 */
(function($) {
	$.fn.csTree = function(options) {

		var settings = $.extend({
			//add callbacl
			'add' : function() {
			},
			//remove callback
			'remove' : function() {
			}
		}, options);

		return this.each(function() {

			$(this).treeview({
				'hover' : ''
			});
			$(document).on('click', this, function(event) {
				var targetClass = event.target.className;
				//quick cancel out of things we do not support
				if(targetClass !== 'action add' && targetClass !== 'action remove' && targetClass !== 'add cancel' && targetClass !== 'add submit' && targetClass !== 'remove cancel' && targetClass !== 'remove submit') {
					return;
				}
				var $target = $(event.target);
				if(targetClass === 'action add') {

					var $inputWrapper = $('<div></div>').addClass('item').addClass('input');
					var $input = $('<input />').attr('type', 'input').attr('maxlength', '100');
					var $submit = $('<button></button>').text('Submit').addClass('add').addClass('submit');
					var $cancel = $('<button></button>').text('Cancel').addClass('add').addClass('cancel');
					$inputWrapper.append($input);
					$inputWrapper.append($submit);
					$inputWrapper.append($cancel);
					$target.parent('div.actions').after($inputWrapper);
				} else if(targetClass === 'action remove') {
					var $inputWrapper = $('<div></div>').addClass('item').addClass('input');
					var $span = $('<span></span>').addClass('remove-text').text('Are you sure you want to remove?');
					var $submit = $('<button></button>').text('Yes').addClass('remove').addClass('submit');
					var $cancel = $('<button></button>').text('No').addClass('remove').addClass('cancel');
					$inputWrapper.append($span);
					$inputWrapper.append($submit);
					$inputWrapper.append($cancel);
					$target.parent('div.actions').after($inputWrapper);
				} else if(targetClass === 'add cancel') {
					$target.parent('div.input').remove();
				} else if(targetClass === 'add submit') {
					if( typeof settings.add == 'function') {// make sure the callback is a function
						settings.add.call(this, event);
					}
				} else if(targetClass === 'remove cancel') {
					$target.parent('div.input').remove();
				} else if(targetClass === 'remove submit') {
					if( typeof settings.remove == 'function') {// make sure the callback is a function
						settings.remove.call(this, event);
					}
				}
			});
		});
	};
})(jQuery);
