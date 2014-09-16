define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/collectible/edit/collectible.part.remove.mustache', 'views/common/mixin.error', 'views/common/growl', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin, growl) {

    var RmoveCollectiblePart = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save',
        },
        initialize: function(options) {
            this.model.startTracking();
            this.collectible = options.collectible;
        },
        onRender: function() {
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.collectible = this.collectible.toJSON();
            return data;
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        save: function(event) {
            var self = this;
            var $button = $(event.currentTarget);
            $button.button('loading');
            var data = {
                'reason': parseInt($('[name=reason]', this.el).val())
            };

            this.model.destroy({
                data: data,
                processData: true,
                wait: true,
                success: function(model, response, options) {
                    if (response.isEdit) {
                        growl.onSuccess('Your edit to the part has been successfully submitted!');
                    } else {
                        growl.onSuccess('Your edit has been successfully saved!');
                    }
                    $button.button('reset');
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        }
    });

    _.extend(RmoveCollectiblePart.prototype, ErrorMixin);

    return RmoveCollectiblePart;
});