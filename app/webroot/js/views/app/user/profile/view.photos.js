define(['require', 'marionette', 'text!templates/app/user/profile/photos.mustache', 'text!templates/app/user/profile/photos.empty.mustache', 'views/app/user/profile/view.photo', 'mustache', 'imagesloaded', 'wookmark',
    'marionette.mustache'
], function(require, Marionette, template, emptyTemplate, PhotoView, mustache, Masonry) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: PhotoView,
        itemViewContainer: "._uploads",
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
        next: function(event) {
            this.collection.getNextPage();
        },
        renderMore: function() {
            var self = this;
            var ItemView;
            if (this.collection.state.currentPage === 1) {
                $(this.itemViewContainer, this.el).empty();
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
            // });
            
            if(!this.collection.hasNextPage()){
                $('._more', this.el).hide();
            } else {
                 $('._more', this.el).show();
            }
        }
    });
});