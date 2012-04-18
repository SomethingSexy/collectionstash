$(function() {
	$('#tree').csTree({
		'add' : function(event) {
			var seriesId = $(event.target).parent('div.input').parent('li').children('span.name').attr('data-id');
			var name = $(event.target).parent('div.input').children('input').val();
			$.ajax({
				url : '/admin/collectibletypes/add.json',
				dataType : 'json',
				data : 'data[Collectibletype][parent_id]=' + seriesId + '&data[Collectibletype][name]=' + name,
				type : 'post',
				beforeSend : function(xhr) {

				},
				error : function(jqXHR, textStatus, errorThrown) {

				},
				success : function(data) {
					if(data.success.isSuccess) {
						//$(event.target).parent('div.input').parent('li').remove();
						//$(event.target).parent('div.input').parent('li').remove();
						//just refreshing the page now becauase it will be easier then trying
						//to dynamically add into the structure. If we want to keep this ajax
						//it might be nice to have teh server generate the HTML
						location.reload(true);
					} else {
						data.errors[0][name];
					}
				}
			});
		},
		remove : function(event) {
			var seriesId = $(event.target).parent('div.input').parent('li').children('span.name').attr('data-id');
			$.ajax({
				url : '/admin/collectibletypes/remove.json',
				dataType : 'json',
				data : 'data[Collectibletype][id]=' + seriesId,
				type : 'post',
				beforeSend : function(xhr) {

				},
				error : function(jqXHR, textStatus, errorThrown) {

				},
				success : function(data) {
					if(data.success.isSuccess) {
						$(event.target).parent('div.input').parent('li').remove();
					} else {

					}
				}
			});
		}
	});
}); 