define(['backbone', 'backbone.pageable', 'models/model.collectible.user'], function(Backbone, pageable, CollectibleUserModel) {
    return Backbone.PageableCollection.extend({
        model: CollectibleUserModel,
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