define(['backbone', 'backbone.pageable'], function(Backbone, pageable) {
    return Backbone.PageableCollection.extend({
        model: Backbone.Model,
        queryParams: {
            query: function() {
                return this.state.query;
            },
            "sortKey": "sort",
            "order": "direction"
        },
        initialize: function(models, props) {
            this.url = "/collectibles/userHistory/" + props.username;
        }
    });
});