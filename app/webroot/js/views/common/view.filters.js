define(['backbone', 'marionette', 'text!templates/app/common/filters.mustache', 'views/common/view.filter', 'mustache', 'marionette.mustache'], function(Backbone, Marionette, template, FilterView) {
    return Marionette.CompositeView.extend({
        template: template,
        itemView: FilterView,
        itemViewContainer: "#fancy-filters",
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