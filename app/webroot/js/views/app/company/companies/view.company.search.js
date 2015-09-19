define(function(require, Marionette, template, mustache) {
    var Marionette = require('marionette'),
        template = require('text!templates/app/company/companies/company.search.mustache'),
        mustache = require('mustache');
    require('marionette.mustache');
    return Marionette.ItemView.extend({
        template: template,
        modelEvents: {
            "change": "render"
        },
        events: {
            'keyup ._companySearch': 'search'
        },
        search: function(event) {
            var searchTxt = event.currentTarget.value;

            this.trigger('search', searchTxt);
        }
    });
});