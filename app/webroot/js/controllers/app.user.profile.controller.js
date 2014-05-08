define(['app/app.user.profile', 'backbone', 'marionette', 'views/app/user/profile/view.header', 'views/app/user/profile/view.user', 'views/app/user/profile/view.facts','text!templates/app/user/profile/layout.mustache', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, HeaderView, UserView, FactsView, layout, mustache) {

        // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            // header (stash list)
            // sidebar
            // content - default activity
            //
            regions: {
                header: '.header',
                userCard: '._user-card',
                facts: '._facts'
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
                App.layout.facts.show(new FactsView({
                    model: App.facts
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