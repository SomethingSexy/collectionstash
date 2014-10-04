define(['backbone'], function(Backbone) {
    return Backbone.Model.extend({
        url: function() {
            return '/attributes_uploads/upload'
        }
    });
});