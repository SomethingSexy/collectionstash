define(['backbone', 'marionette'], function(Backbone, Marionette) {
    return Backbone.Marionette.AppRouter.extend({
        //"index" must be a method in AppRouter's controller
        appRoutes: {
            "*username": "index",
            "*username/stash": "index",
            "*username/wishlist": "wishlist",
            "*username/sale": "sale",
            "*username/photos": "photos",
            "*username/comments": "comments",
            "*username/history": "history"
        }
    });
});