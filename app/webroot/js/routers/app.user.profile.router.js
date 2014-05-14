define(['backbone', 'marionette'], function(Backbone, Marionette) {
    return Backbone.Marionette.AppRouter.extend({
        //"index" must be a method in AppRouter's controller
        appRoutes: {
            "*username/profile": "index",
            "*username/stash": "stash",
            "*username/wishlist": "wishlist",
            "*username/sale": "sale",
            "*username/photos": "photos",
            "*username/comments": "comments",
            "*username/history": "history",
            "*username": "index"
        }
    });
});