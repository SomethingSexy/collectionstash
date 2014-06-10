define(['backbone', 'models/model.comment'], function(Backbone, CommentModel) {
    return Backbone.Collection.extend({
        model: CommentModel,
        url : '/comments/comments'
    });
});