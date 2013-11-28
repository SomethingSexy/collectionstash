var ModalView = Backbone.View.extend({
	template : 'user.upload.details',
	render : function() {
		var self = this;
		var data = this.model.toJSON();

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});

var UserUploadView = Backbone.View.extend({
	template : 'user.upload',
	events : {
		'click .thumbnail' : 'viewDetails'
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	viewDetails : function(event) {
		event.preventDefault();
		var modal = new Backbone.BootstrapModal({
			content : new ModalView({
				model : this.model
			}),
			title : 'modal header',
			animate : true
		}).open(function() {

		});
	}
});

$(function() {

	$.when(
	//
	$.get('/templates/useruploads/upload.dust'), $.get('/templates/useruploads/upload.details.dust')).done(function(uploadTemplate, uploadDetailsTemplate) {
		dust.loadSource(dust.compile(uploadTemplate[0], 'user.upload'));
		dust.loadSource(dust.compile(uploadDetailsTemplate[0], 'user.upload.details'));

		userUploads.each(function(model) {
			$('.user-uploads').append(new UserUploadView({
				model : model
			}).render().el);
		});
	});

});
