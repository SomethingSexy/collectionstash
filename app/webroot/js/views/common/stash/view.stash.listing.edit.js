define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/stash.listing.edit.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {
    var EditListing = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save',
            'change [name=listing_type_id]': 'changeListingType'
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
            data.Collectible = this.collectible.toJSON();
            if (data.listing_type_id) {
                if (data.listing_type_id === '2') {
                    data.showSoldCost = true;
                } else if (data.listing_type_id === '3') {
                    data.showTradedFor = true;
                }
            }

            return data;
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        changeListingType: function() {
            var value = $('[name=listing_type_id]:checked').val();
            this.model.set('listing_type_id', value, {
                silent: true
            });
            this.render();
        },
        save: function(event) {
            var self = this;
            var $button = $(event.currentTarget);
            $button.button('loading');
            var data = {
                'sale': true,
                'listing_type_id': $('[name=listing_type_id]:checked', this.el).val(),
                'listing_price': $('[name=listing_price]', this.el).val(),
                'traded_for': $('[name=traded_for]', this.el).val()
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