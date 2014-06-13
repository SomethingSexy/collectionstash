define(['require', 'marionette', 'text!templates/app/common/comments.mustache', 'views/common/view.comment', 'mustache',
    'marionette.mustache'
], function(require, Marionette, template, CommentView, mustache) {

    // var NoItemsView = Backbone.Marionette.ItemView.extend({
    //     template: emptyTemplate
    // });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CommentView,
        itemViewContainer: "ol._comments",
        // emptyView: NoItemsView,
        // itemViewOptions: function(model, index) {
        //     return {
        //         permissions: this.permissions
        //     };
        // },
        // itemEvents: {
        //     "stash:remove": function(event, view, id) {
        //         this.trigger('stash:remove', id);
        //     },
        //     "stash:sell": function(event, view, id) {
        //         this.trigger('stash:sell', id);
        //     }
        // },
        events : {
            'click ._addcomment' : 'addComment'
        },
        addComment: function(event) {
            this.trigger('comment:add');
            event.preventDefault();
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.renderMore);
            this.listenTo(this.collection, "add", this.addChildView);
        },
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        serializeData: function() {
            var data = {};
            data['permissions'] = this.permissions.toJSON();
            return data;
        },
        onShow: function() {
            // var self = this;
            // this.handler = $('._tiles .tile', this.el);
            // // $('._tiles', this.el).imagesLoaded(function() {
            // if (self.handler.wookmarkInstance) {
            //     self.handler.wookmarkInstance.clear();
            // }
            // // Call the layout function.
            // self.handler.wookmark({
            //     autoResize: true, // This will auto-update the layout when the browser window is resized.
            //     container: $('._tiles', self.el),
            //     verticalOffset: 20,
            //     align: 'left'
            // });
            // // Update the layout.
            // self.handler.wookmark();
        }
    });
});