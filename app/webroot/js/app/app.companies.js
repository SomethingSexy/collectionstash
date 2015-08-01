define(function(require) {

    var Marionette = require('marionette'),
        CompaniesCollection = require('collections/collection.companies'),
        CompanyModel = require('models/model.company'),
        ModalRegion = require('views/common/modal.region'),
        _ = require('underscore');
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
    var models = _.map(rawCompanies, function(company) {
        return new CompanyModel(company, {
            parse: true
        });
    });

    MyApp.companies = new CompaniesCollection(models);
    MyApp.permissions = new Backbone.Model(rawPermissions);

    if (typeof rawBrands !== 'undefined') {
        MyApp.brands = new Backbone.Collection(rawBrands);
    }

    // export the app from this module
    return MyApp;
});