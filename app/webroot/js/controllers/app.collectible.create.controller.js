define(function(require) {

    var App = require('app/app.collectible.create'),
        Backbone = require('backbone'),
        Marionette = require('marionette'),
        mustache = require('mustache'),
        ImportView = require('views/app/collectible/create/view.import'),
        $ = require('jquery');
    require('marionette.mustache');



    var TypeView = Backbone.View.extend({
        el: '#create-container',
        events: {
            'click a.mass': 'addMass',
            'click a.original': 'addOriginal',
            'click a.custom': 'addCustom',
            'click a._import' : 'importCollectible'
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
        },
        importCollectible: function(event){
            event.preventDefault();
            this.trigger('import:collectible');
        }
    });

    return Backbone.Marionette.Controller.extend({
        initialize: function(options) {
            // App.layout = new CompaniesLayout();
            // App.main.show(App.layout);
        },
        index: function() {
            // no need to get fancy here
            new TypeView().on('import:collectible', function(){
                App.modal.show(new ImportView());
            });
        }
    });
});