define(['require', 'marionette', 'text!templates/app/common/comments.mustache', 'text!templates/app/common/comments.empty.mustache', 'views/common/view.comment', 'mustache',
    'marionette.mustache'
], function(require, Marionette, template, emptyTemplate, CommentView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });


    return Marionette.CompositeView.extend({
        template: template,
        itemView: CommentView,
        itemViewContainer: "ol._comments",
        emptyView: NoItemsView,
        itemEvents: {
            "comment:edit": function(event, view, id) {
                this.trigger('comment:edit', id);
            }
        },
        events: {
            'click ._addcomment': 'addComment'
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
        }
    });
});