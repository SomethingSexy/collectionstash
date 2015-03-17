define(function(require, Marionette, template, mustache) {

    var Marionette = require('marionette'),
        template = require('text!templates/app/collectible/detail/approve.mustache'),
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
            'click .save': 'approve'
        },
        approve: function(event) {
            event.preventDefault();
            this.trigger('edit:company', this.model.get('id'));
        }
    });
});