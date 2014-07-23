define(['app/app.home', 'backbone', 'marionette', 'text!templates/app/home/layout.mustache', 'views/app/user/settings/view.menu', 'views/app/user/settings/view.profile', 'views/app/user/settings/view.stash', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, layout, MenuView, ProfileView, PrivacyView, mustache) {
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            regions: {
                pending: "._pending",
                newCollectibles: "._new"
            }
        });


        return Backbone.Marionette.Controller.extend({
            initialize: function(options) {
                App.layout = new UserProfileLayout();
                App.main.show(App.layout);
            },
            index: function() {
                // this is going to add 
                // App.main.show(new MoviesView({
                //     collection: App.movies
                // }));

                //     App.layout.main.show(new ProfileView({
                //         model: App.profile
                //     }));
            }
        });
    });