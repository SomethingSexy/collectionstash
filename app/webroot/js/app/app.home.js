define(['marionette', 'collections/collection.collectibles'], function(Marionette, CollectiblesCollection) {
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
    MyApp.newCollectibles = new CollectiblesCollection(rawNewCollectibles, {
        mode: 'server',
        state: {
            pageSize: 4,
            totalRecords: totalNew
        },
        queryParams: {
            'status': 4,
            'o': 'n'
        }
    });
    MyApp.pendingCollectibles = new CollectiblesCollection(rawPending, {
        mode: 'server',
        state: {
            pageSize: 4,
            totalRecords: totalPending
        },
        queryParams: {
            'status': 2,
            'o': 'o'
        }
    });

    // export the app from this module
    return MyApp;
});