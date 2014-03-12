var AlertView = Backbone.View.extend({
	template : 'alert',
	events : {

	},
	initialize : function(options) {
		options = options || {};

		this.error = options.error || false;

		if (options.responseText) {
			if (options.responseText.replace(/(?:\\[rn]|[\r\n]+)+/g, "") === '') {
				options.responseText = null;
			}
		}

		// if there is a title message override, use that
		if (options.titleMessage) {
			this.titleMessage = options.titleMessage;
		} else if (this.error && options.status && options.responseText) {
			this.titleMessage = options.responseText;
		} else {
			this.titleMessage = false;
		}

		// default true
		this.dismiss = options.dismiss === false ? false : true;
		this.messages = options.messages;

	},
	render : function() {
		var self = this;

		var data = {
			error : this.error,
			titleMessage : this.titleMessage,
			dismiss : this.dismiss
		};

		if (this.collection && this.collection.size() > 0) {
			data.hasMessages = true;
			data.messages = this.messages.toJSON();
		} else {
			data.hasMessages = false;
		}

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
