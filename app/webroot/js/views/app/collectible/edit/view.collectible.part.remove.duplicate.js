define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/collectible/edit/collectible.part.remove.duplicate.mustache', 'views/common/mixin.error', 'views/common/growl', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin, growl) {
    var RmoveCollectiblePart = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click ._cancel': 'cancelAdd',
            'click ._remove': 'removePart',
            'click #select-attribute-link': 'searchCollectible'
        },
        initialize: function(options) {
            this.model.startTracking();
            this.collectible = options.collectible;
            this.replacement = options.replacement;
        },
        onRender: function() {
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.part = this.model.part.toJSON();
            data.collectible = this.collectible.toJSON();
            if (this.replacement) {
                data.replacementPart = this.replacement.part.toJSON();
            }
            return data;
        },
        searchCollectible: function() {
            this.trigger('search:collectible');
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        cancelAdd: function() {
            this.trigger('cancel');
        },
        removePart: function() {
            var self = this;
            // need to pass Attribute.id (which is the model one), Attribute.link = true, Attribute.replace_attribute_id (which is the new one)
            // upon success, we will then update the attribute passed in with the new information and trigger an update
            // create a temp model to act on here because the this.model is
            // an attributes collectible model and we need a subset of that
            // to update the attribute
            $('._remove', this.el).button('loading');
            var data = {
                id: this.model.part.get('id'),
                link: true,
                'replace_attribute_id': this.replacement.part.get('id'),
                // not sure a reason is necessary for this one
                reason: 'Duplicate'
            };
            this.model.part.destroy({
                data: data,
                processData: true,
                success: function(model, response) {
                    $('._remove', this.el).button('reset');
                    // upon success of us switching out the model,
                    // we need to first check to see if is an edit
                    // or not.
                    if (model.get('isEdit')) {
                        // message = 'Replacement has been submitted for approval.';
                        self.trigger('part:added');
                    } else {
                        // now update the model that was changed
                        self.model.fetch({
                            success: function() {
                                self.trigger('part:added');
                            }
                        });
                    }
                },
                error: function() {}
            });
        }
    });
    _.extend(RmoveCollectiblePart.prototype, ErrorMixin);
    return RmoveCollectiblePart;
});