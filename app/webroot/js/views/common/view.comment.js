define(['require', 'marionette', 'text!templates/app/common/comment.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        className: '',
        tagName : 'li',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        events: {
            'click .stash-sell': 'sell',
            'click .remove-from-stash': 'removeFromStash'
        },
        // serializeData: function() {
        //     var data = {};
        //     data = this.model.toJSON();
        //     data.Collectible = this.model.collectible.toJSON();
        //     data['permissions'] = this.permissions.toJSON();
        //     return data;
        // },
        onRender: function() {
            // $('.stash-sell', this.el).attr('data-collectible-user-id', this.model.get('id'));
            // $('.remove-from-stash', this.el).attr('data-collectible-user-id', this.model.get('id'));
        },
        sell: function(event) {
            event.preventDefault();
            this.trigger('stash:sell', this.model.get('id'));
        },
        removeFromStash: function(event) {
            event.preventDefault();
            this.trigger('stash:remove', this.model.get('id'));
        }
    });
});