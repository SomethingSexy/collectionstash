define(['require', 'marionette', 'text!templates/app/user/profile/work.mustache', 'text!templates/app/user/profile/work.empty.mustache', 'views/app/user/profile/view.work.row', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, template, emptyTemplate, WorkView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate,
        tagName: 'tr'
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemViewContainer: "tbody",
        emptyView: NoItemsView,
        itemView: WorkView,
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.render);
            this.listenTo(this.collection, "sync", this.renderMore);
        },
        initialize: function() {

        }
    });
});