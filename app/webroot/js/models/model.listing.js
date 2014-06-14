define(['require', 'backbone'], function(require, backbone) {
    return Backbone.Model.extend({
        url: function() {
            return '/listings/listing/' + this.id;
        }
    });
});