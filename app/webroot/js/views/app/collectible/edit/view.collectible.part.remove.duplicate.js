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
        removePart: function(event) {
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
        },
        // Old method
        save: function() {
            var self = this;
            // need to pass Attribute.id (which is the model one), Attribute.link = true, Attribute.replace_attribute_id (which is the new one)
            // upon success, we will then update the attribute passed in with the new information and trigger an update
            // create a temp model to act on here because the this.model is
            // an attributes collectible model and we need a subset of that
            // to update the attribute
            $('.save', this.el).button('loading');
            var saveModel = new AttributeModel({
                Attribute: {
                    id: this.model.get('Attribute').id,
                    link: true,
                    'replace_attribute_id': this.replacementAttribute.get('id'),
                    // not sure a reason is necessary for this one
                    reason: 'Duplicate'
                }
            });
            // since this is not a 100% delete we will do a post here
            // instead of a destroy
            saveModel.save({}, {
                url: '/attributes/remove',
                success: function(model, response) {
                    $('.save', this.el).button('reset');
                    if (response.response.isSuccess) {
                        var message = "";
                        // upon success of us switching out the model,
                        // we need to first check to see if is an edit
                        // or not.
                        if (model.get('isEdit')) {
                            message = 'Replacement has been submitted for approval.';
                            self.trigger('modal:close');
                        } else {
                            message = 'Part has been replaced.';
                            var data = {};
                            data.Attribute = model.get('Attribute');
                            data.Attribute.Scale = model.get('Scale');
                            data.Attribute.Manufacture = model.get('Manufacture');
                            data.Attribute.Artist = model.get('Artist');
                            data.Attribute.AttributeCategory = model.get('AttributeCategory');
                            data.Attribute.AttributesUpload = model.get('AttributesUpload');
                            self.model.set(data);
                            self.trigger('modal:close');
                        }
                        $.blockUI({
                            message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
                            showOverlay: false,
                            css: {
                                top: '100px',
                                'background-color': '#DDFADE',
                                border: '1px solid #93C49F',
                                'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                                'border-radius': '4px 4px 4px 4px',
                                color: '#333333',
                                'margin-bottom': '20px',
                                padding: '8px 35px 8px 14px',
                                'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                                'z-index': 999999
                            },
                            timeout: 2000
                        });
                        // if it is an edit we flash the message and close
                        // without updating the this.model.
                        // if we did update it, when we modify this.model
                        // and we be done
                    }
                }
            });
        }
    });

    _.extend(RmoveCollectiblePart.prototype, ErrorMixin);

    return RmoveCollectiblePart;
});