define(['backbone', 'backbone.pageable', ], function(Backbone, pageable) {
    return Backbone.PageableCollection.extend({
        url: '/activities',
        mode: "infinite",
        state: {
            pageSize: 25,
        }
    });
});