define(['backbone', 'backbone.pageable'], function(Backbone, pageable) {
    return Backbone.PageableCollection.extend({
        mode: "client",
        state: {
            pageSize: 10,
            sortKey: "updated",
            order: 1,
            query: {}
        }
    });
});