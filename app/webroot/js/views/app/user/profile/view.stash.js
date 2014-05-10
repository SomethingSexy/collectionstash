define(['require', 'marionette', 'text!templates/app/user/profile/stash.mustache', 'views/app/user/profile/view.stash.collectible', 'mustache', 'imagesloaded', 'wookmark',
    'marionette.mustache'
], function(require, Marionette, template, CollectibleView, mustache, Masonry) {

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CollectibleView,
        itemViewContainer: "._tiles",
        events: {
            'click ._more': 'next'
        },
        initialize: function() {
            this.listenTo(this.collection, "reset", this.renderMore);
        },
        _initialEvents: function() {

        },
        onRender: function() {
            this.handler = $('._tiles .tile', this.el);
        },
        next: function(event) {
            this.collection.getNextPage();
        },
        renderMore: function() {
            var self = this;
            var ItemView;
            this.collection.each(function(item, index) {
                ItemView = this.getItemView(item);
                this.addItemView(item, ItemView, index);
            }, this);
            $('._tiles', this.el).imagesLoaded(function() {
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
            });
        },
        onCompositeCollectionRendered: function() {
            var self = this;
            $('._tiles', this.el).imagesLoaded(function() {
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
            });
        }
    });
});