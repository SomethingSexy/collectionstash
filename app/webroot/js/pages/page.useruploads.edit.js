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
/**
 * This view will control all of the photos and multishare.  When the user clicks
 * the button we will enable multishare within the photos.
 */
var UserUploadsView = Backbone.View.extend({
	template : 'user.uploads',
	events : {
		'click .btn-multishare' : 'toggleMultiShare',
		'click .selectable' : 'selectable'
	},
	initialize : function() {
		this.multiShare = false;
		this.views = [];
		this.selectedModels = new Backbone.Collection();
		this.listenTo(this.collection, 'ui:selected', function(model) {
			this.selectedModels.add(model);
			this.renderMultiShareTemplate();
		}, this);
		this.listenTo(this.collection, 'ui:unselected', function(model) {
			this.selectedModels.remove(model);
			this.renderMultiShareTemplate();
		}, this);
	},
	render : function() {
		var self = this;
		var data = {

		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.renderMultiShareTemplate();

		this.collection.each(function(model) {
			var view = new UserUploadView({
				model : model
			});

			$('.user-uploads', self.el).append(view.render().el);

			self.views.push(view);
		});

		return this;
	},
	renderMultiShareTemplate : function() {
		var self = this;
		// TODO: Should probably make this its own view.
		// loop through each view and see
		var data = {
			multiShare : this.multiShare,
			models : this.selectedModels.toJSON()
		};
		dust.render('user.uploads.multishare', data, function(error, output) {
			$('.multishare', self.el).html(output);
		});

	},
	toggleMultiShare : function(event) {
		var self = this;
		event.preventDefault();
		this.multiShare = this.multiShare ? false : true;

		this.renderMultiShareTemplate();

		_.each(this.views, function(view) {
			view.multiShare = self.multiShare;
		});

		// this should probably go in the child view and rerender, however
		// this should be faster, especially with users with a lot of photos
		if (!this.multiShare) {
			$('.ui-selected', this.el).removeClass('ui-selected');
			this.selectedModels.reset();
		}
	},
	selectable : function(event) {
		$(event.currentTarget).select();
	}
});

var UserUploadView = Backbone.View.extend({
	template : 'user.upload',
	events : {
		'click .thumbnail' : 'viewDetails',
		'click' : 'selected'
	},
	initialize : function() {
		this.multiShare = false;
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	selected : function(event) {
		if (this.multiShare) {
			event.preventDefault();
			$(this.el).toggleClass('ui-selected');
			if ($(this.el).hasClass('ui-selected')) {
				this.model.trigger('ui:selected', this.model);
			} else {
				this.model.trigger('ui:unselected', this.model);
			}

		}
	},
	viewDetails : function(event) {
		var self = this;
		if (this.multiShare) {
			return;
		}

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
	$.get('/templates/useruploads/upload.dust'), $.get('/templates/useruploads/upload.details.dust'), $.get('/templates/useruploads/upload.edit.dust'), $.get('/templates/useruploads/uploads.dust'), $.get('/templates/useruploads/uploads.multishare.dust')).done(function(uploadTemplate, uploadDetailsTemplate, uploadEditTemplate, uplaodsTemplate, uploadsMultiShareTemplate) {
		dust.loadSource(dust.compile(uploadTemplate[0], 'user.upload'));
		dust.loadSource(dust.compile(uploadDetailsTemplate[0], 'user.upload.details'));
		dust.loadSource(dust.compile(uploadEditTemplate[0], 'user.upload.edit'));
		dust.loadSource(dust.compile(uplaodsTemplate[0], 'user.uploads'));
		dust.loadSource(dust.compile(uploadsMultiShareTemplate[0], 'user.uploads.multishare'));

		$.unblockUI();

		$('#uploads-container').html(new UserUploadsView({
			collection : userUploads
		}).render().el);

		equalHeight($(".thumbnail"));
	});

});
