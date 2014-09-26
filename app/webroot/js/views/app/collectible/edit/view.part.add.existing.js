define(['marionette', 'text!templates/app/collectible/edit/part.add.existing.mustache', 'mustache', 'marionette.mustache'], function(Mariontte, template, CollectibleSearchView) {
    /**
     * Main view when adding an existing attribute
     */
    return Mariontte.ItemView.extend({
        template: template,
        events: {
            'click #select-attribute-link': 'searchCollectible',
            'click ._cancel': 'cancelAdd'
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
        }
    });
});