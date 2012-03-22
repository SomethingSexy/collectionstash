$(function() {
	tags.init();
});
var tags = function() {
	var totalTagsAllowed = 5;
	var tagNumber = 0;
	var tagCount = 0;

	function initStashAdd() {

	}

	function onTagSelect(value, data) {
		//alert('You selected: ' + value + ', ' + data);
		//This callback will add it to the list
	}

	return {
		init : function() {
			//This var is located on the add_variant.ctp
			if( typeof lastTagKey !== "undefined") {
				//If there is at least one added already then we will want to take that one and +1 for the next.
				tagNumber = ++lastTagKey;
				tagCount = ++ lastTagKey
			}
			var options, a;
			jQuery(function() {
				options = {
					serviceUrl : '/tags/getTagList',
					width : 282,
					onSelect : function(value, data) {
						onTagSelect(value, data);
					}
				};
				a = $('#query').autocomplete(options);
			});
			$('#add-query').click(function() {
				$(this).parent('li').children('div.error-message').remove();
				if(tagCount < totalTagsAllowed) {
					var tagValue = $('#query').val();
					if(tagValue !== '') {
						var $li = $('<li></li>').addClass('tag').addClass('remove');
						var $span = $('<span></span>').addClass('tag-name').text($('#query').val());
						var $removeLink = $('<a></a>').addClass('ui-icon').addClass('ui-icon-close').addClass('remove-tag');
						$li.append($span);
						var $hiddenId = $('<input/>').attr('type', 'hidden').attr('name', 'data[CollectiblesTag][' + tagNumber + '][tag]').val($('#query').val());
						var $hiddenAction = $('<input/>').attr('type', 'hidden').addClass('tag').addClass('action').attr('name', 'data[CollectiblesTag][' + tagNumber + '][action]').val('A');
						$li.append($hiddenId);
						$li.append($hiddenAction);
						$li.append($removeLink);
						$('#add-tag-list').append($li);
						$('#query').val('');
						tagNumber++;
						tagCount++;
					}
				} else {
					$('#add-query').after('<div class="error-message">Only ' + totalTagsAllowed + ' tags allowed.</div>');
				}

			});

			$(document).on('click', '.tag-list > li.tag.remove > a.remove-tag', function() {
				if($('#add-tag-list').attr('data-action') === 'edit') {
					if($(this).parent('li.tag.remove').children('input.tag.action').val() === 'A') {
						$(this).parent('li.tag.remove').remove();
					} else {
						$(this).parent('li.tag.remove').children('input.tag.action').val('D');
						$(this).parent('li.tag.remove').css({
							'border' : '1px solid red'
						});
					}
					tagCount--;

				} else {

				}

			});
		}
	};
}();
