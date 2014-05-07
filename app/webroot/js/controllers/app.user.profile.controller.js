define(['app/app.user.profile', 'backbone', 'marionette', 'views/app/user/profile/view.header', 'text!templates/app/user/profile/layout.dust', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, HeaderView, layout, mustache) {

        // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            // header (stash list)
            // sidebar
            // content - default activity
            //
            regions: {
                header: ".header",
                anotherRegion: ".another-element"
            }
        });


        return Backbone.Marionette.Controller.extend({
            initialize: function(options) {
                App.layout = new UserProfileLayout();
                App.main.show(App.layout);

                App.layout.header.show(new HeaderView());
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