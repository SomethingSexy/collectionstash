define(['backbone', 'backbone.pageable', 'models/model.collectible.user'], function(Backbone, pageable, CollectibleUserModel) {
    return Backbone.PageableCollection.extend({
        model: CollectibleUserModel,
        initialize: function(models, props) {
            this.url = "/collectibles_users/sale/" + props.username;
        },
        mode: "infinite",
        state: {
            pageSize: 25,
            sortKey: "updated",
            order: 1,
            query: {}
        },
        queryParams: {
            query: function() {
                return this.state.query;
            },
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