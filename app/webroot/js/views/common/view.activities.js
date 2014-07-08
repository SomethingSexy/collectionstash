define(['marionette', 'text!templates/app/common/activities.mustache', 'views/common/view.activity', 'text!templates/app/common/activities.empty.mustache', 'mustache', 'marionette.mustache'], function(Marionette, template, ActivityView, emptyTemplate) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: ActivityView,
        itemViewContainer: "._activities",
        emptyView: NoItemsView,
        itemEvents: {
            "filter:selected": function(event, view, type, values) {
                this.trigger('filter:selected', type, values);
            }
        },
        onRender: function() {


            return this;
        }
    });
});