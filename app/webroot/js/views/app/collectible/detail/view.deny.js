define(function(require) {

    var Marionette = require('marionette'),
        template = require('text!templates/app/collectible/detail/deny.mustache'),
        mustache = require('mustache');
    require('marionette.mustache');

    return Marionette.ItemView.extend({
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        modelEvents: {
            "change": "render"
        },
        events: {
            'click ._edit-company': 'edit'
        },
        edit: function(event) {
            event.preventDefault();
            this.trigger('edit:company', this.model.get('id'));
        }
    });
});