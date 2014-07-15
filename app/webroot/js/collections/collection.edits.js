define(['backbone', 'backbone.pageable'], function(Backbone, pageable) {
     return Backbone.PageableCollection.extend({
        model: Backbone.Model,
        initialize: function(models, props) {
            this.url = "/edits/userHistory/" + props.username;
        }
    });
});