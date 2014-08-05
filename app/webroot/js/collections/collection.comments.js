define(['backbone', 'models/model.comment'], function(Backbone, CommentModel) {
    return Backbone.Collection.extend({
        _sortKey: 'desc',
        model: CommentModel,
        url: '/comments/comments',
        comparator: function(chapter) {
            if (this._sortKey === 'desc') {
                return chapter.get("sort_created");
            } else {
                return -chapter.get("sort_created");
            }
        }
    });
});