/**
 * TODO: Known Issues:
 * - If you add a brand to a manufacturer, then go back to that list and find a brand, it won't
 *  exist in there
 */
var printId = '10';
var pageEvents = _.extend({}, Backbone.Events);

var ErrorModel = Backbone.Model.extend({});
var Errors = Backbone.Collection.extend({
	model : ErrorModel
})

var CollectibleModel = Backbone.Model.extend({
	urlRoot : function() {
		return '/collectibles/collectible/' + adminMode + '/';
	},
	validation : {
		msrp : [{
			pattern : 'number',
			msg : 'Must be a valid amount.'
		}, {
			required : false
		}],
		description : [{
			rangeLength : [0, 1000],
			msg : 'Invalid length.'
		}, {
			required : false
		}],
		'edition_size' : [{
			pattern : 'digits',
			msg : 'Must be numeric.'
		}, {
			required : false
		}],
		upc : [{
			required : false
		}, {
			pattern : 'digits',
			msg : 'Must be numeric.'
		}, {
			maxLength : 12,
			msg : 'Must be a valid length.'
		}],
		'product_length' : [{
			pattern : 'number',
			msg : 'Must be a valid length.'
		}, {
			required : false
		}],
		'product_width' : [{
			pattern : 'number',
			msg : 'Must be a valid width.'
		}, {
			required : false
		}],
		'product_depth' : [{
			pattern : 'number',
			msg : 'Must be a valid depth.'
		}, {
			required : false
		}],
		url : [{
			pattern : 'url',
			msg : 'Must be a valid url.'
		}, {
			required : false
		}],
		pieces : [{
			pattern : 'digits',
			msg : 'Must be numeric.'
		}, {
			required : false
		}]
	}
});

var Collectibles = Backbone.Collection.extend({
	model : CollectibleModel
});
var Brand = Backbone.Model.extend({});
var Brands = Backbone.Collection.extend({
	model : Brand,
	comparator : function(brand) {
		return brand.get("License").name.toLowerCase();
	}
});
var CollectibleTagModel = Backbone.Model.extend({});
var CollectibleTypeModel = Backbone.Model.extend({});
var CollectibleUploadModel = Backbone.Model.extend({});
var CollectibleUploads = Backbone.Collection.extend({
	model : CollectibleUploadModel,
	initialize : function(models, options) {
		this.id = options.id;
	},
	url : function() {
		return '/collectibles_uploads/uploads/' + this.id;
	},
	parse : function(resp, xhr) {
		var retVal = [];
		_.each(resp, function(upload) {
			var parsedUpload = upload.CollectiblesUpload;
			parsedUpload['Upload'] = upload.Upload;
			retVal.push(parsedUpload);
		});
		return retVal;
	}
});
// These two are used for the popup to add photos to an attribute
var AttributeUploadModel = Backbone.Model.extend({});
var AttributeUploads = Backbone.Collection.extend({
	model : AttributeUploadModel,
	initialize : function(models, options) {
		this.id = options.id;
	},
	url : function() {
		return '/attributes_uploads/view/' + this.id;
	},
	parse : function(resp, xhr) {
		return resp.response.data.files;
	}
});
var ManufacturerModel = Backbone.Model.extend({
	urlRoot : '/manufactures/manufacturer',
	validation : {
		title : [{
			pattern : /^[A-Za-z0-9 _]*$/,
			msg : 'Invalid characters'
		}, {
			required : true
		}],
		url : [{
			pattern : 'url',
			msg : 'Must be a valid url.'
		}, {
			required : false
		}]
	}
});
var CurrencyModel = Backbone.Model.extend({});
var Currencies = Backbone.Collection.extend({
	model : CurrencyModel
});
var Scale = Backbone.Model.extend({});
var Scales = Backbone.Collection.extend({
	model : Scale
});
var SeriesModel = Backbone.Model.extend({
	url : function() {
		var mode = "";
		if (this.get('mode')) {
			mode = "/" + this.get('mode');
		}

		return '/series/get/' + this.id + mode;
	}
});
var ManufacturerList = Backbone.Collection.extend({
	model : ManufacturerModel,
	comparator : function(man) {
		return man.get("title").toLowerCase();
	}
});

var AttributeModel = Backbone.Model.extend({
	urlRoot : '/attributes_collectibles/attribute',
	parse : function(resp, xhr) {
		var retVal = {};
		retVal = resp.AttributesCollectible;
		retVal.Attribute = resp.Attribute;
		retVal.Revision = resp.Revision;

		return retVal;
	}
});

var Attributes = Backbone.Collection.extend({
	model : AttributeModel
});

var TagModel = Backbone.Model.extend({
	urlRoot : function() {
		return '/collectibles/tag/' + adminMode + '/'
	}
});

var Tags = Backbone.Collection.extend({
	model : TagModel,
	urlRoot : '/collectibles/tags'
});

var ArtistModel = Backbone.Model.extend({
	urlRoot : function() {
		return '/collectibles/artist/' + adminMode + '/'
	}
});

var Artists = Backbone.Collection.extend({
	model : ArtistModel,
	urlRoot : '/collectibles/artists'
});

var AttributesView = Backbone.View.extend({
	template : 'attributes.default.edit',
	className : "span12",
	initialize : function(options) {
		var self = this;
		this.status = options.status;
		this.collection.on('remove', this.renderList, this);
	},
	render : function() {
		var self = this;
		var data = {
			'collectibleId' : collectibleId,
		};
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		// This should only be created once
		this.addCollectiblesAttributes = new AddCollectibleAttributes({
			'adminPage' : adminMode,
			$element : $('.attributes.collectible', self.el),
			$context : self.el,
			success : function(data) {
				if (data.isEdit === false) {
					var attribute = new AttributeModel(data);

					self.collection.add(attribute);
					$('tbody', self.el).append(new AttributeView({
						model : attribute,
						status : self.status
					}).render().el);
				}
			}
		});

		// Init here after we render
		this.addCollectiblesAttributes.init();

		this.addExistingCollectiblesAttributes = new AddExistingCollectibleAttributes({
			'adminPage' : adminMode,
			$element : $('.attributes.collectible', self.el),
			$context : self.el,
			success : function(data) {
				if (data.isEdit === false) {
					var attribute = new AttributeModel(data);

					self.collection.add(attribute);
					$('tbody', self.el).append(new AttributeView({
						model : attribute,
						status : self.status
					}).render().el);
				}
			}
		});

		this.addExistingCollectiblesAttributes.init();

		// Since this handles it for all, we need to handle
		// it here
		this.updateAttributes = new UpdateAttributes({
			'adminPage' : adminMode,
			$element : $('.attributes.collectible', self.el),
			$context : self.el,
			success : function(data) {
				if (data.isEdit === false) {
					// This will return the updated attribute data...we need
					// to find the model and then update it
					self.collection.each(function(attribute) {
						if (attribute.toJSON().Attribute.id === data.Attribute.id) {
							attribute.set({
								Attribute : data.Attribute
							});
						}
					});
				} else {
					// do nothing
				}
			}
		});

		this.updateCollectibleAttributes = new UpdateCollectibleAttributes({
			'adminPage' : adminMode,
			$element : $('.attributes.collectible', self.el),
			$context : self.el,
			success : function(data) {
				if (data.isEdit === false) {
					// This will return the updated attribute data...we need
					// to find the model and then update it
					self.collection.each(function(attribute) {
						if (attribute.toJSON().id === data.id) {
							attribute.set({
								count : data.count
							});
						}
					});
				} else {
					// do nothing
				}
			}
		});

		this.removeAttributes = new RemoveAttributes({
			'adminPage' : adminMode,
			$element : $('.attributes.collectible', self.el),
			$context : self.el,
			success : function(data) {
				if (data.isEdit === false) {
					// This will contain the id of the Attribute we
					// removed.  We will use that to find
					self.collection.each(function(attribute) {
						if (attribute.toJSON().Attribute.id === data.id) {
							self.collection.remove(attribute);
						}
					});
				} else {
					// do nothing
				}
			}
		});

		this.removeCollectibleAttributes = new RemoveAttributeLinks({
			'adminPage' : adminMode,
			$element : $('.attributes.collectible', self.el),
			$context : self.el,
			success : function(data) {
				if (data.isEdit === false) {
					// This will contain the id of the Attribute we
					// removed.  We will use that to find
					self.collection.each(function(attribute) {
						if (attribute.toJSON().id === data.id) {
							self.collection.remove(attribute);
						}
					});
				} else {
					// do nothing
				}
			}
		});

		this.collection.each(function(attribute) {
			$('tbody', self.el).append(new AttributeView({
				model : attribute,
				status : self.status
			}).render().el);
		});

		this.updateAttributes.init();

		this.updateCollectibleAttributes.init();

		this.removeAttributes.init();

		this.removeCollectibleAttributes.init();

		return this;
	},
	renderList : function() {
		var self = this;
		$('tbody', self.el).empty();
		this.collection.each(function(attribute) {
			$('tbody', self.el).append(new AttributeView({
				model : attribute,
				status : self.status
			}).render().el);
		});
	}
});

var AttributeView = Backbone.View.extend({
	template : 'attribute.default.edit',
	tagName : "tr",
	events : {
		'click .edit-attribute-photo-link' : 'addPhoto'
	},
	initialize : function(options) {
		this.status = options.status;
		this.model.on('change', this.render, this);
	},
	render : function() {
		var self = this;
		var attributeModel = this.model.toJSON();
		var status = this.status.toJSON();
		// If the status is draft or submitted, don't allow
		// remove the of the part...once the admin piece
		// comes in update to allow if admin

		// Remove Collectible Attribute will remove the attribute
		// if it is the only one, so we don't need remove really
		if (status.status.id === '1' || status.status.id === '2' && !adminMode) {
			attributeModel.allowRemoveAttribute = false;
		} else {
			attributeModel.allowRemoveAttribute = true;
		}

		// we need to build out some stuff for editing and removing

		var attribute = {};
		attribute.categoryId = attributeModel.Attribute.AttributeCategory.id;
		attribute.categoryName = attributeModel.Attribute.AttributeCategory.path_name;
		attribute.name = attributeModel.Attribute.name;
		attribute.description = attributeModel.Attribute.description;
		if (attributeModel.Attribute.scale_id) {
			attribute.scaleId = attributeModel.Attribute['scale_id'];
		} else {
			attribute.scaleId = null;
		}
		if (attributeModel.Attribute['manufacture_id']) {
			attribute.manufacturerId = attributeModel.Attribute['manufacture_id'];
		} else {
			attribute.manufacturerId = null;
		}

		if (attributeModel.Attribute['artist_id']) {
			attribute.artistId = attributeModel.Attribute['artist_id'];
		} else {
			attribute.artistId = null;
		}

		attribute.id = attributeModel.Attribute.id;

		var attributeCollectible = {};
		attributeCollectible.id = attributeModel.id;
		attributeCollectible.attributeId = attributeModel['attribute_id'];
		attributeCollectible.categoryName = attributeModel.Attribute.AttributeCategory['path_name'];
		attributeCollectible.count = attributeModel.count;

		attributeModel.uploadDirectory = uploadDirectory;
		dust.render(this.template, attributeModel, function(error, output) {
			$(self.el).html(output);
		});
		$(self.el).attr('data-id', this.model.toJSON().Attribute.id).attr('data-attribute-collectible-id', this.model.toJSON().id).attr('data-attached', true);
		$('span.popup', self.el).popover({
			placement : 'bottom',
			html : 'true',
			trigger : 'click'
		});
		$(self.el).attr('data-attribute', JSON.stringify(attribute));
		$(self.el).attr('data-attribute-collectible', JSON.stringify(attributeCollectible));

		return this;
	},
	addPhoto : function() {
		var self = this;
		var attribute = self.model.toJSON();
		// Hmmm, well, it might make sense at some point
		// to merge the upload stuff, directly into the attribute
		// model data but the plugin requires it's data in a special
		//format, so for now we are going to fetch each time we
		// need it, oh well.

		$.blockUI({
			message : 'Loading...',
			css : {
				border : 'none',
				padding : '15px',
				backgroundColor : ' #F1F1F1',
				'-webkit-border-radius' : '10px',
				'-moz-border-radius' : '10px',
				color : '#222',
				background : 'none repeat scroll 0 0 #F1F1F',
				'border-radius' : '5px 5px 5px 5px',
				'box-shadow' : '0 0 10px rgba(0, 0, 0, 0.5)'
			}
		});

		var uploads = new AttributeUploads([], {
			'id' : attribute.Attribute.id
		});

		uploads.fetch({
			success : function() {

				if (self.photoEditView) {
					self.photoEditView.remove();
				}
				self.photoEditView = new AttributePhotoView({
					collection : uploads,
					model : self.model
				});

				$.unblockUI();
				$('body').append(self.photoEditView.render().el);
				$('#attribute-upload-dialog', 'body').modal({
					backdrop : 'static'
				});

				$('#attribute-upload-dialog', 'body').on('hidden', function() {
					self.photoEditView.remove();
					self.model.fetch();
				});
			}
		});
	}
});
var AttributePhotoView = Backbone.View.extend({
	template : 'attribute.photo.edit',
	className : "span4",
	events : {

	},
	initialize : function(options) {
		this.eventManager = options.eventManager;
		// this.collection.on('reset', function() {
		// var self = this;
		// var data = {
		// uploads : this.collection.toJSON(),
		// uploadDirectory : uploadDirectory
		// };
		// dust.render(this.template, data, function(error, output) {
		// $(self.el).html(output);
		// });
		// }, this);
	},
	render : function() {
		var self = this;
		var data = {
			uploadDirectory : uploadDirectory,
			attribute : this.model.toJSON()
		};
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		$('.fileupload', self.el).fileupload({
			//dropZone : $('#dropzone')
		});
		$('.fileupload', self.el).fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));

		$('.fileupload', self.el).fileupload('option', {
			url : '/attributes_uploads/upload',
			maxFileSize : 2097152,
			acceptFileTypes : /(\.|\/)(gif|jpe?g|png)$/i,
			process : [{
				action : 'load',
				fileTypes : /^image\/(gif|jpeg|png)$/,
				maxFileSize : 2097152 // 2MB
			}, {
				action : 'resize',
				maxWidth : 1440,
				maxHeight : 900
			}, {
				action : 'save'
			}]
		});

		$('.fileupload', self.el).bind('fileuploaddestroy', function(e, data) {
			var filename = data.url.substring(data.url.indexOf("=") + 1);
			console.log(data);
		});

		$('.fileupload', self.el).on('hidden', function() {
			$('#fileupload table tbody tr.template-download').remove();
			pageEvents.trigger('upload:close');
		});

		$('.upload-url', self.el).on('click', function() {
			var url = $.trim($('.url-upload-input', self.el).val());
			if (url !== '') {
				$.ajax({
					dataType : 'json',
					type : 'post',
					data : $('.fileupload', self.el).serialize(),
					url : '/attributes_uploads/upload/',
					beforeSend : function(formData, jqForm, options) {
						$('.fileupload-progress', self.el).removeClass('fade').addClass('active');
						$('.fileupload-progress .progress .bar', self.el).css('width', '100%');
					},
					success : function(data, textStatus, jqXHR) {
						if (data && data.files.length) {
							var that = $('.fileupload', self.el);
							that.fileupload('option', 'done').call(that, null, {
								result : data
							});
						} else if (data.response && !data.response.isSuccess) {
							// most like an error
							$('span', '.component-message.error').text(data.response.errors[0].message);

						}
					},
					complete : function() {
						$('.fileupload-progress', self.el).removeClass('active').addClass('fade');
						$('.fileupload-progress .progress .bar', self.el).css('width', '0%');
					}
				});
			}

		});

		var that = $('.fileupload', self.el);
		that.fileupload('option', 'done').call(that, null, {
			result : {
				files : self.collection.toJSON()
			}
		});

		return this;
	}
});
var PhotoView = Backbone.View.extend({
	template : 'photo.default.edit',
	className : "span4",
	events : {

	},
	initialize : function(options) {
		this.eventManager = options.eventManager;
		this.collection.on('reset', function() {
			var self = this;
			var data = {
				uploads : this.collection.toJSON(),
				uploadDirectory : uploadDirectory
			};
			dust.render(this.template, data, function(error, output) {
				$(self.el).html(output);
			});
		}, this);
	},
	render : function() {
		var self = this;
		var data = {
			uploads : this.collection.toJSON(),
			uploadDirectory : uploadDirectory
		};
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		$('#fileupload').fileupload({
			//dropZone : $('#dropzone')
		});
		$('#fileupload').fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));

		$('#fileupload').fileupload('option', {
			url : '/collectibles_uploads/upload',
			maxFileSize : 2097152,
			acceptFileTypes : /(\.|\/)(gif|jpe?g|png)$/i,
			process : [{
				action : 'load',
				fileTypes : /^image\/(gif|jpeg|png)$/,
				maxFileSize : 2097152 // 2MB
			}, {
				action : 'resize',
				maxWidth : 1440,
				maxHeight : 900
			}, {
				action : 'save'
			}]
		});

		$('#upload-dialog').on('hidden', function() {
			$('#fileupload table tbody tr.template-download').remove();
			pageEvents.trigger('upload:close');
		});

		$('#upload-url').on('click', function() {
			var url = $.trim($('.url-upload-input').val());
			if (url !== '') {
				$.ajax({
					dataType : 'json',
					type : 'post',
					data : $('#fileupload').serialize(),
					url : '/collectibles_uploads/upload/',
					beforeSend : function(formData, jqForm, options) {
						$('.fileupload-progress').removeClass('fade').addClass('active');
						$('.fileupload-progress .progress .bar').css('width', '100%');
					},
					success : function(data, textStatus, jqXHR) {
						if (data && data.files.length) {
							var that = $('#fileupload');
							that.fileupload('option', 'done').call(that, null, {
								result : data
							});
						} else if (data.response && !data.response.isSuccess) {
							// most like an error
							$('span', '.component-message.error').text(data.response.errors[0].message);

						}
					},
					complete : function() {
						$('.fileupload-progress').removeClass('active').addClass('fade');
						$('.fileupload-progress .progress .bar').css('width', '0%');
					}
				});
			}

		});

		$(self.el).on('click', '#upload-link', function() {
			$.blockUI({
				message : 'Loading...',
				css : {
					border : 'none',
					padding : '15px',
					backgroundColor : ' #F1F1F1',
					'-webkit-border-radius' : '10px',
					'-moz-border-radius' : '10px',
					color : '#222',
					background : 'none repeat scroll 0 0 #F1F1F',
					'border-radius' : '5px 5px 5px 5px',
					'box-shadow' : '0 0 10px rgba(0, 0, 0, 0.5)'
				}
			});

			$.ajax({
				dataType : 'json',
				url : '/collectibles_uploads/view/' + collectibleId,
				beforeSend : function(formData, jqForm, options) {

				},
				success : function(data, textStatus, jqXHR) {

					if (data && data.response.data.files.length) {
						var that = $('#fileupload');
						that.fileupload('option', 'done').call(that, null, {
							result : data.response.data
						});
					}

					$.unblockUI();
					$('.url-upload-input', '#upload-dialog').val('');
					$('span', '.component-message.error').text('');
					$('#upload-dialog').modal();

				}
			});
		});

		return this;
	}
});

var SeriesView = Backbone.View.extend({
	events : {
		'click span.item' : 'selectSeries'
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;
		$(self.el).html(this.model.toJSON().response.data);
		return this;
	},
	selectSeries : function(event) {
		var name = $(event.currentTarget).attr('data-path');
		var id = $(event.currentTarget).attr('data-id');
		pageEvents.trigger('series:select', id, name);
	}
});

var ManufacturerSeriesView = Backbone.View.extend({
	template : 'manufacturer.series.add',
	modal : 'modal',
	events : {
		'click .add-series' : 'showAdd',
		'click .add.submit' : 'addSeries'
	},
	initialize : function(options) {
		var self = this;
		Backbone.Validation.bind(this, {
			valid : function(view, attr, selector) {
				view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
				view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
				view.$('[' + selector + '~="' + attr + '"]').closest('.control-group').removeClass('error');
				// do something
			},
			invalid : function(view, attr, error, selector) {
				view.$('[' + selector + '~="' + attr + '"]').addClass('invalid').attr('data-error', error);
				view.$('[' + selector + '~="' + attr + '"]').closest('.control-group').addClass('error');
				view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
				view.$('[' + selector + '~="' + attr + '"]').after('<span class="help-block _error">' + error + '</span>');
				// do something
			}
		});
		this.manufacturer = options.manufacturer;
	},
	remove : function() {
		//this.model.off('change');
		Backbone.View.prototype.remove.call(this);
	},
	renderBody : function() {
		var self = this;
		var data = {
			manufacturer : this.manufacturer.toJSON()
		};

		dust.render(this.template, data, function(error, output) {
			$('.modal-body', self.el).html(output);
		});

		$('.modal-body', self.el).append(this.model.toJSON().response.data);

	},
	render : function() {
		var self = this;

		dust.render(this.modal, {
			modalId : 'manufacturerSeriesModal',
			modalTitle : 'Manufacturer Categories'
		}, function(error, output) {
			$(self.el).html(output);
		});

		$(self.el).find('.btn-primary.save').remove();

		this.renderBody();

		return this;
	},
	showAdd : function(event) {
		this.hideMessage();
		var $target = $(event.currentTarget);
		var $inputWrapper = $('<div></div>').addClass('item').addClass('input');
		var $input = $('<input />').attr('type', 'input').attr('maxlength', '100');
		var $submit = $('<button></button>').text('Submit').addClass('add').addClass('submit');
		var $cancel = $('<button></button>').text('Cancel').addClass('add').addClass('cancel');
		$inputWrapper.append($input);
		$inputWrapper.append($submit);
		$inputWrapper.append($cancel);
		$target.parent('span.actions').after($inputWrapper);
	},
	closeAdd : function(event) {
		var $target = $(event.currentTarget);
		$target.parent('div.input').remove();
	},
	addSeries : function(event) {
		var self = this;
		var seriesId = $(event.currentTarget).parent('div.input').parent('li').children('span.name').attr('data-id');
		var name = $(event.currentTarget).parent('div.input').children('input').val();
		$.ajax({
			url : '/series/add.json',
			dataType : 'json',
			data : 'data[Series][parent_id]=' + seriesId + '&data[Series][name]=' + name,
			type : 'post',
			beforeSend : function(xhr) {

			},
			error : function(jqXHR, textStatus, errorThrown) {
				var $messageContainer = $('.message-container', self.el);
				$('h4', $messageContainer).text('');
				$('ul', $messageContainer).empty();
				if (jqXHR.status === 401) {
					$('h4', $messageContainer).text('You must be logged in to do that!');
				} else if (jqXHR.status === 400) {
					var response = JSON.parse(jqXHR.responseText);
					$('h4', $messageContainer).text('Oops! Something wasn\'t filled out correctly.');

					if (response && response.response && response.response.errors) {
						_.each(response.response.errors, function(error) {
							_.each(error.message, function(message) {
								$('ul', $messageContainer).append($('<li></li>').text(message));
							});
						});
					}
				} else {
					$('h4', $messageContainer).text('Something really bad happened.');
				}

				$messageContainer.show();

			},
			success : function(data) {
				self.hideMessage();
				if (data.response.isSuccess) {
					//TODO: Once this part is more backboney then we can just add
					// render
					// let's try and add it to the current list
					var $parentLi = $(event.currentTarget).parent('div.input').parent('li');
					var $ul = $('ul', $parentLi);
					if ($ul.length === 0) {
						$parentLi.append($('<ul></ul>'));
						$ul = $('ul', $parentLi);
					}

					var $series = $('<li></li>');
					$series.append('<span class="item name" data-id=" ' + data.response.data.id + '" data-path="' + data.response.data.name + '">' + data.response.data.name + '</span>');
					$series.append('<span class="item actions"> <a class="action add-series"> Add</a></span>');
					$ul.append($series);

					self.closeAdd(event);
					// first check to see if
				} else {
					//data.errors[0][name];
				}
			}
		});
	},
	hideMessage : function() {
		$('.message-container', this.el).hide();
	}
});

var ManufacturerView = Backbone.View.extend({
	template : 'manufacturer.add',
	modal : 'modal',
	events : {
		"change #inputManName" : "fieldChanged",
		"change #inputManUrl" : "fieldChanged",
		'change textarea' : 'fieldChanged',
		'click .save' : 'saveManufacturer',
		'click .manufacturer-brand-add' : 'addBrand'

	},
	initialize : function(options) {
		var self = this;

		this.mode = options.mode;
		if (options.mode === 'edit') {
			this.template = 'manufacturer.edit';
		} else if (options.mode === 'add') {
			this.template = 'manufacturer.add';
		}

		if (this.mode === 'add') {
			Backbone.Validation.bind(this, {
				valid : function(view, attr, selector) {
					view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
					view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
					view.$('[' + selector + '~="' + attr + '"]').closest('.control-group').removeClass('error');
					// do something
				},
				invalid : function(view, attr, error, selector) {
					view.$('[' + selector + '~="' + attr + '"]').addClass('invalid').attr('data-error', error);
					view.$('[' + selector + '~="' + attr + '"]').closest('.control-group').addClass('error');
					view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
					view.$('[' + selector + '~="' + attr + '"]').after('<span class="help-block _error">' + error + '</span>');
					// do something
				}
			});
		}

		this.brands = options.brands;
		this.brandArray = [];
		options.brands.each(function(brand) {
			self.brandArray.push(brand.get('License').name);
		});
		this.model.on('change:LicensesManufacture', this.renderBody, this);

	},
	remove : function() {
		this.model.off('change');
		Backbone.View.prototype.remove.call(this);
	},
	renderBody : function() {
		var self = this;
		var data = {
			manufacturer : this.model.toJSON()
		};

		dust.render(this.template, data, function(error, output) {
			$('.modal-body', self.el).html(output);
		});

		$('#inputManBrand', self.el).typeahead({
			source : this.brandArray,
			items : 100
		});
	},
	render : function() {
		var self = this;

		dust.render(this.modal, {
			modalId : 'manufacturerModal',
			modalTitle : 'Manufacturer'
		}, function(error, output) {
			$(self.el).html(output);
		});

		this.renderBody();

		return this;
	},
	selectionChanged : function(e) {
		var field = $(e.currentTarget);

		var value = $("option:selected", field).val();

		var data = {};

		data[field.attr('name')] = value;

		this.model.set(data);

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

		this.model.set(data);
	},
	saveManufacturer : function() {
		var self = this;
		var isValid = true;
		if (this.mode === 'add') {
			isValid = this.model.isValid(true);
		}
		if (isValid) {
			$('.btn-primary', this.el).button('loading');
			this.model.save({}, {
				error : function() {
					$('.btn-primary', self.el).button('reset');
				}
			});
		}

	},
	addBrand : function() {
		var self = this;
		$('.input-man-brand-error', self.el).text('');
		var brand = $('#inputManBrand', self.el).val();
		brand = $.trim(brand);
		$('.inline-error', self.el).text('');
		$('.control-group ', self.el).removeClass('error');
		if (brand !== '') {
			if (!this.model.get('LicensesManufacture')) {
				this.model.set({
					LicensesManufacture : []
				}, {
					silent : true
				});
			}

			// Also check first to see if this exists already

			var brands = this.model.get('LicensesManufacture');
			var add = true;
			_.each(brands, function(existingBrand) {
				if (existingBrand.License && existingBrand.License.name) {
					if (existingBrand.License.name.toLowerCase() === brand.toLowerCase()) {
						add = false
					}
				}
			});
			if (add) {
				brands.push({
					License : {
						name : brand
					}
				});
				this.model.set({
					LicensesManufacture : brands
				}, {
					silent : true
				});
				this.model.trigger("change:LicensesManufacture");
			} else {
				$('.input-man-brand-error', self.el).text('That brand has already been added.');
				//$('.input-man-brand-error', self.el).closest('control-group').addClass('error');
			}

		}
	}
});

var CollectibleView = Backbone.View.extend({
	template : 'collectible.default.edit',
	className : "span8",
	events : {
		'change #inputManufacturer' : 'changeManufacturer',
		'click #buttonSeries' : 'changeSeries',
		'click .save' : 'save',
		"change input" : "fieldChanged",
		"change select" : "selectionChanged",
		'change textarea' : 'fieldChanged',
		'click .manufacturer-add' : 'addManufacturer',
		'click .manufacturer-edit' : 'editManufacturer',
		'click .manufacturer-add-brand' : 'editManufacturer',
		'click .manufacturer-add-category' : 'editManufacturerSeries'
	},
	initialize : function(options) {
		var self = this;
		this.manufacturers = options.manufacturers;
		this.currencies = options.currencies;
		this.retailers = options.retailers;
		this.scales = options.scales;
		this.collectibleType = options.collectibleType;
		this.brands = options.brands;
		this.status = options.status;
		// this is information on the selected manufacturer
		if (options.manufacturer) {
			this.manufacturer = options.manufacturer;
			this.series = new SeriesModel({
				id : this.manufacturer.get('id')
			});
			this.seriesEdit = new SeriesModel({
				id : this.manufacturer.get('id'),
				mode : 'edit'
			});
		} else {
			this.series = new SeriesModel();
			this.seriesEdit = new SeriesModel({
				mode : 'edit'
			});
		}

		// do other init things

		// create years

		var minOffset = -3, maxOffset = 100;
		// Change to whatever you want
		var thisYear = (new Date()).getFullYear();
		this.years = [];

		for (var i = minOffset; i <= maxOffset; i++) {
			var year = thisYear - i;
			this.years.push(year);
		}

		//setup model events
		this.model.on("change:manufacture_id", function() {
			this.model.set({
				seriesPath : '',
				'series_id' : null,
				'license_id' : null
			}, {
				silent : true
			});
			this.render();
		}, this);
		this.model.on('change:retailer', function() {
			this.model.set({
				'retailer_id' : null
			}, {
				silent : true
			});
		}, this);
		this.model.on("change:limited", this.render, this);
		this.model.on("change:edition_size", this.render, this);
		this.model.on("change:series_id", this.render, this);
		this.manufacturers.on('add', this.render, this);
		this.seriesView = null;
		this.series.on('change', function() {
			if (this.seriesView) {
				this.seriesView.remove();
			}
			this.seriesView = new SeriesView({
				model : this.series
			});

			$.unblockUI();
			$('.modal-body', '#seriesModal').html(this.seriesView.render().el);

			$('#seriesModal').modal();
		}, this);

		this.seriesEdit.on('change', function() {
			var self = this;
			if (this.manufacturerSeriesView) {
				this.manufacturerSeriesView.remove();
			}
			this.manufacturerSeriesView = new ManufacturerSeriesView({
				model : this.seriesEdit,
				manufacturer : this.manufacturer
			});

			$.unblockUI();
			$('body').append(this.manufacturerSeriesView.render().el);
			$('#manufacturerSeriesModal', 'body').modal({
				backdrop : 'static'
			});

			$('#manufacturerSeriesModal', 'body').on('hidden', function() {
				self.manufacturerSeriesView.remove();
			});
		}, this);

		pageEvents.on('series:select', function(id, name) {
			$('#seriesModal').modal('hide');
			this.model.set({
				seriesPath : name,
				'series_id' : id
			});
		}, this);

		this.model.on('sync', this.onModelSaved, this);

		Backbone.Validation.bind(this);

		Backbone.Validation.bind(this, {
			valid : function(view, attr, selector) {
				view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
				view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
				view.$('[' + selector + '~="' + attr + '"]').closest('.control-group').removeClass('error');
				// do something
			},
			invalid : function(view, attr, error, selector) {
				view.$('[' + selector + '~="' + attr + '"]').addClass('invalid').attr('data-error', error);
				view.$('[' + selector + '~="' + attr + '"]').closest('.control-group').addClass('error');
				view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
				view.$('[' + selector + '~="' + attr + '"]').after('<span class="help-block _error">' + error + '</span>');
				// do something
			}
		});

	},
	onModelSaved : function(model, response, options) {
		$('.save', this.el).button('reset');
		$.blockUI({
			message : '<button class="close" data-dismiss="alert" type="button">×</button>Your changes have been successfully saved!',
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
	},
	render : function() {
		var self = this;
		var collectibleType = this.collectibleType.toJSON();
		var collectible = this.model.toJSON();
		var status = this.status.toJSON();

		var data = {
			collectible : collectible,
			manufacturers : this.manufacturers.toJSON(),
			currencies : this.currencies.toJSON(),
			years : this.years,
			scales : this.scales.toJSON(),
			collectibleType : collectibleType,
			brands : this.brands.toJSON()
		};
		if (this.manufacturer) {
			data.manufacturer = this.manufacturer.toJSON();
			// use this to sort but then add back to the manufacturer so we don't
			// have to pass more data around
			var brands = new Brands(data.manufacturer.LicensesManufacture);
			data.manufacturer.LicensesManufacture = brands.toJSON();
			data.renderBrandList = false;
		} else {
			// if there is no manufacturer selected and the collectible type
			// is a print
			if (collectibleType.id === printId) {
				data.renderBrandList = true;
			}
		}

		// If this collectible is submitted and we are
		// editing it. Do not allow adding new
		// manufacturer
		if (status.status.id === '4') {
			data.allowAddManufacturer = false;
		} else {
			data.allowAddManufacturer = true;
		}

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		$('#inputRetailer', self.el).typeahead({
			source : this.retailers,
			items : 100
		});

		return this;
	},
	changeManufacturer : function(event) {
		var field = $(event.currentTarget);

		var value = $("option:selected", field).val();

		// we also need to change the update manufacturer
		// will make it easier for the template
		var selectedManufacturer = null;
		_.each(this.manufacturers.models, function(manufacturer) {
			if (manufacturer.get('id') === value) {
				selectedManufacturer = manufacturer;
				return;
			}
		});

		this.manufacturer = selectedManufacturer;
		// Change the id on the series
		if (this.manufacturer !== null) {
			this.series.set({
				id : this.manufacturer.get('id')
			}, {
				silent : true
			});
		} else {
			this.series.set({
				id : ''
			}, {
				silent : true
			});
		}

	},
	changeSeries : function(event) {
		$.blockUI({
			message : 'Loading...',
			css : {
				border : 'none',
				padding : '15px',
				backgroundColor : ' #F1F1F1',
				'-webkit-border-radius' : '10px',
				'-moz-border-radius' : '10px',
				color : '#222',
				background : 'none repeat scroll 0 0 #F1F1F',
				'border-radius' : '5px 5px 5px 5px',
				'box-shadow' : '0 0 10px rgba(0, 0, 0, 0.5)'
			}
		});

		// This is a little ghetto, there should
		// be a better way to do this
		// Do a clear to make sure we are always getting new data
		// but then we need to set the id
		// then do a fetch
		this.series.clear({
			silent : true
		});
		this.series.set({
			id : this.manufacturer.get('id')
		}, {
			silent : true
		});
		this.series.fetch();
	},
	save : function(event) {
		event.preventDefault();
		$(event.currentTarget).button('loading');
		//TODO: validate
		this.model.save({}, {
			wait : true,
			error : function(model, response) {
				$(event.currentTarget).button('reset');
				if (response.status === 401) {
					var errors = [];
					errors.push({
						message : ['You do not have access.']
					});
					pageEvents.trigger('status:change:error', errors);
				}

			}
		});
	},
	selectionChanged : function(e) {
		var field = $(e.currentTarget);

		var value = $("option:selected", field).val();

		var data = {};

		data[field.attr('name')] = value;

		this.model.set(data, {
			forceUpdate : true
		});

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
			forceUpdate : true
		});
	},
	addManufacturer : function() {
		var self = this;
		if (this.manufacturerView) {
			this.manufacturerView.remove();
		}

		var manufacturer = new ManufacturerModel();
		manufacturer.set({
			'CollectibletypesManufacture' : {
				collectibletype_id : this.collectibleType.toJSON().id
			}
		}, {
			silent : true
		});

		manufacturer.on('sync', function() {
			$.blockUI({
				message : '<button class="close" data-dismiss="alert" type="button">×</button>Your manufacturer has been added!',
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

			// unbind all
			manufacturer.off();

			this.manufacturers.add(manufacturer);

			$('#manufacturerModal', 'body').modal('hide')

		}, this);

		this.manufacturerView = new ManufacturerView({
			model : manufacturer,
			brands : this.brands,
			mode : 'add'
		});

		$('body').append(this.manufacturerView.render().el);

		$('#manufacturerModal', 'body').modal({
			backdrop : 'static'
		});

		$('#manufacturerModal', 'body').on('hidden', function() {
			self.manufacturerView.remove();

		});
	},
	editManufacturer : function() {
		var self = this;
		if (this.manufacturerView) {
			this.manufacturerView.remove();
		}

		this.manufacturer.on('sync', function() {
			$.blockUI({
				message : '<button class="close" data-dismiss="alert" type="button">×</button>The manufacturer has been updated!',
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

			this.render();

			// unbind all
			this.manufacturer.off();

			$('#manufacturerModal', 'body').modal('hide')

		}, this);

		this.manufacturerView = new ManufacturerView({
			model : this.manufacturer,
			brands : this.brands,
			mode : 'edit'
		});

		$('body').append(this.manufacturerView.render().el);

		$('#manufacturerModal', 'body').modal({
			backdrop : 'static'
		});

		$('#manufacturerModal', 'body').on('hidden', function() {
			self.manufacturerView.remove();
			// on close I need to make sure we do not have
			// any brands attached to this manufacturer that are
			// not officially saved
			if (self.manufacturer.get('LicensesManufacture')) {
				var brands = self.manufacturer.get('LicensesManufacture');

				var len = brands.length;
				while (len--) {
					brand = brands[len];
					if (!brand.hasOwnProperty('id')) {
						brands.splice(len, 1);
					}
				}

				self.manufacturer.set({
					LicensesManufacture : brands
				}, {
					silent : true
				});
			}

		});
	},
	editManufacturerSeries : function() {

		$.blockUI({
			message : 'Loading...',
			css : {
				border : 'none',
				padding : '15px',
				backgroundColor : ' #F1F1F1',
				'-webkit-border-radius' : '10px',
				'-moz-border-radius' : '10px',
				color : '#222',
				background : 'none repeat scroll 0 0 #F1F1F',
				'border-radius' : '5px 5px 5px 5px',
				'box-shadow' : '0 0 10px rgba(0, 0, 0, 0.5)'
			}
		});

		// This is a little ghetto, there should
		// be a better way to do this
		// Do a clear to make sure we are always getting new data
		// but then we need to set the id
		// then do a fetch
		this.seriesEdit.clear({
			silent : true
		});
		this.seriesEdit.set({
			id : this.manufacturer.get('id'),
			mode : 'edit'
		}, {
			silent : true
		});
		this.seriesEdit.fetch();
	}
});

var MessageView = Backbone.View.extend({
	template : 'message.edit',
	className : "span12",
	events : {

	},
	initialize : function(options) {

		if (options.errors && _.size(options.errors) > 0) {
			this.hasErrors = true;
			this.errors = options.errors;
		} else {
			this.hasErrors = false;
		}
	},
	render : function() {
		var self = this;

		var data = {
			hasErrors : this.hasErrors,

		};
		if (this.hasErrors) {
			data.errors = this.errors.toJSON();
		}
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});

var DupListMessageView = Backbone.View.extend({
	template : 'message.duplist',
	className : "span12",
	events : {

	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;

		var data = {
			collectibles : this.collection.toJSON(),
			uploadDirectory : uploadDirectory
		};
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});

var TagsView = Backbone.View.extend({
	template : 'tags.edit',
	className : "span8 pull-right",
	events : {
		'click .save' : 'save',
	},
	initialize : function(options) {
		this.collection.on('add', this.render, this);
		this.collection.on('remove', this.render, this);
	},
	render : function() {
		var self = this;
		dust.render(this.template, {
			total : this.collection.length
		}, function(error, output) {
			$(self.el).html(output);
		});

		this.collection.each(function(tag) {
			$('ul.tags', self.el).append(new TagView({
				model : tag
			}).render().el);
		});

		if (this.collection.length < 5) {
			if (this.addTagView) {
				this.addTagView.remove();
			}
			this.addTagView = new AddTagView({
				collection : this.collection
			});
			$('.add-container', self.el).html(this.addTagView.render().el);
		}
		return this;
	},
	save : function() {
		this.collection.sync();
	}
});

var TagView = Backbone.View.extend({
	template : 'tag.edit',
	className : "li",
	tagName : 'li',
	events : {
		'click .remove-tag' : 'removeTag'
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;
		var tag = this.model.toJSON();
		dust.render(this.template, tag, function(error, output) {
			$(self.el).html(output);
		});
		return this;
	},
	removeTag : function() {
		this.model.destroy({
			wait : true,
			success : function(model, response) {
				var message = "The tag has been successfully deleted!";
				if (response.response.data) {
					if (response.response.data.hasOwnProperty('isEdit')) {
						if (response.response.data.isEdit) {
							message = "Your edit has been successfully submitted!";
						}
					}
				}

				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
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
			},
		});
	}
});

var AddTagView = Backbone.View.extend({
	template : 'tag.add',
	events : {
		'click .add-tag' : 'addTag',
		'keypress #inputTag' : 'inputChange'
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;
		dust.render(this.template, {}, function(error, output) {
			$(self.el).html(output);
		});

		$('#inputTag', self.el).typeahead({
			source : function(query, process) {
				$.get('/tags/getTagList', {
					query : query,
				}, function(data) {
					process(data.suggestions);
				});
			},
			items : 100
		});
		return this;
	},
	inputChange : function() {
		$('.inline-error', this.el).text('');
		$('.control-group ', this.el).removeClass('error');
	},
	addTag : function() {
		var self = this;
		var tag = $('#inputTag', self.el).val();
		tag = $.trim(tag);
		$('.inline-error', self.el).text('');
		$('.control-group ', self.el).removeClass('error');
		if (tag !== '') {
			this.collection.create({
				'collectible_id' : collectibleId,
				Tag : {
					tag : tag
				}
			}, {
				wait : true,
				success : function(model, response) {
					var message = "The tag has been successfully added!";
					if (response.response.data) {
						if (response.response.data.hasOwnProperty('isEdit')) {
							if (response.response.data.isEdit) {
								message = "Your edit has been successfully submitted!";
							}
						}
					}

					$.blockUI({
						message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
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
				},
				error : function(model, response) {
					var responseObj = $.parseJSON(response.responseText)
					if (responseObj.response && responseObj.response.errors) {
						$('.control-group ', self.el).addClass('error');
						$('.inline-error', self.el).text(responseObj.response.errors[0].message[0]);
					}
				}
			});

			$('#inputTag', self.el).val('');
		}
	}
});

var ArtistsView = Backbone.View.extend({
	template : 'artists.edit',
	className : "span8 pull-right",
	events : {
		'click .save' : 'save',
	},
	initialize : function(options) {
		this.collectibleType = options.collectibleType;
		this.collection.on('add', this.render, this);
		this.collection.on('remove', this.render, this);
	},
	render : function() {
		var self = this;
		dust.render(this.template, {
			total : this.collection.length,
			collectibleType : this.collectibleType.toJSON()
		}, function(error, output) {
			$(self.el).html(output);
		});

		this.collection.each(function(tag) {
			$('ul.artists', self.el).append(new ArtistView({
				model : tag
			}).render().el);
		});

		if (this.addArtistView) {
			this.addArtistView.remove();
		}
		this.addArtistView = new AddArtistView({
			collection : this.collection
		});
		$('.add-container', self.el).html(this.addArtistView.render().el);

		return this;
	},
	save : function() {
		this.collection.sync();
	}
});

var ArtistView = Backbone.View.extend({
	template : 'artist.edit',
	className : "li",
	tagName : 'li',
	events : {
		'click .remove-artist' : 'removeArtist'
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;
		var artist = this.model.toJSON();
		dust.render(this.template, artist, function(error, output) {
			$(self.el).html(output);
		});
		return this;
	},
	removeArtist : function() {
		this.model.destroy({
			wait : true,
			success : function(model, response) {
				var message = "The artist has been successfully deleted!";
				if (response.response.data) {
					if (response.response.data.hasOwnProperty('isEdit')) {
						if (response.response.data.isEdit) {
							message = "Your edit has been successfully submitted!";
						}
					}
				}

				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
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
			},
		});
	}
});

var AddArtistView = Backbone.View.extend({
	template : 'artist.add',
	events : {
		'click .add-artist' : 'addArtist',
		'keypress #inputArtist' : 'inputChange'
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;
		dust.render(this.template, {}, function(error, output) {
			$(self.el).html(output);
		});

		$('#inputArtist', self.el).typeahead({
			source : function(query, process) {
				$.get('/artists/getArtistList', {
					query : query,
				}, function(data) {
					process(data.suggestions);
				});
			},
			items : 100
		});
		return this;
	},
	inputChange : function() {
		$('.inline-error', this.el).text('');
		$('.control-group ', this.el).removeClass('error');
	},
	addArtist : function() {
		var self = this;
		var name = $('#inputArtist', self.el).val();
		name = $.trim(name);
		$('.inline-error', self.el).text('');
		$('.control-group ', self.el).removeClass('error');
		if (name !== '') {
			this.collection.create({
				'collectible_id' : collectibleId,
				Artist : {
					name : name
				}
			}, {
				wait : true,
				success : function(model, response) {
					var message = "The artist has been successfully added!";
					if (response.response.data) {
						if (response.response.data.hasOwnProperty('isEdit')) {
							if (response.response.data.isEdit) {
								message = "Your edit has been successfully submitted!";
							}
						}
					}

					$.blockUI({
						message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
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
				},
				error : function(model, response) {
					var responseObj = $.parseJSON(response.responseText)
					if (responseObj.response && responseObj.response.errors) {
						$('.control-group ', self.el).addClass('error');
						$('.inline-error', self.el).text(responseObj.response.errors[0].message[0]);
					}
				}
			});

			$('#inputArtist', self.el).val('');
		}
	}
});

var hasDupList = false;

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
	$.when($.get('/templates/collectibles/collectible.default.dust'), $.get('/templates/collectibles/photo.default.dust'), $.get('/templates/collectibles/attributes.default.dust'), $.get('/templates/collectibles/attribute.default.dust'), $.get('/templates/collectibles/status.dust'), $.get('/templates/collectibles/message.dust'), $.get('/templates/collectibles/tags.default.dust'), $.get('/templates/collectibles/tag.default.dust'), $.get('/templates/collectibles/tag.add.default.dust'), $.get('/templates/collectibles/message.duplist.dust'), $.get('/templates/collectibles/artists.default.dust'), $.get('/templates/collectibles/artist.default.dust'), $.get('/templates/collectibles/artist.add.default.dust'), $.get('/templates/collectibles/manufacturer.add.dust'), $.get('/templates/collectibles/manufacturer.edit.dust'), $.get('/templates/collectibles/modal.dust'), $.get('/templates/collectibles/manufacturer.series.add.dust'), $.get('/templates/collectibles/attribute.upload.dust'), $.get('/templates/collectibles/directional.dust')).done(function(collectibleTemplate, photoTemplate, attributesTemplate, attributeTemplate, statusTemplate, messageTemplate, tagsTemplate, tagTemplate, addTagTemplate, dupListTemplate, artistsTemplate, artistTemplate, addArtistTemplate, manufacturerAddTemplate, manufacturerEditTemplate, modalTemplate, manufacturerSeriesAddTemplate, attributeUploadTemplate, directionalTemplate) {
		dust.loadSource(dust.compile(collectibleTemplate[0], 'collectible.default.edit'));
		dust.loadSource(dust.compile(photoTemplate[0], 'photo.default.edit'));
		dust.loadSource(dust.compile(attributesTemplate[0], 'attributes.default.edit'));
		dust.loadSource(dust.compile(attributeTemplate[0], 'attribute.default.edit'));
		dust.loadSource(dust.compile(statusTemplate[0], 'status.edit'));
		dust.loadSource(dust.compile(messageTemplate[0], 'message.edit'));
		dust.loadSource(dust.compile(tagsTemplate[0], 'tags.edit'));
		dust.loadSource(dust.compile(tagTemplate[0], 'tag.edit'));
		dust.loadSource(dust.compile(addTagTemplate[0], 'tag.add'));
		dust.loadSource(dust.compile(dupListTemplate[0], 'message.duplist'));
		dust.loadSource(dust.compile(artistsTemplate[0], 'artists.edit'));
		dust.loadSource(dust.compile(artistTemplate[0], 'artist.edit'));
		dust.loadSource(dust.compile(addArtistTemplate[0], 'artist.add'));
		dust.loadSource(dust.compile(manufacturerAddTemplate[0], 'manufacturer.add'));
		dust.loadSource(dust.compile(manufacturerEditTemplate[0], 'manufacturer.edit'));
		dust.loadSource(dust.compile(modalTemplate[0], 'modal'));
		dust.loadSource(dust.compile(manufacturerSeriesAddTemplate[0], 'manufacturer.series.add'));
		dust.loadSource(dust.compile(attributeUploadTemplate[0], 'attribute.photo.edit'));
		dust.loadSource(dust.compile(directionalTemplate[0], 'directional.page'));

		$.ajax({
			url : "/collectibles/getCollectible/" + collectibleId,
			dataType : "json",
			cache : false,
			error : function(jqXHR, textStatus, errorThrown){
				jqXHR;
				textStatus;
				errorThrown;
			},
			complete : function(jqXHR, textStatus){
				jqXHR;
				textStatus;
			},
			success : function(data, textStatus, jqXHR) {
				$.unblockUI();

				// Setup the current model
				var collectibleModel = new CollectibleModel(data.response.data.collectible.Collectible);
				var collectibleTypeModel = new CollectibleTypeModel(data.response.data.collectible.Collectibletype);
				// Setup the manufacturer list, this will contain all data for each manufacturer
				var manufacturerList = new ManufacturerList(data.response.data.manufacturers);
				var currencies = new Currencies(data.response.data.currencies);
				var scales = new Scales(data.response.data.scales);
				var attributes = new Attributes(data.response.data.collectible.AttributesCollectible);
				var uploads = new CollectibleUploads(data.response.data.collectible.CollectiblesUpload, {
					'id' : data.response.data.collectible.Collectible.id
				});
				var brands = new Brands(data.response.data.brands);
				var tags = new Tags(data.response.data.collectible.CollectiblesTag);
				var artists = new Artists(data.response.data.collectible.ArtistsCollectible);

				var status = new Status();
				status.set({
					id : data.response.data.collectible.Collectible.id,
					status : data.response.data.collectible.Status
				}, {
					silent : true
				});
				var retailersArray = [];
				_.each(data.response.data.retailers, function(retailer) {
					retailersArray.push(retailer.Retailer.name);
				});

				// This could probably go in the init method but works here for now
				var selectedManufacturer = null;
				_.each(manufacturerList.models, function(manufacturer) {
					if (manufacturer.get('id') === collectibleModel.get('manufacture_id')) {
						selectedManufacturer = manufacturer;
						return;
					}
				});

				// Setup global events
				pageEvents.on('status:change:error', function(errors) {
					if (messageView) {
						messageView.remove();
						messageView = null;
					}
					messageView = new MessageView({
						errors : new Errors(errors)
					})

					$('#message-container').html(messageView.render().el);

				});

				pageEvents.on('status:change:dupList', function(collectibles) {
					hasDupList = true;
					status.set({
						'hasDupList' : hasDupList
					});
					if (messageView) {
						messageView.remove();
						messageView = null;
					}
					messageView = new DupListMessageView({
						collection : new Collectibles(collectibles)
					});

					$('#message-container').html(messageView.render().el);
				});

				pageEvents.on('collectible:delete', function() {
					collectibleModel.destroy({
						wait : true,
						error : function(model, response) {

							var responseObj = $.parseJSON(response.responseText);

							pageEvents.trigger('status:change:error', responseObj.response.errors);

						}
					});
				});

				pageEvents.on('upload:close', function() {
					uploads.fetch();
				});

				// Setup views
				var collectibleView = new CollectibleView({
					model : collectibleModel,
					manufacturers : manufacturerList,
					manufacturer : selectedManufacturer,
					currencies : currencies,
					retailers : retailersArray,
					scales : scales,
					status : status,
					collectibleType : collectibleTypeModel,
					brands : brands
				});

				$('#photo-container').append(new PhotoView({
					collection : uploads,
					eventManager : pageEvents
				}).render().el);

				$('#collectible-container').append(new ArtistsView({
					collection : artists,
					collectibleType : collectibleTypeModel
				}).render().el);
				$('#collectible-container').append(collectibleView.render().el);

				$('#attributes-container').append(new AttributesView({
					collection : attributes,
					status : status
				}).render().el);

				$('#status-container').html(new StatusView({
					model : status,
					allowEdit : true
				}).render().el);

				$('#collectible-container').append(new TagsView({
					collection : tags
				}).render().el);

				// $('#artists-container').append(new ArtistsView({
				// collection : artists
				// }).render().el);

				// Make sure we only have one
				var messageView = null;

				// If the status has changed and I am on the view
				//page and they change the status and it is a draft
				// go to the edit page
				status.on('sync', function() {
					if (this.toJSON().status.id === '2') {
						window.location.href = '/collectibles/view/' + this.id
					}
				}, status);

				collectibleModel.on('destroy', function() {
					window.location.href = '/users/home';
				});
				// view is overkill here
				dust.render('directional.page', {}, function(error, output) {
					$('#directional-text-container').html(output);
				});

			}
		});
	});

});
