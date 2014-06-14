define(['require', 'backbone'], function(require, backbone) {
    return Backbone.Model.extend({
        urlRoot: '/comments/comment'
    });
});