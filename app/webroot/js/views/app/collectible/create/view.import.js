define(function(require) {

    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        mustache = require('mustache'),
        template = require('text!templates/app/collectible/create/import.mustache');
    require('marionette.mustache');

    var ImportView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click ._save': 'importCollectible'
        },
        importCollectible: function(event) {
            event.preventDefault();
            $(event.currentTarget).button('loading');
            Backbone.ajax('/collectibles/import', {
                type: 'post',
                dataType: 'json',
                data: {
                    url: $('input[name=url]', this.el).val()
                }
            }).then(function(data, textStatus, jqXHR) {
            	data;
                window.location.href = '/collectibles/edit/' + data.id;
            }, function(jqXHR, textStatus, errorThrown) {
                $(event.currentTarget).button('reset');
                var statusCode = jqXHR.status;
                if (statusCode === 400) {
                    self.onGlobalError(jqXHR.responseText);
                } else if (statusCode === 401) {
                    self.onGlobalError(jqXHR.responseText);
                } else if (statusCode === 500) {
                    self.onGlobalError(errorThrown);
                }
            });
        }
    });

    return ImportView;

});