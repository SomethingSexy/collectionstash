define(['require', 'backbone'], function(require, backbone) {
    return Backbone.Model.extend({
        url: '/comments/comment'
    });
});