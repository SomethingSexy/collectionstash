define(['require', 'marionette', 'text!templates/app/user/profile/sale.collectible.row.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        className: 'stash-item',
        template: template,
        tagName: 'tr',
        initialize: function(options) {
            this.permissions = options.permissions;
            this.listenTo(this.model.listing, 'change', this.render);
        },
        modelEvents: {
            "change": "render"
        },
        events: {
            'click .stash-remove-listing': 'removeListing',
            'click .stash-mark-as-sold': 'removeFromStash',
            'click .stash-edit-listing': 'editListing'
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            data.Listing = this.model.listing.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            data['permissions'] = this.permissions.toJSON();
            return data;
        },
        removeListing: function(event) {
            event.preventDefault();
            this.trigger('stash:remove', this.model.get('id'));
        },
        removeFromStash: function(event) {
            event.preventDefault();
            this.trigger('stash:mark:sold', this.model.get('id'));
        },
        editListing: function(event) {
            event.preventDefault();
            this.trigger('stash:listing:edit', this.model.get('id'));
        }
    });
});