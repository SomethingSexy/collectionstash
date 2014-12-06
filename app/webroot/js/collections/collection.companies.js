define(['backbone', 'backbone.pageable', 'models/model.collectible'], function(Backbone, pageable, CollectibleModel) {
    return Backbone.PageableCollection.extend({
        model: CollectibleModel,
        initialize: function(models, props) {
            this.url = "/manufacturers/manufacturers/";
        },
        mode: "client",
        state: {
            pageSize: 25,
            query: {}
        }
    });
});