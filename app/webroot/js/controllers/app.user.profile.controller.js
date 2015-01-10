define(function(require) {

    var App = require('app/app.user.profile'),
        Backbone = require('backbone'),
        Marionette = require('marionette'),
        HeaderView = require('views/app/user/profile/view.header'),
        UserView = require('views/app/user/profile/view.user'),
        FactsView = require('views/app/user/profile/view.facts'),
        StashFactsView = require('views/app/user/profile/view.stash.facts'),
        StashView = require('views/app/user/profile/view.stash'),
        StashTableView = require('views/app/user/profile/view.stash.table'),
        WishlistView = require('views/app/user/profile/view.wishlist'),
        PhotosView = require('views/app/user/profile/view.photos'),
        WishlistTableView = require('views/app/user/profile/view.wishlist.table'),
        HistoryView = require('views/app/user/profile/view.history'),
        HistoryChartView = require('views/app/user/profile/view.history.chart'),
        SaleView = require('views/app/user/profile/view.sale'),
        WorkView = require('views/app/user/profile/view.work'),
        SubmissionsView = require('views/app/user/profile/view.submissions'),
        EditsView = require('views/app/user/profile/view.edits'),
        ActivitiesView = require('views/common/view.activities'),
        LoaderView = require('views/common/view.loader'),
        layout = require('text!templates/app/user/profile/layout.mustache'),
        profileLayout = require('text!templates/app/user/profile/layout.profile.mustache'),
        photosLayout = require('text!templates/app/user/profile/layout.photos.mustache'),
        stashLayout = require('text!templates/app/user/profile/layout.stash.mustache'),
        historyLayout = require('text!templates/app/user/profile/layout.history.mustache'),
        saleLayout = require('text!templates/app/user/profile/layout.sale.mustache'),
        activityLayout = require('text!templates/app/user/profile/layout.activity.mustache'),
        ModalRegion = require('views/common/modal.region'),
        StashSellView = require('views/common/stash/view.stash.sell'),
        StashListingEditView = require('views/common/stash/view.stash.listing.edit'),
        StashRemoveView = require('views/common/stash/view.stash.remove'),
        StashAddView = require('views/common/stash/view.stash.add'),
        FiltersView = require('views/common/view.filters'),
        growl = require('views/common/growl'),
        CollectibleUser = require('models/model.collectible.user'),
        CollectiblesCollection = require('collections/collection.collectible.user'),
        WishlistCollection = require('collections/collection.collectible.wishlist'),
        CommentsView = require('views/common/view.comments'),
        CommentAddView = require('views/common/view.comment.add'),
        wishlistLayout = require('text!templates/app/user/profile/layout.wishlist.mustache'),
        mustache = require('mustache'),
        StashUploadView = require('views/app/user/profile/view.stash.upload');
    require('marionette.mustache');



    // TODO: It might make sense to add the layout in the controller, depending on what the user is looking at
    var UserProfileLayout = Backbone.Marionette.Layout.extend({
        template: layout,
        regions: {
            header: '.header',
            userCard: '._user-card',
            main: '._main',
            modal: ModalRegion
        }
    });

    var ProfileLayout = Backbone.Marionette.Layout.extend({
        template: profileLayout,
        regions: {
            comments: '._comments',
            stashFacts: '._stashFacts',
            activity: '._activity',
            work: '._work',
            facts: '._facts'
        }
    });

    var ActivityLayout = Backbone.Marionette.Layout.extend({
        template: activityLayout,
        regions: {
            submissions: '._submissions',
            edits: '._edits'
        }
    });


    var PhotosLayout = Backbone.Marionette.Layout.extend({
        template: photosLayout,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data['upload_photos'] = this.permissions.get('upload_photos');
            data['edit_photos'] = this.permissions.get('edit_photos');
            return data;
        },
        regions: {
            photos: '._photos'
        }
    });

    var HistoryLayout = Backbone.Marionette.Layout.extend({
        template: historyLayout,
        regions: {
            history: '._history',
            chart: '._chart'
        }
    });

    var SaleLayout = Backbone.Marionette.Layout.extend({
        template: saleLayout,
        regions: {
            sales: '._sale'
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
            },
            'click ._tilesLink': function(event) {
                event.preventDefault();
                Backbone.history.navigate(App.profile.get('username') + '/stash/tiles', {
                    trigger: true
                });
            },
            'click ._listLink': function(event) {
                event.preventDefault();
                Backbone.history.navigate(App.profile.get('username') + '/stash/list', {
                    trigger: true
                });
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
            'click ._tilesLink': function(event) {
                event.preventDefault();
                Backbone.history.navigate(App.profile.get('username') + '/wishlist/tiles', {
                    trigger: true
                });
            },
            'click ._listLink': function(event) {
                event.preventDefault();
                Backbone.history.navigate(App.profile.get('username') + '/wishlist/list', {
                    trigger: true
                });
            }

        },
        regions: {
            wishlist: '._wishlist'
        }
    });

    function renderStashTiles(stashLayout) {
        if (App.collectibles.mode !== 'infinite') {
            App.collectibles.switchMode('infinite');
        }

        var stashView = new StashView({
            collection: App.collectibles,
            permissions: App.permissions
        });

        stashView.on('stash:remove', function(id) {
            var model = App.collectibles.fullCollection.get(id);
            App.layout.modal.show(new StashRemoveView({
                model: model,
                reasons: App.reasonsCollection
            }));

            model.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });

        stashView.on('stash:sell', function(id) {
            var model = App.collectibles.get(id);
            App.layout.modal.show(new StashSellView({
                model: model
            }));

            model.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });

        stashView.on('stash:add:photo', function(id) {
            var model = App.collectibles.get(id);
            App.layout.modal.show(new StashUploadView({
                model: model,
                profile: App.profile,
                permissions: App.permissions
            }));

            model.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });

        stashLayout.stash.show(stashView);
    }

    function renderStashList(stashLayout) {
        if (App.collectibles.mode !== 'server') {
            App.collectibles.switchMode('server');
        }
        var stashView = new StashTableView({
            collection: App.collectibles,
            permissions: App.permissions
        });

        stashView.on('stash:remove', function(id) {
            var model = App.collectibles.get(id);
            App.layout.modal.show(new StashRemoveView({
                model: model,
                reasons: App.reasonsCollection
            }));

            model.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });

        stashView.on('stash:sell', function(id) {
            var model = App.collectibles.get(id);
            App.layout.modal.show(new StashSellView({
                model: model
            }));

            model.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });

        stashLayout.stash.show(stashView);
    }

    function renderWishlistTiles(wishlistLayout) {
        if (App.wishlist.mode !== 'infinite') {
            App.wishlist.switchMode('infinite');
        }

        var stashView = new WishlistView({
            collection: App.wishlist,
            permissions: App.permissions
        });

        stashView.on('stash:add', function(id) {

            // upon success, we need to kick off a delete from wish list

            // create a new model here
            var wishListCollectible = App.wishlist.fullCollection.get(id);
            var model = new CollectibleUser(wishListCollectible.toJSON());
            model.unset('id');
            model.collectible = wishListCollectible.collectible;

            App.layout.modal.show(new StashAddView({
                model: model
            }));
            // once this has been saved
            // close the modal and then trigger a delete
            // on the wishlist 
            model.once('sync', function() {
                wishListCollectible.destroy();
                App.layout.modal.hideModal();
            });
        });

        wishlistLayout.wishlist.show(stashView);
    }

    function renderWishlistList(wishlistLayout) {
        if (App.wishlist.mode !== 'server') {
            App.wishlist.switchMode('server');
        }
        var stashView = new WishlistTableView({
            collection: App.wishlist,
            permissions: App.permissions
        });

        stashView.on('stash:add', function(id) {

            // upon success, we need to kick off a delete from wish list

            // create a new model here
            var wishListCollectible = App.wishlist.get(id);
            var model = new CollectibleUser(wishListCollectible.toJSON());
            model.unset('id');
            model.collectible = wishListCollectible.collectible;

            App.layout.modal.show(new StashAddView({
                model: model
            }));
            // once this has been saved
            // close the modal and then trigger a delete
            // on the wishlist 
            model.once('sync', function() {
                wishListCollectible.destroy();
                App.layout.modal.hideModal();
            });
        });

        wishlistLayout.wishlist.show(stashView);
    }

    function renderStash(view) {
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
        if (view === 'tiles') {
            renderStashTiles(stashLayout);
        } else if (view === 'list') {
            renderStashList(stashLayout);
        }

    }

    function renderWishlist(view) {
        var layout = new WishlistLayout({
            permissions: App.permissions,
            model: App.profile
        });

        App.layout.main.show(layout);
        if (view === 'tiles') {
            renderWishlistTiles(layout);
        } else if (view === 'list') {
            renderWishlistList(layout);
        }
    }

    function renderPhotos(view) {
        var layout = new PhotosLayout({
            permissions: App.permissions,
            model: App.profile
        });

        App.layout.main.show(layout);

        var view = new PhotosView({
            collection: App.photos,
            permissions: App.permissions
        });

        layout.photos.show(view);
    }

    function renderHistory(layout) {
        var view = new HistoryView({
            collection: App.history,
            permissions: App.permissions
        });

        layout.history.show(view);
    }

    function renderSubmissions(layout) {
        var view = new SubmissionsView({
            collection: App.submissions,
            permissions: App.permissions
        });

        layout.submissions.show(view);
    }

    function renderEdits(layout) {
        var view = new EditsView({
            collection: App.edits,
            permissions: App.permissions
        });

        layout.edits.show(view);
    }

    function renderSale(layout) {
        var view = new SaleView({
            collection: App.sales,
            permissions: App.permissions
        });

        view.on('stash:mark:sold', function(id) {
            var model = App.sales.get(id);

            App.layout.modal.show(new StashRemoveView({
                model: model,
                reasons: App.reasonsCollection,
                changeReason: false,
                removeReasonId: model.get('Listing').collectible_user_remove_reason_id
            }));

            model.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });


        view.on('stash:listing:edit', function(id) {
            var model = App.sales.get(id);

            App.layout.modal.show(new StashListingEditView({
                model: model.listing,
                collectible: model.collectible
            }));

            model.listing.once('sync', function() {
                App.layout.modal.hideModal();
            });
        });

        view.on('stash:remove', function(id) {
            var model = App.sales.get(id);

            model.save({
                sale: false
            }, {
                wait: true,
                success: function(model, response, options) {
                    growl.onSuccess('The collectible has been removed from your sale/trade list!');

                    App.sales.remove(model);
                },
                error: function(model, xhr, options) {
                    var errorMessage = 'Oops! Something went terribly wrong!';
                    if (xhr.status === 400) {
                        $.each(xhr.responseJSON.response.errors, function(index, value) {
                            errorMessage = value.message;
                        });
                    } else if (xhr.status === 401) {
                        errorMessage = 'You are not authorized to do that!';
                    }

                    growl.onErrpr(errorMessage);
                }
            });
        });

        layout.sales.show(view);
    }

    function renderHistoryChart(layout) {
        var chartView = new HistoryChartView({
            model: App.histroyGraph
        });

        layout.chart.show(chartView);
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
        },
        index: function() {
            renderHeader('profile');
            var profileLayout = new ProfileLayout();
            App.layout.main.show(profileLayout);

            var commentsView = new CommentsView({
                collection: App.comments,
                permissions: App.permissions
            });

            commentsView.on('comment:add', function(id) {
                var model = new App.comments.model();
                model.set('entity_type_id', App.profile.get('entity_type_id'));

                // set the last comment created so that this will return any comments
                // created in the mean time
                if (!App.comments.isEmpty()) {
                    model.set('last_comment_created', App.comments.last().get('created'));
                }

                App.layout.modal.show(new CommentAddView({
                    model: model
                }));

                model.once('sync', function(model, response, options) {
                    if (_.isArray(response)) {
                        App.comments.add(response);
                    }

                    App.layout.modal.hideModal();
                });
            });


            commentsView.on('comment:edit', function(id) {
                var model = App.comments.get(id);

                App.layout.modal.show(new CommentAddView({
                    model: model
                }));

                model.once('sync', function(model, response, options) {
                    // this gets called before tracking is finished updating 
                    App.layout.modal.hideModal();
                });
            });

            profileLayout.comments.show(commentsView);

            if (App.permissions.get('show_stash_facts')) {
                profileLayout.stashFacts.show(new StashFactsView({
                    model: App.stashFacts
                }));
            }

            profileLayout.activity.show(new ActivitiesView({
                collection: App.activity
            }));

            profileLayout.work.show(new WorkView({
                collection: App.work,
                permissions: App.permissions
            }));

            profileLayout.facts.show(new FactsView({
                model: App.facts
            }));

        },
        stash: function() {
            renderHeader('stash');

            App.layout.main.show(new LoaderView());

            // This is kind of dumb but a decent fix for #113.
            // if the size of the collection is less than 25 
            // when you go from the list to the tiles, backone.paginator
            // is triggering a reset event, before finishing so
            // it is re-rendering the table list and that is blowing up
            // because it doesnt have pagination

            // SO create a new collection here, since we were resetting and
            // reloading anyway this will stop events from being triggered
            // on the current view before it gets blown away.

            // Another solution would be to destroy the current
            // view and show a loading view
            App.collectibles = new CollectiblesCollection([], {
                username: App.profile.get('username')
            });

            // if (App.collectibles.mode !== 'infinite') {
            //     App.collectibles.switchMode('infinite').done(function() {
            //         renderStash('tiles');
            //     });
            // } else {
            //     // for now we want to reset everytime we come to this page
            //     // in case data has changed.  This is the best way I have found
            //     // to handle that for now
            //     App.collectibles.reset();
            //     App.collectibles.fullCollection.reset();
            App.collectibles.getFirstPage({
                silent: true
            }).done(function() {
                renderStash('tiles');
            });
            // }
        },
        stashList: function() {
            renderHeader('stash');
            App.layout.main.show(new LoaderView());
            // see above for reasoning
            App.collectibles = new CollectiblesCollection([], {
                username: App.profile.get('username'),
                mode: 'server'
            });
            // if (App.collectibles.mode !== 'server') {
            //     App.collectibles.switchMode('server').done(function() {
            //         renderStash('list');
            //     });
            // } else if (App.collectibles.isEmpty()) {

            // pass in silent true here otherwise it will render twice
            App.collectibles.getFirstPage({
                silent: true
            }).done(function() {
                renderStash('list');
            });
            // } else {
            //     renderStash('list');
            // }
        },
        wishlist: function() {
            renderHeader('wishlist');
            App.layout.main.show(new LoaderView());
            // see above for reasoning
            App.wishlist = new WishlistCollection([], {
                username: App.profile.get('username')
            });
            App.wishlist.getFirstPage({
                silent: true
            }).done(function() {
                renderWishlist('tiles');
            });

            // if (App.wishlist.mode !== 'infinite') {
            //     App.wishlist.switchMode('infinite').done(function() {
            //         renderWishlist('tiles');
            //     });
            // } else if (App.wishlist.isEmpty()) {
            //     App.wishlist.getFirstPage().done(function() {
            //         renderWishlist('tiles');
            //     });
            // } else {
            //     renderWishlist('tiles');
            // }
        },
        wishlistList: function() {
            renderHeader('wishlist');
            App.layout.main.show(new LoaderView());
            // see above for reasoning
            App.wishlist = new WishlistCollection([], {
                username: App.profile.get('username'),
                mode: 'server'
            });
            App.wishlist.getFirstPage({
                silent: true
            }).done(function() {
                renderWishlist('list');
            });
            // if (App.wishlist.mode !== 'server') {
            //     App.wishlist.switchMode('server').done(function() {
            //         renderWishlist('list');
            //     });
            // } else if (App.wishlist.isEmpty()) {
            //     App.wishlist.getFirstPage().done(function() {
            //         renderWishlist('list');
            //     });
            // } else {
            //     renderWishlist('list');
            // }
        },
        sale: function() {
            renderHeader('sale');

            var layout = new SaleLayout({
                permissions: App.permissions,
                model: App.profile
            });
            App.layout.main.show(layout);
            layout.sales.show(new LoaderView());
            // for now we want to reset everytime we come to this page
            // in case data has changed.  This is the best way I have found
            // to handle that for now
            App.sales.reset();
            App.sales.fullCollection.reset();
            App.sales.getFirstPage({
                silent: true
            }).done(function() {
                renderSale(layout);
            });
        },
        photos: function() {
            renderHeader('photos');
            App.layout.main.show(new LoaderView());
            // just get first page again, for some reason
            // it renders the last page if you keep clicking more and then go away
            // if (App.photos.isEmpty()) {
                App.photos.getFirstPage({
                    silent: true
                }).done(function() {
                    renderPhotos();
                });
            // } else {
            //     renderPhotos();
            // }
        },
        history: function() {
            renderHeader('history');

            var layout = new HistoryLayout({
                permissions: App.permissions,
                model: App.profile
            });

            App.layout.main.show(layout);

            if (App.histroyGraph.isNew()) {
                // could bind to sync event but not sure we will
                // fetch this again
                App.histroyGraph.fetch({
                    success: function() {
                        renderHistoryChart(layout);
                    }
                });
            } else {
                renderHistoryChart(layout);
            }

            layout.history.show(new LoaderView());

            if (App.history.isEmpty()) {
                App.history.getFirstPage({
                    silent: true
                }).done(function() {
                    renderHistory(layout);
                });
            } else {
                renderHistory(layout);
            }
        },
        activity: function() {
            renderHeader('activity');

            var layout = new ActivityLayout({
                permissions: App.permissions,
                model: App.profile
            });
            App.layout.main.show(layout);

            if (App.submissions.isEmpty()) {
                App.submissions.getFirstPage({
                    silent: true
                }).done(function() {
                    renderSubmissions(layout);
                });
            } else {
                renderSubmissions(layout);
            }

            if (App.edits.isEmpty()) {
                App.edits.getFirstPage({
                    silent: true
                }).done(function() {
                    renderEdits(layout);
                });
            } else {
                renderEdits(layout);
            }
        }
    });
});