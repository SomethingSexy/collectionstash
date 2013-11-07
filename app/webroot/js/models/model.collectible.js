if (!adminMode) {
	var adminMode = false;
}
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
		}],
		retailer : [{
			rangeLength : [4, 150],
			msg : 'Invalid length.  Must be between 4 and 150 characters.'
		}, {
			required : false
		}],
	}
});
