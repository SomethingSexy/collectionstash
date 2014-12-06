define(function(require) {

    var Marionette = require('marionette'),
        CollectiblesCollection = require('collections/collection.companies');
    // set up the app instance
    // TODO: we could probably have a base app that defines the header/footer
    var MyApp = new Marionette.Application();

    // configuration, setting up regions, etc ...
    MyApp.addRegions({
        main: '._home'
    });

    MyApp.on('initialize:after', function() {
        Backbone.history.start({});
    });

    // adding initial collection here
    MyApp.companies = new CollectiblesCollection(rawCompanies);
    
    // export the app from this module
    return MyApp;
});