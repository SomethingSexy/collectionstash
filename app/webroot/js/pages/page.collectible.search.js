$(function() {
	// Get all of the data here
	$.when($.get('/templates/collectibles/collectible.view.dust')).done(function(collectibleTemplate) {
		dust.loadSource(dust.compile(collectibleTemplate[0], 'collectible.view'));

	});

});
