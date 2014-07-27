define(['app/app.home', 'backbone', 'marionette', 'text!templates/app/home/layout.mustache', 'views/app/home/view.collectibles', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, layout, CollectiblesView, mustache) {
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

                App.layout.pending.show(new CollectiblesView({
                    collection: App.pendingCollectibles
                }));
            }
        });
    });