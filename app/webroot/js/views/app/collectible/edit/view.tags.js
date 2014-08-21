define(['marionette', 'text!templates/app/collectible/edit/tags.mustache', 'views/app/collectible/edit/view.tag', 'views/app/collectible/edit/view.tag.add', 'mustache', 'underscore', 'marionette.mustache'], function(Marionette, template, TagView, AddTagView, mustache, _) {

    return Marionette.CompositeView.extend({
        template: template,
        className: "row",
        itemViewContainer: ".tags",
        // emptyView: NoItemsView,
        itemView: TagView,
        events: {
            'click .save': 'save',
        },
        initialize: function() {

        },
        _initialEvents: function() {
            // Bind only after composite view is rendered to avoid adding child views
            // to nonexistent itemViewContainer
            this.once('render', function() {
                if (this.collection) {
                    this.listenTo(this.collection, 'remove', this.render);
                    this.listenTo(this.collection, 'add', this.render);
                    this.listenTo(this.collection, "reset", this._renderChildren);
                }
            });
        },
        serializeData: function() {
            return {
                total: this.collection.length,
            };
        },
        onRender: function() {
            var self = this;
            if (this.collection.length < 5) {
                if (this.addTagView) {
                    this.addTagView.remove();
                }
                this.addTagView = new AddTagView({
                    collection: this.collection
                });
                $('.add-container', self.el).html(this.addTagView.render().el);
            }
            return this;
        },
        save: function() {
            this.collection.sync();
        }
    });
});