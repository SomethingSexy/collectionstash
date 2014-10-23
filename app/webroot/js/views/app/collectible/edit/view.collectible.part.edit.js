define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/collectible/edit/collectible.part.edit.mustache', 'views/common/mixin.error', 'views/common/growl', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin, growl) {
    var EditCollectiblePart = Marionnette.ItemView.extend({
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
            if (this.model.get('attribute_collectible_type_id')) {
                $('[name=attribute_collectible_type_id] option[value=' + this.model.get('attribute_collectible_type_id') + ']', this.el).attr('selected', 'selected');
            }
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
                'count': parseInt($('[name=count]', this.el).val())
            };
            if (this.collectible.get('custom')) {
                data.attribute_collectible_type_id = $('[name=attribute_collectible_type_id]', this.el).val()
            }
            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    if (response.isEdit) {
                        growl.onSuccess('Your edit to the part has been successfully submitted!');
                    } else {
                        growl.onSuccess('Your edit has been successfully saved!');
                    }
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        }
    });

    _.extend(EditCollectiblePart.prototype, ErrorMixin);

    return EditCollectiblePart;
});