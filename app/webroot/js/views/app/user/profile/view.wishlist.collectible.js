define(['require', 'marionette', 'text!templates/app/user/profile/wishlist.collectible.mustache', 'mustache', 'models/model.collectible.wishlist', 'marionette.mustache'], function(require, Marionette, template, mustache, CollectibleUserModel) {

    return Marionette.ItemView.extend({
        className: 'tile stash-item',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        events: {
            'click .add-full-to-stash': 'addToStash',
            'click .remove-from-wishlist': 'removeFromWishlist'
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
        addToStash: function(event) {
            this.trigger('stash:add', this.model.get('id'));
            event.preventDefault();
        },
        removeFromWishlist: function(event) {
            event.preventDefault();
            this.model.destroy();
        }
    });
});