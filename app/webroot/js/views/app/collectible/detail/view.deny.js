define(function(require) {

    var Marionette = require('marionette'),
        template = require('text!templates/app/collectible/detail/deny.mustache'),
        Backbone = require('backbone'),
        ErrorMixin = require('views/common/mixin.error'),
        mustache = require('mustache');
    require('marionette.mustache');

    var DenyView = Marionette.ItemView.extend({
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
            this.collectible = options.collectible;
        },
        modelEvents: {
            "change": "render"
        },
        events: {
            'click .save': 'deny'
        },
        deny: function(event) {
            var self = this;
            event.preventDefault();
            if ($('textarea[name=notes]', this.el).val().trim() === '') {
                this.addFieldError('notes', 'Notes are required when denying a collectible.');
                return;
            }

            $(event.currentTarget).button('loading');
            Backbone.ajax('/admin/collectibles/deny/' + this.collectible.get('id'), {
                type: 'post',
                dataType: 'json',
                data: {
                    notes: $('textarea[name=notes]', this.el).val()
                },
            }).then(function(data, textStatus, jqXHR) {
                window.location.href = '/admin/collectibles';
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

    _.extend(DenyView.prototype, ErrorMixin);

    return DenyView;
});