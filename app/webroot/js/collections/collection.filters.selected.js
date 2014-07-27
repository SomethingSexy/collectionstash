define(['backbone'], function(Backbone) {
    return Backbone.Collection.extend({
        processFilter: function(type, values) {
            var filter = this.findWhere({
                type: type
            });

            if (filter) {
                filter.set('values', values);
            } else {
                this.add({
                    type: type,
                    values: values
                });
            }
        }
    });
});