define(['require', 'marionette', 'text!templates/app/user/profile/stash.collectible.mustache', 'mustache', 'models/model.collectible.user', 'marionette.mustache'], function(require, Marionette, template, mustache, CollectibleUserModel) {

    return Marionette.ItemView.extend({
        className: 'tile stash-item col-xs-6 col-md-3',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        events: {
            'click .stash-sell': 'sell',
            'click .remove-from-stash': 'removeFromStash'
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
        sell: function(event) {
            var $anchor = $(event.currentTarget);

            var collectibleUserId = $anchor.attr('data-collectible-user-id');
            this.trigger('stash:sell', collectibleUserId);

            event.preventDefault();
        },
        removeFromStash: function(event) {
            var $anchor = $(event.currentTarget);
            var collectibleUserId = $anchor.attr('data-collectible-user-id');
            this.trigger('stash:remove', collectibleUserId);

            event.preventDefault();
        }
    });
});