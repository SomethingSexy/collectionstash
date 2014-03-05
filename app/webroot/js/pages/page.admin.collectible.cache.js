var CacheView = Backbone.View.extend({
	template : 'admin.collectible.cache',
	events : {
		'click .clearOne' : 'clearOne',
		'click .clearAll' : 'clearAll'
	},
	render : function() {
		var self = this;
		dust.render(this.template, {}, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	clearOne : function() {

	},
	clearAll : function(event) {
		var self = this;
		event.preventDefault();
		this.model.set('clearAll', true);

		this.model.save({}, {
			success : function() {

			}
		});

	}
});

var CacheModel = Backbone.Model.extend({
	url : '/collectibles/cache'
});

$(function() {
	$.blockUI({
		message : '<img src="/img/ajax-loader-circle.gif" />',
		showOverlay : false,
		css : {
			top : '100px',
			border : 'none',
			'background-color' : 'transparent',
			'z-index' : 999999
		}
	});

	// Get all of the data here
	$.when($.get('/templates/collectibles/admin.cache.dust'), $.get('/templates/common/alert.dust')).done(function(adminCacheTemplate, alertTemplate) {
		dust.loadSource(dust.compile(adminCacheTemplate[0], 'admin.collectible.cache'));
		dust.loadSource(dust.compile(alertTemplate[0], 'alert'));
		$.unblockUI();

		var cacheModel = new CacheModel();

		var cacheView = new CacheView({
			model : cacheModel
		});

		cacheModel.on('error', function(model, response, options) {

			$('#message-container').html(new AlertView({
				error : true,
				status : response.status,
				responseText : response.responseText
			}).render().el);
		});

		$('#admin-container').html(cacheView.render().el);

	});

});
