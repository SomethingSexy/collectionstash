function timeDifference(current, previous) {

	var msPerMinute = 60 * 1000;
	var msPerHour = msPerMinute * 60;
	var msPerDay = msPerHour * 24;
	var msPerMonth = msPerDay * 30;
	var msPerYear = msPerDay * 365;

	var elapsed = current - previous;

	if (elapsed < msPerMinute) {
		return Math.round(elapsed / 1000) + ' seconds ago';
	} else if (elapsed < msPerHour) {
		return Math.round(elapsed / msPerMinute) + ' minutes ago';
	} else if (elapsed < msPerDay) {
		return Math.round(elapsed / msPerHour) + ' hours ago';
	} else if (elapsed < msPerMonth) {
		return 'approximately ' + Math.round(elapsed / msPerDay) + ' days ago';
	} else if (elapsed < msPerYear) {
		return 'approximately ' + Math.round(elapsed / msPerMonth) + ' months ago';
	} else {
		return 'approximately ' + Math.round(elapsed / msPerYear) + ' years ago';
	}
}

var ActivityView = Backbone.View.extend({
	className : 'row-fluid activity',
	template : 'activity',
	render : function() {
		var self = this;
		var data = this.model.toJSON();

		// dust doese not handle objects very well
		if (!data.Activity.data.target) {
			data.Activity.data.isTarget = false;
		} else {
			data.Activity.data.isTarget = true;
		}

		if (data.Activity.data.published) {
			var bits = data.Activity.data.published.split(/\D/);
			var date = new Date(bits[0], --bits[1], bits[2], bits[3], bits[4], bits[5]);

			var serverBits = serverTime.split(/\D/);
			var serverDate = new Date(serverBits[0], --serverBits[1], serverBits[2], serverBits[3], serverBits[4], serverBits[5]);

			var fancyDate = timeDifference(serverDate, date);
			data.Activity.data.fancyDate = fancyDate;
		}
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
