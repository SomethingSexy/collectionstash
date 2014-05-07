define(['marionette', 'models/model.profile'], function(Marionette, ProfileModel) {
    // set up the app instance
    // TODO: we could probably have a base app that defines the header/footer
    var MyApp = new Marionette.Application();

    // configuration, setting up regions, etc ...
    MyApp.addRegions({
        //header: '#header',
        main: '#main',
        //footer: '#footer'
    });

    MyApp.on('initialize:after', function() {
        Backbone.history.start();
    });

    MyApp.profile = new ProfileModel(rawProfile);

    // export the app from this module
    return MyApp;
});