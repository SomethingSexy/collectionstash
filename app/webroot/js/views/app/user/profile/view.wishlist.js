define(['require', 'marionette', 'text!templates/app/user/profile/wishlist.mustache', 'text!templates/app/user/profile/wishlist.empty.mustache', 'views/app/user/profile/view.wishlist.collectible', 'mustache', 'imagesloaded', 'wookmark',
    'marionette.mustache', 'jquery.blueimp-gallery', 'bootstrap'
], function(require, Marionette, template, emptyTemplate, CollectibleView, mustache, Masonry) {


    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CollectibleView,
        itemViewContainer: "._tiles",
        emptyView: NoItemsView,
        itemViewOptions: function(model, index) {
            return {
                permissions: this.permissions
            };
        },
        itemEvents: {
            "stash:remove": function(event, view, id) {
                this.trigger('stash:remove', id);
            },
            "stash:add": function(event, view, id) {
                this.trigger('stash:add', id);
            }
        },
        events: {
            'click ._more': 'next'
        },
        initialize: function(options) {
            this.listenTo(this.collection, "reset", this.renderMore);
            this.permissions = options.permissions;
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
        },
        serializeData: function() {
            var data = {
                showMore: this.collection.hasNextPage()
            };
            return data;
        },
        onShow: function() {
            var self = this;
            this.handler = $('._tiles .tile', this.el);
            // $('._tiles', this.el).imagesLoaded(function() {
            if (self.handler.wookmarkInstance) {
                self.handler.wookmarkInstance.clear();
            }
            // Call the layout function.
            self.handler.wookmark({
                autoResize: true, // This will auto-update the layout when the browser window is resized.
                container: $('._tiles', self.el),
                verticalOffset: 20,
                align: 'left'
            });
            // Update the layout.
            self.handler.wookmark();
        },
        onItemRemoved: function() {
            if (this.handler.wookmarkInstance) {
                this.handler.wookmarkInstance.clear();
            }
            this.handler = $('._tiles .tile', this.el);
            // Call the layout function.
            this.handler.wookmark({
                autoResize: true, // This will auto-update the layout when the browser window is resized.
                container: $('._tiles', this.el),
                verticalOffset: 20,
                align: 'left'
            });
            // Update the layout.
            this.handler.wookmark();
        },
        next: function(event) {
            this.collection.getNextPage();
        },
        renderMore: function() {
            var self = this;
            var ItemView;
            if (this.collection.state.currentPage === 1) {
                $('._tiles', this.el).empty();
            }
            this.startBuffering();
            this.collection.each(function(item, index) {
                ItemView = this.getItemView(item);
                this.addItemView(item, ItemView, index);
            }, this);
            this.endBuffering();
            // $('._tiles', this.el).imagesLoaded(function() {
            if (self.handler.wookmarkInstance) {
                self.handler.wookmarkInstance.clear();
            }
            self.handler = $('._tiles .tile', this.el);
            // Call the layout function.
            self.handler.wookmark({
                autoResize: true, // This will auto-update the layout when the browser window is resized.
                container: $('._tiles', self.el),
                verticalOffset: 20,
                align: 'left'
            });
            // Update the layout.
            self.handler.wookmark();
            // })
            if (!this.collection.hasNextPage() || this.collection.state.currentPage >= this.collection.state.lastPage) {
                $('._more', this.el).hide();
            } else {
                $('._more', this.el).show();
            }
        }
    });
});