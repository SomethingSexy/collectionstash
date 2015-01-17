define(['require', 'marionette', 'text!templates/app/user/profile/stash.collectible.mustache', 'mustache', 'models/model.collectible.user', 'marionette.mustache'], function(require, Marionette, template, mustache, CollectibleUserModel) {

    return Marionette.ItemView.extend({
        className: 'tile stash-item',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        modelEvents: {
            "change": "render",
            'change:userUpload' : 'render'
        },
        events: {
            'click .stash-sell': 'sell',
            'click .remove-from-stash': 'removeFromStash',
            'click ._add-photo': 'addPhoto'
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            if (this.model.userUpload) {
                data.UserUpload = this.model.userUpload.toJSON();
            }
            data['permissions'] = this.permissions.toJSON();
            return data;
        },
        onRender: function() {

        },
        sell: function(event) {
            event.preventDefault();
            this.trigger('stash:sell', this.model.get('id'));
        },
        removeFromStash: function(event) {
            event.preventDefault();
            this.trigger('stash:remove', this.model.get('id'));
        },
        addPhoto: function(event) {
            event.preventDefault();
            this.trigger('stash:add:photo', this.model.get('id'));
        }
    });
});