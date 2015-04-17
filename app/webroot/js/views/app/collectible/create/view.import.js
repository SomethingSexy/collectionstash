define(function(require) {

    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        mustache = require('mustache'),
        ErrorMixin = require('views/common/mixin.error'),
        template = require('text!templates/app/collectible/create/import.mustache');
    require('marionette.mustache');

    var ImportView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click ._save': 'importCollectible'
        },
        initialize: function() {
            this.once('shown', function() {
                this.$('input[name=url]').focus();
            });
        },
        importCollectible: function(event) {
            var url = this.$('input[name=url]').val();
            var self = this;
            event.preventDefault();
            if (url.trim() === '') {
                this.addFieldError('url', 'A url is required to import.');
            } else {    
                $(event.currentTarget).button('loading');
                this.onGlobalMessage('Please be patient while we gather all of the information around this collectible.');
                Backbone.ajax('/collectibles/import', {
                    type: 'post',
                    dataType: 'json',
                    data: {
                        url: url
                    }
                }).then(function(data, textStatus, jqXHR) {
                    window.location.href = '/collectibles/edit/' + data.id;
                }, function(jqXHR, textStatus, errorThrown) {
                    self.removeGlobalMessage();
                    $(event.currentTarget).button('reset');
                    var statusCode = jqXHR.status;
                    if (statusCode === 400) {
                        self.onModelError(self, jqXHR);
                    } else if (statusCode === 401) {
                        self.onGlobalError(jqXHR.responseText);
                    } else if (statusCode === 500) {
                        self.onGlobalError(errorThrown);
                    }
                });
            }
        }
    });

    _.extend(ImportView.prototype, ErrorMixin);

    return ImportView;

});