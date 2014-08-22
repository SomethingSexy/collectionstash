define(['marionette', 'models/model.profile', 'models/model.stash.settings'], function(Marionette, ProfileModel, StashSettingsModel) {
    // set up the app instance
    // TODO: we could probably have a base app that defines the header/footer
    var MyApp = new Marionette.Application();

    // configuration, setting up regions, etc ...
    MyApp.addRegions({
        //header: '#header',
        main: '#main',
        //footer: '#footer'
    });
    var enablePushState = true;

    // Disable for older browsers
    var pushState = !! (enablePushState && window.history && window.history.pushState);
    MyApp.on('initialize:after', function() {
        Backbone.history.start({
            pushState: pushState,
            root: "/settings/"
        });
    });

    MyApp.vent.on("search:term", function(searchTerm) {
        Backbone.history.navigate("search/" + searchTerm);
    });

    // adding initial collection here
    MyApp.profile = new ProfileModel(rawProfile);
    MyApp.stashSettings = new StashSettingsModel(rawStashSettings);

    // export the app from this module
    return MyApp;
});