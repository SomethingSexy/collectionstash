define(['backbone', 'backbone.pageable'], function(Backbone) {
    return Backbone.PageableCollection.extend({
        url: "/collectibles_users/collectibles/"
    });
});