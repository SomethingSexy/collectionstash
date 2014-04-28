define( ["marionette"], function (Marionette, MoviesCollection) {

    // set up the app instance
    // TODO: we could probably have a base app that defines the header/footer
    var MyApp = new Marionette.Application();

    // configuration, setting up regions, etc ...
	MyApp.addRegions({
		//header: '#header',
		main: '#main',
		//footer: '#footer'
	});

	MyApp.on('initialize:after', function () {
		Backbone.history.start();
	});

	// adding initial collection here
	//MyApp.movies = new MoviesCollection(rawMovies);

    // export the app from this module
    return MyApp;
});