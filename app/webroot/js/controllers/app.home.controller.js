define(['app/app.home', 'backbone', 'marionette', 'text!templates/app/home/layout.mustache', 'views/app/home/view.collectibles', 'views/common/view.activities', 'views/app/home/view.points', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, layout, CollectiblesView, ActivitiesView, PointsView, mustache) {
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            regions: {
                pending: "._pending",
                newCollectibles: "._new",
                activities: "._activites",
                points : '._points'
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

                App.layout.newCollectibles.show(new CollectiblesView({
                    collection: App.newCollectibles
                }));


                App.layout.activities.show(new ActivitiesView({
                    collection: App.activities,
                    showMore: true
                }));

                App.layout.points.show(new PointsView({
                    model : App.points
                }));

                
            }
        });
    });