var ModalView = Backbone.View.extend({
	template : 'user.upload.details',
	events : {
		'click .edit-upload' : 'showEditUpload',
		'click .selectable' : 'selectable'
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	showEditUpload : function() {
		this.trigger('edit:upload');
	},
	selectable : function(event) {
		$(event.currentTarget).select();
	}
});

var EditModalView = Backbone.View.extend({
	template : 'user.upload.edit',
	events : {
		"change input" : "fieldChanged",
		'change textarea' : 'fieldChanged',
	},
	initiliaze : function() {

	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		data.errors = this.errors;
		data.inlineErrors = {};
		_.each(this.errors, function(error) {
			if (error.inline) {
				data.inlineErrors[error.name] = error.message;
			}
		});

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.errors = [];

		return this;
	},
	fieldChanged : function(e) {

		var field = $(e.currentTarget);
		var data = {};
		if (field.attr('type') === 'checkbox') {
			if (field.is(':checked')) {
				data[field.attr('name')] = true;
			} else {
				data[field.attr('name')] = false;
			}
		} else {
			data[field.attr('name')] = field.val();
		}

		this.model.set(data, {
			//forceUpdate : true
		});
	}
});

var UserUploadView = Backbone.View.extend({
	template : 'user.upload',
	events : {
		'click .thumbnail' : 'viewDetails'
	},
	initialize : function() {

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
		var self = this;
		if (event)
			event.preventDefault();

		var modalView = new ModalView({
			model : this.model
		});

		var title = this.model.get('title') || null;

		var modal = new Backbone.BootstrapModal({
			content : modalView,
			title : title,
			animate : true
		}).open();

		modalView.on('edit:upload', function() {
			modal.close();
			self.viewEditUpload();
		});

	},
	viewEditUpload : function() {
		var self = this;
		var modalView = new EditModalView({
			model : this.model
		});

		var modal = new Backbone.BootstrapModal({
			content : modalView,
			animate : true
		}).open();

		modalView.on('edit:collectible', function() {
			modal.close();
		});

		modal.on('cancel', function() {
			self.viewDetails();
		}, this);

		modal.on('ok', function() {
			var self = this;
			modal.preventClose();
			$('.ok', modal.el).button('loading');
			this.model.save({}, {
				error : function(model, xhr, options) {
					modalView.errors = xhr.responseJSON.errors;
					modalView.render();
					$('.ok', modal.el).button('reset');
				},
				success : function() {
					modal.close();
					$.blockUI({
						message : '<button class="close" data-dismiss="alert" type="button">×</button> Photo has been successfully updated.',
						showOverlay : false,
						css : {
							top : '100px',
							'background-color' : '#DDFADE',
							border : '1px solid #93C49F',
							'box-shadow' : '3px 3px 5px rgba(0, 0, 0, 0.5)',
							'border-radius' : '4px 4px 4px 4px',
							color : '#333333',
							'margin-bottom' : '20px',
							padding : '8px 35px 8px 14px',
							'text-shadow' : '0 1px 0 rgba(255, 255, 255, 0.5)',
							'z-index' : 999999
						},
						timeout : 2000
					});
				}
			});

		}, this);
	}
});

function equalHeight(group) {
	tallest = 0;
	group.each(function() {
		thisHeight = $(this).height();
		if (thisHeight > tallest) {
			tallest = thisHeight;
		}
	});
	group.each(function() {
		$(this).height(tallest);
	});
}

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
	$.when(
	//
	$.get('/templates/useruploads/upload.dust'), $.get('/templates/useruploads/upload.details.dust'), $.get('/templates/useruploads/upload.edit.dust')).done(function(uploadTemplate, uploadDetailsTemplate, uploadEditTemplate) {
		dust.loadSource(dust.compile(uploadTemplate[0], 'user.upload'));
		dust.loadSource(dust.compile(uploadDetailsTemplate[0], 'user.upload.details'));
		dust.loadSource(dust.compile(uploadEditTemplate[0], 'user.upload.edit'));
		$.unblockUI();
		userUploads.each(function(model) {
			$('.user-uploads').append(new UserUploadView({
				model : model
			}).render().el);
		});
		equalHeight($(".thumbnail"));
	});

});
