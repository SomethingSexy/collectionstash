define(function(require) {

    var App = require('app/app.companies'),
        Backbone = require('backbone'),
        Marionette = require('marionette'),
        layout = require('text!templates/app/home/layout.mustache'),
        CompaniesView = require('views/app/company/companies/view.companies'),
        mustache = require('mustache');
    require('marionette.mustache');

    var CompaniesLayout = Backbone.Marionette.Layout.extend({
        el: '#companies-layout',
        regions: {
            list: "._list",
            newCollectibles: "._sort",
            activities: "._pagination"
        },
        render: function() {
            // do nothing since we are using existing HTML
        }
    });


    return Backbone.Marionette.Controller.extend({
        initialize: function(options) {
            App.layout = new CompaniesLayout();
            App.main.show(App.layout);
        },
        index: function() {
            App.layout.list.show(new CompaniesView({
                collection: App.companies
            }));
        },
        page: function(pageNumber) {
            App.companies.getPage(parseInt(pageNumber));
            if (!App.layout.list.currentView) {
                App.layout.list.show(new CompaniesView({
                    collection: App.companies
                }));
            }

        }
    });
});