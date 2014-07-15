define(['require', 'marionette', 'text!templates/app/user/profile/submissions.mustache', 'text!templates/app/user/profile/submission.empty.mustache', 'views/app/user/profile/view.submission', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, template, emptyTemplate, WorkView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate,
        tagName: 'tr'
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemViewContainer: "tbody",
        emptyView: NoItemsView,
        itemView: WorkView,
        itemViewOptions: function(model, index) {
            return {
                permissions: this.permissions
            };
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.render);
            this.listenTo(this.collection, "sync", this.renderMore);
        },
        initialize: function(options) {
        	this.permissions = options.permissions;
        }
    });
});