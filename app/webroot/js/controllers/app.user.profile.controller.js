define(['app/app.user.profile', 'backbone', 'marionette', 'views/app/user/profile/view.header', 'views/app/user/profile/view.user', 'text!templates/app/user/profile/layout.mustache', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, HeaderView, UserView, layout, mustache) {

        // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            // header (stash list)
            // sidebar
            // content - default activity
            //
            regions: {
                header: ".header",
                userCard: "._user-card"
            }
        });


        return Backbone.Marionette.Controller.extend({
            initialize: function(options) {
                App.layout = new UserProfileLayout();
                App.main.show(App.layout);

                App.layout.header.show(new HeaderView());
                App.layout.userCard.show(new UserView({
                    model: App.profile
                }));
            },
            //gets mapped to in AppRouter's appRoutes
            index: function() {
                // this is going to add 
                // App.main.show(new MoviesView({
                //     collection: App.movies
                // }));
            }
        });
    });