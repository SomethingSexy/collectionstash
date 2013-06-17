function timeDifference(current, previous) {

	var msPerMinute = 60 * 1000;
	var msPerHour = msPerMinute * 60;
	var msPerDay = msPerHour * 24;
	var msPerMonth = msPerDay * 30;
	var msPerYear = msPerDay * 365;

	var elapsed = current - previous;

	if (elapsed < msPerMinute) {
		return Math.round(elapsed / 1000) + 's';
	} else if (elapsed < msPerHour) {
		return Math.round(elapsed / msPerMinute) + 'm';
	} else if (elapsed < msPerDay) {
		return Math.round(elapsed / msPerHour) + 'h';
	} else if (elapsed < msPerMonth) {
		return Math.round(elapsed / msPerDay) + 'd';
	} else if (elapsed < msPerYear) {
		return Math.round(elapsed / msPerMonth) + 'mon';
	} else {
		return Math.round(elapsed / msPerYear) + 'y';
	}
}

var ActivityView = Backbone.View.extend({
	className : 'activity',
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
		// old api we need to account for
		if (data.Activity.data.object && data.Activity.data.object.objectType === 'collectible' && (data.Activity.activity_type_id === '6' || data.Activity.activity_type_id === '8'  )) {
			if (data.Activity.data.object.data.type) {
				data.Activity.data.object.data.Collectible = {
					displayTitle : data.Activity.data.object.data.displayName
				};

			}
		}

		// TODO: If it is a verb edit, check target and display ie. user edited part <name>
		// TODO: If it is a submit edit (type 7) and there is no object or target, hide
		// TODO: If it is a submit edit(type 7) we need to check the target objectType for display purposes

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
		
		$(this.el).attr('data-id', data.Activity.id);

		return this;
	}
});
