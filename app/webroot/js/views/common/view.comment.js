define(['require', 'marionette', 'text!templates/app/common/comment.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        className: '',
        tagName : 'li',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        events: {
            'click ._edit': 'edit',
            'click ._remove': 'removeComment'
        },
        onRender: function() {
            // $('.stash-sell', this.el).attr('data-collectible-user-id', this.model.get('id'));
            // $('.remove-from-stash', this.el).attr('data-collectible-user-id', this.model.get('id'));
        },
        edit: function(event) {
            event.preventDefault();
            this.trigger('comment:edit', this.model.get('id'));
        },
        removeComment: function(event) {
            event.preventDefault();
            this.model.destroy();
        }
    });
});