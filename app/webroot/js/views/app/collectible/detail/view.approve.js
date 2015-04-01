define(function(require) {

    var Marionette = require('marionette'),
        template = require('text!templates/app/collectible/detail/approve.mustache'),
        Backbone = require('backbone'),
        ErrorMixin = require('views/common/mixin.error'),
        mustache = require('mustache');
    require('marionette.mustache');

    var ApproveView = Marionette.ItemView.extend({
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
            this.collectible = options.collectible;
        },
        modelEvents: {
            "change": "render"
        },
        events: {
            'click .save': 'approve'
        },
        approve: function(event) {
            var self = this;
            event.preventDefault();
            $(event.currentTarget).button('loading');
            Backbone.ajax('/admin/collectibles/approve/' + this.collectible.get('id'), {
                type: 'post',
                dataType: 'json',
                data: {
                    notes: $('textarea[name=notes]', this.el).val()
                },
            }).then(function(data, textStatus, jqXHR) {
                data;
                textStatus;
                jqXHR;

            }, function(jqXHR, textStatus, errorThrown) {
                $(event.currentTarget).button('reset');
                var statusCode = jqXHR.status;
                if (statusCode === 400) {
                    self.onGlobalError(jqXHR.responseText);
                } else if (statusCode === 401) {
                     self.onGlobalError(jqXHR.responseText);
                } else if(statusCode === 500){
                    self.onGlobalError(errorThrown);
                }
                textStatus;
                jqXHR;
                errorThrown;
            });
        }
    });

    _.extend(ApproveView.prototype, ErrorMixin);

    return ApproveView;
});