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
            'click ._edit-company': 'edit'
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            if (data.bio) {
                data.bio = data.bio.replace(/\\n/g, "\n");
            }
            if (this.model.photo) {
                data.photo = this.model.photo.toJSON();
            }
            data.uploadDirectory = uploadDirectory;
            data.permissions = this.permissions.toJSON();
            return data;
        },
        edit: function(event) {
            event.preventDefault();
            this.trigger('edit:company', this.model.get('id'));
        }
    });
});