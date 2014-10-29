define(['marionette', 'text!templates/app/collectible/edit/part.add.existing.mustache', 'views/common/mixin.error', 'views/common/growl', 'mustache', 'marionette.mustache'], function(Mariontte, template, ErrorMixin, growl) {
    /**
     * Main view when adding an existing attribute
     */
    var AddPartView = Mariontte.ItemView.extend({
        template: template,
        events: {
            'click #select-attribute-link': 'searchCollectible',
            'click ._cancel': 'cancelAdd',
            'click ._add': 'addPart'
        },
        initialize: function(options) {
            // pssing in the collectible model, used to determine collectible type
            this.collectible = options.collectible;
        },
        serializeData: function() {
            var self = this;
            var data = {};
            if (this.model) {
                data = this.model.toJSON();
                data.part = this.model.part.toJSON();
                data.hasPart = true;
            } else {
                data.hasPart = false;
            }

            data['uploadDirectory'] = uploadDirectory;
            // if the collectible is a custom, then we will display
            // the type field
            data['collectible'] = this.collectible.toJSON();

            return data;
        },
        onClose: function() {
            //TODO: close any potential open views
        },
        searchCollectible: function() {
            this.trigger('search:collectible');
        },
        cancelAdd: function() {
            this.trigger('cancel');
        },
        addPart: function() {
            var self = this;
            var model = new this.model.constructor({
                attribute_collectible_type_id: this.model.get('attribute_collectible_type_id'),
                attribute_id: this.model.get('attribute_id'),
                collectible_id: this.collectible.get('id'),
                count: $('[name=count]', this.el).val()
            });

            model.save({}, {
                success: function(model, response, options) {
                    if (response.isEdit) {
                        growl.onSuccess('Your new part has been successfully submitted!');
                        self.trigger('part:added', model);
                    } else {
                        growl.onSuccess('Your part has been successfully added!');
                        self.trigger('part:added', model);
                    }
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });

        }
    });

    _.extend(AddPartView.prototype, ErrorMixin);

    return AddPartView;
});