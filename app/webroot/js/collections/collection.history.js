define(['backbone', 'backbone.pageable', 'models/model.collectible.user'], function(Backbone, pageable, CollectibleUserModel) {
    return Backbone.PageableCollection.extend({
        model: CollectibleUserModel,
        initialize: function(models, props) {
            this.url = "/collectibles_users/history/" + props.username;
        },
        mode: "server",
        state: {
            pageSize: 25,
            query: {}
        },
        queryParams: {
            query: function() {
                return this.state.query;
            },
            "sortKey": "sort",
            "order": "direction"
        },
        setQuery: function(query, page_size) {
            var state = this.state;
            if (query != state.query) {
                state = _.clone(this._initState)
                //state.pageSize = page_size;
            }
            state = this.state = this._checkState(_.extend({}, state, {
                query: query,
            }));

        }
    });
});