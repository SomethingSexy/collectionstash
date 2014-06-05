define(['app/app.user.profile', 'backbone', 'marionette', 'views/app/user/profile/view.header', 'views/app/user/profile/view.user', 'views/app/user/profile/view.facts', 'views/app/user/profile/view.stash', 'views/app/user/profile/view.wishlist', 'text!templates/app/user/profile/layout.mustache', 'text!templates/app/user/profile/layout.profile.mustache', 'text!templates/app/user/profile/layout.stash.mustache', 'views/common/modal.region', 'views/common/stash/view.stash.sell', 'views/common/stash/view.stash.remove', 'views/common/view.filters', 'text!templates/app/user/profile/layout.wishlist.mustache', 'mustache', 'marionette.mustache'],
    function(App, Backbone, Marionette, HeaderView, UserView, FactsView, StashView, WishlistView, layout, profileLayout, stashLayout, ModalRegion, StashSellView, StashRemoveView, FiltersView, wishlistLayout, mustache) {

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
            initialize: function(options) {
                this.permissions = options.permissions;
            },
            serializeData: function() {
                var data = this.model.toJSON();
                data['edit_collectible_user'] = this.permissions.get('edit_collectible_user');
                return data;
            },
            events: {
                'click ._filtersLink': function(event) {
                    event.preventDefault();
                    this.trigger('toggle:filters');
                }

            },
            regions: {
                stash: '._stash',
                filters: '._filters'
            }
        });

        var WishlistLayout = Backbone.Marionette.Layout.extend({
            template: wishlistLayout,
            initialize: function(options) {
                this.permissions = options.permissions;
            },
            serializeData: function() {
                var data = this.model.toJSON();
                data['edit_collectible_user'] = this.permissions.get('edit_collectible_user');
                return data;
            },
            events: {


            },
            regions: {
                wishlist: '._wishlist'
            }
        });

        function renderStash() {
            var stashLayout = new StashLayout({
                permissions: App.permissions,
                model: App.profile
            });

            var filtersVisible = false;

            stashLayout.on('toggle:filters', function() {
                if (filtersVisible) {
                    stashLayout.filters.close();
                    // TODO: on close we need to cache the selected filters
                    // we could probably pass in some sort of backbone collection or model
                    // to store the selected values 
                    filtersVisible = false;
                } else {
                    filtersVisible = true;
                    var filtersView = new FiltersView({
                        collection: App.filters
                    });

                    stashLayout.filters.show(filtersView);

                    filtersView.on('filter:selected', function(type, values) {
                        App.collectibles.queryParams[type] = _.isArray(values) ? values.join(',') : values;
                        // reset current page to 1, 
                        App.collectibles.state.currentPage = 1;
                        App.collectibles.fetch();
                    });
                }
            });
            App.layout.main.show(stashLayout);

            var stashView = new StashView({
                collection: App.collectibles,
                permissions: App.permissions
            });

            stashView.on('stash:remove', function(id) {
                App.layout.modal.show(new StashRemoveView({
                    model: App.collectibles.fullCollection.get(id),
                    reasons: App.reasonsCollection
                }));
            });

            stashView.on('stash:sell', function(id) {
                App.layout.modal.show(new StashSellView({
                    model: App.collectibles.fullCollection.get(id)
                }));
            });

            stashLayout.stash.show(stashView);
        }

        function renderWishlist() {
            var layout = new WishlistLayout({
                permissions: App.permissions,
                model: App.profile
            });
            App.layout.main.show(layout);

            var stashView = new WishlistView({
                collection: App.wishlist,
                permissions: App.permissions
            });

            stashView.on('stash:remove', function(id) {
                App.layout.modal.show(new StashRemoveView({
                    model: App.wishlist.fullCollection.get(id),
                    reasons: App.reasonsCollection
                }));
            });

            stashView.on('stash:sell', function(id) {
                App.layout.modal.show(new StashSellView({
                    model: App.wishlist.fullCollection.get(id)
                }));
            });

            layout.wishlist.show(stashView);
        }

        function renderHeader(selectedMenu) {
            var header = new HeaderView({
                model: App.profile,
                selectedMenu: selectedMenu
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
        }

        return Backbone.Marionette.Controller.extend({
            initialize: function(options) {
                App.layout = new UserProfileLayout();
                App.main.show(App.layout);

                App.layout.userCard.show(new UserView({
                    model: App.profile,
                    facts: App.facts
                }));

                App.layout.facts.show(new FactsView({
                    model: App.facts
                }));

            },
            index: function() {
                renderHeader('profile');
                var profileLayout = new ProfileLayout();
                App.layout.main.show(profileLayout);
            },
            stash: function() {
                renderHeader('stash');
                if (App.collectibles.isEmpty()) {
                    App.collectibles.getFirstPage().done(renderStash);
                } else {
                    renderStash();
                }
            },
            wishlist: function() {
                renderHeader('wishlist');
                if (App.wishlist.isEmpty()) {
                    App.wishlist.getFirstPage().done(renderWishlist);
                } else {
                    renderWishlist();
                }
            },
            sale: function() {
                renderHeader('sale');
            },
            photos: function() {
                renderHeader('photos');
            },
            comments: function() {
                renderHeader('comments');
            },
            history: function() {
                renderHeader('history');
            }
        });
    });