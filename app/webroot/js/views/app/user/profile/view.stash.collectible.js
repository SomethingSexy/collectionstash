define(['require', 'marionette', 'text!templates/app/user/profile/stash.collectible.mustache', 'mustache', 'models/model.collectible.user', 'marionette.mustache', 'stash.tools'], function(require, Marionette, template, mustache, CollectibleUserModel) {

    return Marionette.ItemView.extend({
        className: 'tile stash-item col-xs-6 col-md-3',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        events: {
            'click .stash-sell': 'sell'
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            data['permissions'] = this.permissions.toJSON();
            return data;
        },
        onRender: function() {
            $('.stash-sell', this.el).attr('data-collectible-user', JSON.stringify(this.model.get('CollectiblesUser'))).attr('data-collectible', JSON.stringify(this.model.get('Collectible'))).attr('data-collectible-user-id', this.model.get('CollectiblesUser').id);
            $('.remove-from-stash', this.el).attr('data-collectible-user', JSON.stringify(this.model.get('CollectiblesUser'))).attr('data-collectible', JSON.stringify(this.model.get('Collectible'))).attr('data-collectible-user-id', this.model.get('CollectiblesUser').id);

        },
        sell: function(event) {
            var $anchor = $(event.currentTarget);

            var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
            var collectibleUserData = JSON.parse($anchor.attr('data-collectible-user'));

            var collectibleUserModel = new CollectibleUserModel(collectibleUserData);

            var $stashItem = $anchor.closest('.stash-item');

            $anchor.stashsell(collectibleModel, collectibleUserModel, {
                $stashItem: $stashItem
            });
            event.preventDefault();
        }
    });
});