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
	$.when($.get('/templates/collectibles/create.collectibletype.dust'), $.get('/templates/collectibles/create.type.dust')).done(function(collectibleTypeTemplate, typeTemplate) {
		dust.loadSource(dust.compile(collectibleTypeTemplate[0], 'create.collectibletype'));
		dust.loadSource(dust.compile(typeTemplate[0], 'create.type'));
		$.unblockUI();

		var collectibleTypeView = new TypeView();
		$('#create-container').html(collectibleTypeView.render().el);

		// global page events

		// We will cache the type they selected and then
		// we will show the next view
		pageEvents.on('view:select:collectibletype', function(collectibleTypeId) {
			selectedCollectibleType = collectibleTypeId;

			if (type === 'mass') {
				window.location.href = '/collectibles/create/' + selectedCollectibleType + '/false/false';
			} else if (type === 'custom') {
				window.location.href = '/collectibles/create/' + selectedCollectibleType + '/false/true';
			} else if (type === 'original') {
				window.location.href = '/collectibles/create/' + selectedCollectibleType + '/true/false';
			}
		});

		pageEvents.on('view:select:type', function(selectedType) {
			type = selectedType;
			collectibleTypeView.remove();

			var typeView = new CollectibleTypeView({
				typesHtml : collectiblTypeHtml
			});

			$('#create-container').html(typeView.render().el);
		});
	});

});
