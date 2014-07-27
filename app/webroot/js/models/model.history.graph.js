define(['require', 'backbone', 'underscore', 'backbone.trackit'], function(require, backbone, _) {
    return Backbone.Model.extend({
        initialize: function(models, props) {
            this.url = "/collectibles_users/historyData/" + props.username;
        }
    });
});