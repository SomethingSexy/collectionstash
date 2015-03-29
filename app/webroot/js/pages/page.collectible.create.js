var pageEvents = _.extend({}, Backbone.Events);

var CollectibleTypeView = Backbone.View.extend({
	template : 'create.collectibletype',

	events : {
		'click span.item' : 'selectType'
	},
	initialize : function(options) {
		this.typesHtml = options.typesHtml
	},
	render : function() {
		var self = this;
		dust.render(this.template, {
			types : this.typesHtml
		}, function(error, output) {
			$(self.el).html(output);
		});
		return this;
	},
	selectType : function(event) {
		var collectibleTypeId = $(event.currentTarget).attr('data-id');
		event.preventDefault();
		pageEvents.trigger('view:select:collectibletype', collectibleTypeId);

	}
});

var TypeView = Backbone.View.extend({
	template : 'create.type',

	events : {
		'click a.mass' : 'addMass',
		'click a.original' : 'addOriginal',
		'click a.custom' : 'addCustom'
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;
		dust.render(this.template, {
		}, function(error, output) {
			$(self.el).html(output);
		});
		return this;
	},
	addMass : function(event) {
		event.preventDefault();
		pageEvents.trigger('view:select:type', 'mass');
	},
	addOriginal : function(event) {
		event.preventDefault();
		pageEvents.trigger('view:select:type', 'original');
	},
	addCustom : function(event) {
		event.preventDefault();
		pageEvents.trigger('view:select:type', 'custom');
	}
});

$(function() {
	var selectedCollectibleType = null;
	var custom = false;
	var type = '';
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
	$.when($.get('/templates/collectibles/create.type.dust')).done(function(typeTemplate) {
		dust.loadSource(dust.compile(typeTemplate, 'create.type'));
		$.unblockUI();

		var collectibleTypeView = new TypeView();
		$('#create-container').html(collectibleTypeView.render().el);

		// global page event

		pageEvents.on('view:select:type', function(selectedType) {
			type = selectedType;

			if (type === 'mass') {
				window.location.href = '/collectibles/create/false/false';
			} else if (type === 'custom') {
				window.location.href = '/collectibles/create/false/true';
			} else if (type === 'original') {
				window.location.href = '/collectibles/create/true/false';
			}
		});
	});

});
