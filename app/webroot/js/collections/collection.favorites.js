define(function(require) {
    var Backbone = require('backbone'),
        pageable = require('backbone.pageable'),
        PhotoModel = require('models/model.favorite');

    return Backbone.PageableCollection.extend({
        model: PhotoModel,
        initialize: function(models, props) {
            this.url = "/favorites/index/" + props.username;
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