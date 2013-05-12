(function() {

	if ( typeof exports !== "undefined") {
		dust = require("dustjs-linkedin");
	}

	if ( typeof dust.helpers === "undefined" || dust.helpers === null) {
		dust.helpers = {};
	}

	dust.helpers['empty'] = function(chunk, context, bodies, params) {
		var body = bodies.block, skip = bodies['else'];
		if (params && params.cond) {
			var cond = params.cond;
			cond = dust.helpers.tap(cond, chunk, context);
			// eval expressions with given dust references
			if (cond === '' || cond === false) {
				if (body) {
					return chunk.render(bodies.block, context);
				} else {
					_console.log("Missing body block in the if helper!");
					return chunk;
				}
			}
			if (skip) {
				return chunk.render(bodies['else'], context);
			}
		}
		// no condition
		else {
			_console.log("No condition given in the if helper!");
		}
		return chunk;
	}
})();
