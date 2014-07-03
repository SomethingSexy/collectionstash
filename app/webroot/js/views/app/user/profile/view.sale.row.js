define(['require', 'marionette', 'text!templates/app/user/profile/sale.collectible.row.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        className: 'stash-item',
        template: template,
        tagName: 'tr',
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        events: {
            'click .stash-remove-listing': 'removeListing',
            'click .stash-mark-as-sold': 'removeFromStash'
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            data['permissions'] = this.permissions.toJSON();
            return data;
        },
        onRender: function() {
            // $('.stash-sell', this.el).attr('data-collectible-user-id', this.model.get('id'));
            // $('.remove-from-stash', this.el).attr('data-collectible-user-id', this.model.get('id'));
        },
        removeListing: function(event) {
            event.preventDefault();
            this.trigger('stash:remove', this.model.get('id'));
        },
        removeFromStash: function(event) {
            event.preventDefault();
            this.trigger('stash:mark:sold', this.model.get('id'));
        }
    });
});