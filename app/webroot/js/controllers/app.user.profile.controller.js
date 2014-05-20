define(['app/app.user.profile', 'backbone', 'marionette', 'views/app/user/profile/view.header', 'views/app/user/profile/view.user', 'views/app/user/profile/view.facts', 'views/app/user/profile/view.stash', 'views/app/user/profile/view.wishlist', 'text!templates/app/user/profile/layout.mustache', 'text!templates/app/user/profile/layout.profile.mustache', 'text!templates/app/user/profile/layout.stash.mustache', 'views/common/modal.region', 'views/common/stash/view.stash.sell', 'views/common/stash/view.stash.remove', 'views/common/view.filters', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, HeaderView, UserView, FactsView, StashView, WishlistView, layout, profileLayout, stashLayout, ModalRegion, StashSellView, StashRemoveView, FiltersView, mustache) {

        // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
        var UserProfileLayout = Backbone.Marionette.Layout.extend({
            template: layout,
            regions: {
                header: '.header',
                userCard: '._user-card',
                main: '._main',
                modal: ModalRegion,
                facts: '._facts'
            }
        });

        var ProfileLayout = Backbone.Marionette.Layout.extend({
            template: profileLayout,
            regions: {

            }
        });

        var StashLayout = Backbone.Marionette.Layout.extend({
            template: stashLayout,
            regions: {
                stash: '._stash',
                filters: '._filters'
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
            index: function() {
                var profileLayout = new ProfileLayout();
                App.layout.main.show(profileLayout);


            },
            //gets mapped to in AppRouter's appRoutes
            stash: function() {
                var stashLayout = new StashLayout();
                App.layout.main.show(stashLayout);

                // TODO: probably need to check to see if we have stuff or not, this is blowing
                // up if you come back here while on the page, since it already has the first page, it does
                // not return a deferred
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

                    stashLayout.stash.show(stashView);

                    var filtersView = new FiltersView({
                        collection: App.filters
                    });


                    filtersView.on('filter:selected', function(type, values) {
                        App.collectibles.queryParams[type] = _.isArray(values)? values.join(','): values;
                        App.collectibles.fetch();
                    });

                    stashLayout.filters.show(filtersView);
                });

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