// TODO: We will need a model that will fetch all of teh series information given a manufacturer
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
var CollectibleTagModel = Backbone.Model.extend({});
var CollectibleUploadModel = Backbone.Model.extend({});
var CollectibleUploads = Backbone.Collection.extend({
	model : CollectibleUploadModel
})
var ManufacturerModel = Backbone.Model.extend({});
var CurrencyModel = Backbone.Model.extend({});
var Currencies = Backbone.Collection.extend({
	model : CurrencyModel
});
var Scale = Backbone.Model.extend({});
var Scales = Backbone.Collection.extend({
	model : Scale
});
var SeriesModel = Backbone.Model.extend({
	urlRoot : '/series/get'
});
var ManufacturerList = Backbone.Collection.extend({
	model : ManufacturerModel
});

var AttributeModel = Backbone.Model.extend({});

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
		attribute.manufacturerId = attributeModel.Attribute['manufacture_id'];
		attribute.id = attributeModel.Attribute.id;

		var attributeCollectible = {};
		attributeCollectible.id = attributeModel.id;
		attributeCollectible.attributeId = attributeModel['attribute_id'];
		attributeCollectible.categoryName = attributeModel.Attribute.AttributeCategory['path_name'];
		attributeCollectible.count = attributeModel.count;

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
	}
});

var PhotoView = Backbone.View.extend({
	template : 'photo.default.edit',
	className : "span4",
	events : {

	},
	initialize : function() {

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

		$('#fileupload').balls({
			'collectibleId' : collectibleId,
			'$element' : $('#upload-link', self.el)
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

var CollectibleView = Backbone.View.extend({
	template : 'collectible.default.edit',
	className : "span8",
	events : {
		'change #inputManufacturer' : 'changeManufacturer',
		'click #buttonSeries' : 'changeSeries',
		'click .save' : 'save',
		"change input" : "fieldChanged",
		"change select" : "selectionChanged",
		'change textarea' : 'fieldChanged'
	},
	initialize : function(options) {
		var self = this;
		this.manufacturers = options.manufacturers;
		this.currencies = options.currencies;
		this.retailers = options.retailers;
		this.scales = options.scales;
		// this is information on the selected manufacturer
		if (options.manufacturer) {
			this.manufacturer = options.manufacturer;
			this.series = new SeriesModel({
				id : this.manufacturer.get('id')
			});
		} else {
			this.series = new SeriesModel();
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
		this.model.on("change:limited", this.render, this);
		this.model.on("change:edition_size", this.render, this);
		this.model.on("change:series_id", this.render, this);
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

		pageEvents.on('series:select', function(id, name) {
			$('#seriesModal').modal('hide');
			this.model.set({
				seriesPath : name,
				'series_id' : id
			});
		}, this);

		this.model.on('sync', this.onModelSaved, this);

		Backbone.Validation.bind(this);

		this.model.bind('validated:valid', function(model) {
			$('.control-group.error', self.el).each(function() {
				$(this).removeClass('error');
				$(this).find('._error').remove();
			});
		});

		this.model.bind('validated:invalid', function(model) {
			// Remove all first
			$('.control-group.error', self.el).each(function() {
				$(this).removeClass('error');
				$(this).find('._error').remove();
			});
			// Now add any back
			$(':input.invalid', self.el).each(function() {
				$(this).closest('.control-group').addClass('error');
				$(this).after('<span class="help-block _error">' + $(this).attr('data-error') + '</span>');
			});
		});

	},
	onModelSaved : function(model, response, options) {
		$('.save', this.el).button('reset');
		$.blockUI({
			message : '<button class="close" data-dismiss="alert" type="button">×</button>Your changes have been successfully saved!',
			showOverlay : false,
			css : {
				top : '100px',
				'background-color' : '#DFF0D8',
				border : '1px solid #D6E9C6',
				'border-radius' : '4px 4px 4px 4px',
				color : '#468847',
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

		var data = {
			collectible : this.model.toJSON(),
			manufacturers : this.manufacturers.toJSON(),
			currencies : this.currencies.toJSON(),
			years : this.years,
			scales : this.scales.toJSON()
		};
		if (this.manufacturer) {
			data.manufacturer = this.manufacturer.toJSON();
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
		this.series.set({
			id : this.manufacturer.get('id')
		}, {
			silent : true
		});
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
	className : "span12",
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
						'background-color' : '#DFF0D8',
						border : '1px solid #D6E9C6',
						'border-radius' : '4px 4px 4px 4px',
						color : '#468847',
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
							'background-color' : '#DFF0D8',
							border : '1px solid #D6E9C6',
							'border-radius' : '4px 4px 4px 4px',
							color : '#468847',
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
	$.when($.get('/templates/collectibles/collectible.default.dust'), $.get('/templates/collectibles/photo.default.dust'), $.get('/templates/collectibles/attributes.default.dust'), $.get('/templates/collectibles/attribute.default.dust'), $.get('/templates/collectibles/status.dust'), $.get('/templates/collectibles/message.dust'), $.get('/templates/collectibles/tags.default.dust'), $.get('/templates/collectibles/tag.default.dust'), $.get('/templates/collectibles/tag.add.default.dust'), $.get('/templates/collectibles/message.duplist.dust')).done(function(collectibleTemplate, photoTemplate, attributesTemplate, attributeTemplate, statusTemplate, messageTemplate, tagsTemplate, tagTemplate, addTagTemplate, dupListTemplate) {
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

		$.ajax({
			url : "/collectibles/getCollectible/" + collectibleId,
			dataType : "json",
			cache : false,
			success : function(data, textStatus, jqXHR) {
				$.unblockUI();
				console.log(data);
				data.response.data.collectible.CollectiblesTag
				// Setup the current model
				var collectibleModel = new CollectibleModel(data.response.data.collectible.Collectible);
				// Setup the manufacturer list, this will contain all data for each manufacturer
				var manufacturerList = new ManufacturerList(data.response.data.manufacturers);
				var currencies = new Currencies(data.response.data.currencies);
				var scales = new Scales(data.response.data.scales);
				var attributes = new Attributes(data.response.data.collectible.AttributesCollectible);
				var uploads = new CollectibleUploads(data.response.data.collectible.CollectiblesUpload);

				var tags = new Tags(data.response.data.collectible.CollectiblesTag);

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

				var collectibleView = new CollectibleView({
					model : collectibleModel,
					manufacturers : manufacturerList,
					manufacturer : selectedManufacturer,
					currencies : currencies,
					retailers : retailersArray,
					scales : scales,
					status : status
				});

				$('#edit-container .row').append(new PhotoView({
					collection : uploads
				}).render().el);
				$('#edit-container .row').append(collectibleView.render().el);
				$('#attributes-container').append(new AttributesView({
					collection : attributes,
					status : status
				}).render().el);

				$('#status-container').html(new StatusView({
					model : status,
					allowEdit : true
				}).render().el);

				$('#tags-container').append(new TagsView({
					collection : tags
				}).render().el);

				// Make sure we only have one
				var messageView = null;
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
			}
		});
	});

});
