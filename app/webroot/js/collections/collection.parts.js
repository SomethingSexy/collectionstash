define(['backbone', 'backbone.pageable'], function(Backbone, pageable) {
    return Backbone.PageableCollection.extend({
        initialize: function(models, props) {
            this.url = "/attributes/index/";
        },
        mode: "infinite",
        state: {
            pageSize: 25,
            query: {}
        },
        queryParams: {
            query: function() {
                return this.state.query;
            },
            "sortKey": "sort",
            "pageSize" : "limit"
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