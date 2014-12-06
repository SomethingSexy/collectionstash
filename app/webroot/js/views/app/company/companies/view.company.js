define(function(require, Marionette, template, mustache) {

    var Marionette = require('marionette'),
        template = require('text!templates/app/company/companies/company.mustache'),
        mustache = require('mustache');
    require('marionette.mustache');

    return Marionette.ItemView.extend({
        className: '',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
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
            // data.Listing = this.model.listing.toJSON();
            // data.Collectible = this.model.collectible.toJSON();
            // data['permissions'] = this.permissions.toJSON();
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