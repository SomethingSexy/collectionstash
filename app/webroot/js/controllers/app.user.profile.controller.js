define(['app/app.user.profile', 'backbone', 'marionette'],
    function (App, Backbone, Marionette, HeaderView, MoviesView) {
    return Backbone.Marionette.Controller.extend({
        initialize:function (options) {
            //App.header.show(new HeaderView());
        },
        //gets mapped to in AppRouter's appRoutes
        index:function () {
            // App.main.show(new MoviesView({
            //     collection: App.movies
            // }));
        }
    });
});