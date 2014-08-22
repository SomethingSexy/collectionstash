define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/collectible/edit/part.edit.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {
    var EditListing = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save',
            'click .attribute-type': 'toggleType'
        },
        initialize: function(options) {
            this.model.startTracking();
            this.manufacturers = options.manufacturers;
            this.artists = options.artists;
            this.scales = options.scales;
            this.collectible = options.collectible;

            // edit vs new
            this.type = options.type;
        },
        onRender: function() {
            if (this.model.get('listing_type_id')) {
                $('[name=listing_type_id][value=' + this.model.get('listing_type_id') + ']', this.el).attr('checked', 'checked');
            }
            this.errors = [];

            $(self.el).animate({
                scrollTop: 0
            });
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.manufacturers = this.manufacturers.toJSON();
            data.artists = this.artists.toJSON();
            data.scales = this.scales.toJSON();
            // we need this to determine how to render the view
            data.collectible = this.collectible.toJSON();
            if (this.type === 'new') {
                data.showCount = true;
                data.showId = false;
            } else {
                data.showCount = false;
                data.showId = true;
            }

            if (data.type === 'mass') {
                data.isMass = true;
                data.showManufacturer = true;
            } else if (data.type === 'original') {
                data.isOriginal = true;
                data.showManufacturer = true;
            }

            data['uploadDirectory'] = uploadDirectory;
            return data;
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        toggleType: function(event) {
            var field = $(event.currentTarget);
            var type = field.attr('data-type');
            var data = {};
            if (type) {
                // else we need to get the type
                // set the new one
                data = this.model.get(type);
            }
            data[field.attr('data-name')] = field.val();
            // silent because we don't want to trigger a change
            // if this is an edit
            this.model.set(data, {
                silent: true
            });
            this.render();
        },
        save: function(event) {
            var self = this;
            var $button = $(event.currentTarget);
            $button.button('loading');
            var data = {
                'count': parseInt($('[name=count]', this.el).val())
            };

            // TODO: we might need to do this above for new stuff
            var hasType = (this.type === 'edit');
            // default the attribute to be a custom one
            if (this.collectible.get('custom') && !hasType) {
                this.model.set({
                    type: 'custom'
                });
            } else if (this.collectible.get('original') && !hasType) {
                this.model.set({

                    type: 'original'

                });
            } else if (!hasType) {
                this.model.set({

                    type: 'mass'

                });
            }

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