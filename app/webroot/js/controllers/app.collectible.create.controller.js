define(function(require) {

    var App = require('app/app.collectible.create'),
        Backbone = require('backbone'),
        Marionette = require('marionette'),
        mustache = require('mustache'),
        $ = require('jquery');
    require('marionette.mustache');

    var TypeView = Backbone.View.extend({
        el: '#create-container',
        events: {
            'click a.mass': 'addMass',
            'click a.original': 'addOriginal',
            'click a.custom': 'addCustom'
        },
        initialize: function(options) {

        },
        addMass: function(event) {
            event.preventDefault();
            window.location.href = '/collectibles/create/false/false';
        },
        addOriginal: function(event) {
            event.preventDefault();
            window.location.href = '/collectibles/create/true/false';
        },
        addCustom: function(event) {
            event.preventDefault();
            window.location.href = '/collectibles/create/false/true';
        }
    });

    return Backbone.Marionette.Controller.extend({
        initialize: function(options) {
            // App.layout = new CompaniesLayout();
            // App.main.show(App.layout);
        },
        index: function() {
            // no need to get fancy here
            new TypeView();
        }
    });
});