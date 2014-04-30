var NotificationsView = Backbone.View.extend({
	template : 'profile.notifications',
	className : 'tab-pane',
	events : {
		'click #notifications-submit' : 'save',
		"change input" : "fieldChanged",
		// "change select" : "selectionChanged",
		'change textarea' : 'fieldChanged'
	},
	initialize : function() {
		this.listenTo(this.model, 'sync', this.onModelSaved, this);
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		$(this.el).attr('id', 'notifications');
		return this;
	},
	save : function(event) {
		event.preventDefault();
		$(event.currentTarget).button('loading');
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
	onModelSaved : function(model, response, options) {
		$('#notifications-submit', this.el).button('reset');
		var message = 'You have successfully updated your settings.';

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
});