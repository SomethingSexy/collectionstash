define(function(require) {

    var Marionette = require('marionette'),
        CollectiblesCollection = require('collections/collection.companies'),
        ModalRegion = require('views/common/modal.region');
    // set up the app instance
    // TODO: we could probably have a base app that defines the header/footer
    var MyApp = new Marionette.Application();

    // configuration, setting up regions, etc ...
    MyApp.addRegions({
        main: '._home',
        modal: ModalRegion
    });

    var enablePushState = true;
    // Disable for older browsers
    var pushState = !! (enablePushState && window.history && window.history.pushState);
    MyApp.on('initialize:after', function() {
        Backbone.history.start({
            pushState: pushState,
            root: "/companies/"
        });
    });

    // adding initial collection here
    MyApp.companies = new CollectiblesCollection(rawCompanies);
    MyApp.permissions = new Backbone.Model(rawPermissions);

    if (typeof rawBrands !== 'undefined') {
        MyApp.brands = new Backbone.Collection(rawBrands);
    }

    // export the app from this module
    return MyApp;
});