define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/collectible/edit/collectible.part.edit.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {
    var EditListing = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save',
        },
        initialize: function(options) {
            this.model.startTracking();
            this.collectible = options.collectible;
        },
        onRender: function() {
            if (this.model.get('listing_type_id')) {
                $('[name=listing_type_id][value=' + this.model.get('listing_type_id') + ']', this.el).attr('checked', 'checked');
            }
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
                'count':  parseInt($('[name=count]', this.el).val())
            };

            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    $button.button('reset');
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        }
    });

    _.extend(EditListing.prototype, ErrorMixin);

    return EditListing;
});