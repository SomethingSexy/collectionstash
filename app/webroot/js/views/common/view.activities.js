define(['marionette', 'text!templates/app/common/activities.mustache', 'views/common/view.activity', 'mustache', 'marionette.mustache'], function(Marionette, template, ActivityView) {

    return Marionette.CompositeView.extend({
        template: template,
        itemView: ActivityView,
        itemViewContainer: "._activities",
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