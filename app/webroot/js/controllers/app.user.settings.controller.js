define(['app/app.user.settings', 'backbone', 'marionette', 'text!templates/app/user/settings/layout.dust', 'views/app/user/settings/view.menu', 'views/app/user/settings/view.profile', 'views/app/user/settings/view.privacy', 'dust', 'marionette-dust'],
    function(App, Backbone, Marionette, layout, MenuView, ProfileView, PrivacyView, dust) {

        dust.loadSource(dust.compile(layout, "user.settings.layout"));

        // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: "user.settings.layout",
            // header (stash list)
            // sidebar
            // content - default activity
            //
            regions: {
                sidebar: ".sidebar",
                main: ".main"
            }
        });


        return Backbone.Marionette.Controller.extend({
            initialize: function(options) {
                App.layout = new UserProfileLayout();
                App.main.show(App.layout);

                // not sure if this is the best way to do it
                var menuView = new MenuView();

                App.listenTo(menuView, 'navigate:privacy', function() {
                    // supposedly triggering true is a bad design, however I would
                    // just be calling the method manually in here so whatever, this works
                    // for now
                    Backbone.history.navigate('privacy', {
                        trigger: true
                    });
                });

                App.layout.sidebar.show(menuView);
            },
            //gets mapped to in AppRouter's appRoutes
            index: function() {
                // this is going to add 
                // App.main.show(new MoviesView({
                //     collection: App.movies
                // }));

                App.layout.main.show(new ProfileView({
                    model: App.profile
                }));
            },
            privacy: function() {
                // this is going to add 
                // App.main.show(new MoviesView({
                //     collection: App.movies
                // }));

                App.layout.main.show(new PrivacyView({
                    model: App.profile
                }));
            }
        });
    });