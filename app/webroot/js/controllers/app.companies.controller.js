define(function(require) {

    var App = require('app/app.companies'),
        Backbone = require('backbone'),
        Marionette = require('marionette'),
        layout = require('text!templates/app/home/layout.mustache'),
        CompaniesView = require('views/app/company/companies/view.companies'),
        mustache = require('mustache'),
        EditCompany = require('views/app/collectible/edit/view.company');
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

    function renderCompaniesView() {
        var companiesView = new CompaniesView({
            collection: App.companies,
            permissions: App.permissions
        });
        companiesView.on('edit:company', function(id) {
            var company = App.companies.get(id);

            company.once('save:done', function(model, response, options) {
                if (_.isArray(response)) {
                    // App.comments.add(response);
                }
                App.modal.hideModal();
            });


            App.modal.show(new EditCompany({
                mode: 'edit',
                model: company,
                permissions: App.permissions,
                brands: App.brands
            }));
        });
        App.layout.list.show(companiesView);
    }


    return Backbone.Marionette.Controller.extend({
        initialize: function(options) {
            App.layout = new CompaniesLayout();
            App.main.show(App.layout);
        },
        index: function() {
            renderCompaniesView();
        },
        page: function(pageNumber) {
            App.companies.getPage(parseInt(pageNumber));
            if (!App.layout.list.currentView) {
                renderCompaniesView();
            }

        }
    });
});