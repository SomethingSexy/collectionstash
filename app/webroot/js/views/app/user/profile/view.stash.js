define(['require', 'marionette', 'text!templates/app/user/profile/stash.mustache', 'text!templates/app/user/profile/stash.empty.mustache', 'views/app/user/profile/view.stash.collectible', 'mustache', 'imagesloaded', 'wookmark',
    'marionette.mustache', 'jquery.blueimp-gallery', 'bootstrap'
], function(require, Marionette, template, emptyTemplate, CollectibleView, mustache, Masonry, imagesloaded) {

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
            "stash:sell": function(event, view, id) {
                this.trigger('stash:sell', id);
            }
        },
        events: {
            'click ._more': 'next'
        },
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.renderMore);
        },
        serializeData: function() {
            var data = {
                showMore: this.collection.hasNextPage()
            };
            return data;
        },
        onShow: function() {
            var self = this;
            this.wookmark();
        },
        next: function(event) {
            $('._more', this.el).button('loading');
            this.collection.getNextPage();
        },
        onItemRemoved: function() {
            this.wookmark();
        },
        renderMore: function() {
            var self = this;
            var ItemView;
            $('._more', this.el).button('reset');

            if (this.collection.state.currentPage === 1) {
                $(this.itemViewContainer, this.el).empty();
            }
            this.startBuffering();
            this.collection.each(function(item, index) {
                ItemView = this.getItemView(item);
                this.addItemView(item, ItemView, index);
            }, this);
            this.endBuffering();

            this.wookmark();
            if (!this.collection.hasNextPage() || this.collection.state.currentPage >= this.collection.state.lastPage) {
                $('._more', this.el).hide();
            } else {
                $('._more', this.el).show();
            }
        },
        wookmark: function() {
            var self = this;
            $('._tiles', this.el).imagesLoaded(function() {
                if (self.handler && self.handler.wookmarkInstance) {
                    self.handler.wookmarkInstance.clear();
                }
                self.handler = $('._tiles .tile', this.el);
                // Call the layout function.
                self.handler.wookmark({
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: $('._tiles', self.el),
                    verticalOffset: 20,
                    align: 'left',
                    offset: 20
                });
                // Update the layout.
                self.handler.wookmark();
            });
        }
    });
});