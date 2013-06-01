var StashAddView = Backbone.View.extend({
	template : 'stash.add',
	events : {
		"change input" : "fieldChanged",
		"change select" : "selectionChanged",
		'change textarea' : 'fieldChanged',
		'click .save' : 'addCollectible'
	},
	initialize : function(options) {
		this.collectible = options.collectible;
	},
	render : function() {
		var self = this;
		var data = this.collectible.toJSON();
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		$("#CollectiblesUserPurchaseDate", this.el).datepicker();

		$('#CollectiblesUserMerchantId', this.el).typeahead({
			source : function(query, process) {
				$.get('/merchants/getMerchantList', {
					query : query,
				}, function(data) {
					process(data.suggestions);
				});
			},
			items : 100
		});

		return this;
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
	addCollectible : function() {
		var self = this;
		this.model.save({}, {
			success : function(model, response, options) {
				if (response.response.isSuccess) {
					self.trigger('add:success');
				} else {
					if (response.response.errors) {
						$.each(response.response.errors, function(index, value) {
							if (value.inline) {
								$(':input[name="' + value.name + '"]', self.el).after('<div class="error-message">' + value.message + '</div>');
							} else {
								$('#attribute-collectible-add-existing-dialog').find('.component-message.error').children('span').text(value.message);
							}

						});
					}
				}
			},
			error : function(model, xhr, options) {

			}
		});
	}
});
