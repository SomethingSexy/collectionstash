define(['backbone', 'marionette'], function(Backbone, Marionette) {
    return Backbone.Marionette.AppRouter.extend({
        //"index" must be a method in AppRouter's controller
        appRoutes: {
            "*username/profile": "index",
            "*username/stash": "stash",
            "*username/stash/tiles": "stash",
            "*username/stash/list": "stashList",
            "*username/wishlist": "wishlist",
            "*username/wishlist/tiles": "wishlist",
            "*username/wishlist/list": "wishlistList",
            "*username/sale": "sale",
            "*username/photos": "photos",
            "*username/history": "history",
            "*username": "index"
        }
    });
});