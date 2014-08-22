define(['marionette',
    'backbone',
    'underscore',
    'models/model.profile',
    'collections/collection.collectible.user',
    'collections/collection.filters.selected',
    'collections/collection.collectible.wishlist',
    'collections/collection.comments',
    'collections/collection.photos',
    'collections/collection.history',
    'collections/collection.edits',
    'models/model.history.graph',
    'collections/collection.collectible.sale',
    'collections/collection.work',
    'collections/collection.collectible.history'
], function(Marionette, Backbone, _, ProfileModel, CollectiblesCollection, SelectedFilters, WishlistCollection, CommentCollection, PhotosCollection, HistoryCollection, EditsCollection, GraphModel, SaleCollection, WorkCollection, SubmissionCollection) {
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
            root: "/profile/"
        });
    });

    MyApp.profile = new ProfileModel(rawProfile);
    MyApp.facts = new Backbone.Model(rawFacts);
    MyApp.permissions = new Backbone.Model(rawPermissions);
    MyApp.reasonsCollection = new Backbone.Collection(rawReasons);
    MyApp.activity = new Backbone.Collection(rawActivity);
    if (rawComments) {
        MyApp.comments = new CommentCollection(rawComments);
    }
    if (rawStashFacts) {
        MyApp.stashFacts = new Backbone.Model(rawStashFacts);
    }

    MyApp.photos = new PhotosCollection([], {
        username: MyApp.profile.get('username')
    });

    MyApp.history = new HistoryCollection([], {
        username: MyApp.profile.get('username')
    });

    MyApp.histroyGraph = new GraphModel({}, {
        username: MyApp.profile.get('username')
    });

    MyApp.sales = new SaleCollection([], {
        username: MyApp.profile.get('username')
    });

    MyApp.work = new WorkCollection(rawWork, {
        username: MyApp.profile.get('username')
    });

    MyApp.submissions = new SubmissionCollection([], {
        username: MyApp.profile.get('username')
    });

    MyApp.edits = new EditsCollection([], {
        username: MyApp.profile.get('username')
    });

    MyApp.selectedFilters = new SelectedFilters();

    // process the filters and put them in a format taht is 
    var filters = new Backbone.Collection();
    _.each(rawFilters, function(rawFilter, key) {
        var filter = _.clone(rawFilter);
        delete filter.id;
        filter['filterKey'] = key;

        filter.values = [];
        _.each(rawFilter.values, function(value, valueKey) {
            filter.values.push({
                key: valueKey,
                value: value
            });
        });

        filters.add(filter);

    });

    MyApp.filters = filters;
    // export the app from this module
    return MyApp;
});