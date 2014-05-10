define(['backbone', 'backbone.pageable'], function(Backbone) {
    return Backbone.PageableCollection.extend({
        initialize: function(models, props) {
            this.url = "/collectibles_users/collectibles/" + props.username;
        },
        mode: "infinite",
        state: {
            pageSize: 25,
            sortKey: "updated",
            order: 1
        }
    });
});