define(['app/app.user.profile', 'backbone', 'marionette', 'views/app/user/profile/view.header', 'views/app/user/profile/view.user', 'views/app/user/profile/view.facts', 'views/app/user/profile/view.stash', 'views/app/user/profile/view.wishlist', 'text!templates/app/user/profile/layout.mustache', 'views/common/modal.region', 'views/common/stash/view.stash.sell', 'views/common/stash/view.stash.remove', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, HeaderView, UserView, FactsView, StashView, WishlistView, layout, ModalRegion, StashSellView, StashRemoveView, mustache) {

        // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            regions: {
                header: '.header',
                userCard: '._user-card',
                facts: '._facts',
                main: '._main',
                modal: ModalRegion
            }
        });


        return Backbone.Marionette.Controller.extend({
            initialize: function(options) {
                App.layout = new UserProfileLayout();
                App.main.show(App.layout);

                var header = new HeaderView({
                    model: App.profile
                });

                App.layout.header.show(header);

                // supposedly triggering true is a bad design, however I would
                // just be calling the method manually in here so whatever, this works
                // for now
                App.listenTo(header, 'navigate:menu', function(route) {
                    Backbone.history.navigate(App.profile.get('username') + '/' + route, {
                        trigger: true
                    });
                });


                App.layout.userCard.show(new UserView({
                    model: App.profile
                }));
                App.layout.facts.show(new FactsView({
                    model: App.facts
                }));
            },
            //gets mapped to in AppRouter's appRoutes
            index: function() {

                App.collectibles.getFirstPage().done(function() {

                    var stashView = new StashView({
                        collection: App.collectibles,
                        permissions: App.permissions
                    });

                    stashView.on('stash:remove', function(id) {

                    });

                    stashView.on('stash:sell', function(id) {
                        App.layout.modal.show(new StashSellView({
                            model: App.collectibles.get(id)
                        }));
                    });

                    App.layout.main.show(stashView);
                })

            },
            wishlist: function() {
                App.layout.main.show(new WishlistView({
                    // model: App.facts
                }));
            },
            sale: function() {

            },
            photos: function() {

            },
            comments: function() {

            },
            history: function() {

            }
        });
    });