define(['require', 'backbone'], function(require, backbone) {
    return Backbone.Model.extend({
        url: function() {
            return '/user_uploads/upload'
        }
    });
});