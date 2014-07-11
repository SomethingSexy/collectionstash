define(['backbone', 'backbone.pageable'], function(Backbone, pageable) {
    return Backbone.PageableCollection.extend({
        initialize: function(models, props) {
            this.url = "/collectibles/userHistory/" + props.username;
        },
        mode: "client",
        state: {
            pageSize: 10,
            sortKey: "updated",
            order: 1,
            query: {}
        }
    });
});