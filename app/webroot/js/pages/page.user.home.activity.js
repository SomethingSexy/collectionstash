var CollectibleModel = Backbone.Model.extend({});
var EditModel = Backbone.Model.extend({});





var EditsView = Backbone.View.extend({
	template : 'edits',
	tagName : 'div',
	className : 'table-responsive',
	initialize : function() {
		this.collection.on('change', this.render, this);
		this.collection.on('reset', this.render, this);
	},
	render : function() {
		var self = this;
		var data = {

		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.collection.each(function(model) {
			$('tbody', self.el).append(new EditView({
				model : model
			}).render().el);
		});

		return this;
	}
});

var CollectiblesView = Backbone.View.extend({
	template : 'collectibles',
	tagName : 'div',
	className : 'table-responsive',
	initialize : function() {
		this.collection.on('change', this.render, this);
		this.collection.on('reset', this.render, this);
	},
	render : function() {
		var self = this;
		var data = {

		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.collection.each(function(model) {
			$('tbody', self.el).append(new CollectibleView({
				model : model
			}).render().el);
		});

		return this;
	}
});

var CollectibleView = Backbone.View.extend({
	tagName : 'tr',
	template : 'collectible',
	events : {
		'click' : 'selectCollectible'
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	selectCollectible : function(event) {
		event.preventDefault();
		var collectible = this.model.toJSON();
		if (collectible.Status.id == 1) {
			window.location.href = '/collectibles/edit/' + collectible.Collectible.id;
		} else if (collectible.Status.id == 2) {
			window.location.href = '/collectibles/view/' + collectible.Collectible.id;
		} else {
			window.location.href = '/collectibles/view/' + collectible.Collectible.id;
		}
	}
});

var EditView = Backbone.View.extend({
	tagName : 'tr',
	template : 'edit',
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
//

$(function() {

	$.when($.get('/templates/user/edit.dust'), $.get('/templates/user/edits.dust'), $.get('/templates/common/paging.dust')).done(function( editTemplate, editsTemplate, pagingTemplate) {
		dust.loadSource(dust.compile(editTemplate[0], 'edit'));
		dust.loadSource(dust.compile(editsTemplate[0], 'edits'));
		dust.loadSource(dust.compile(pagingTemplate[0], 'paging'));


		$('.edits .panel-heading').after(new EditsView({
			collection : edits
		}).render().el);

		$('.edits .panel-footer').append(new PagingView({
			collection : edits
		}).render().el);

	});

});
