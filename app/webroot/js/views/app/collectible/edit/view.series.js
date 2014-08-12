define(['backbone'], function(Backbone) {
    var SeriesView = Backbone.View.extend({
        events: {
            'click span.item': 'selectSeries'
        },
        initialize: function(options) {
        },
        render: function() {
            var self = this;
            $(self.el).html(this.model.toJSON().response.data);
            return this;
        },
        selectSeries: function(event) {
            var name = $(event.currentTarget).attr('data-path');
            var id = $(event.currentTarget).attr('data-id');
            this.trigger('series:select', id, name);
        }
    });

    return SeriesView;
});