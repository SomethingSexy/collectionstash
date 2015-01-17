define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/stash.add.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache', 'bootstrap-datepicker'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {

    var StashAddView = Marionnette.ItemView.extend({
        template: template,
        events: {
            // TODO Add event for change of reason
            'click .save': 'save'
        },
        initialize: function(options) {
            this.collectible = options.collectible;
            this.reasons = options.reasons;
            this.stashCount = options.stashCount || 0;
            this.wishlistCount = options.wishlistCount || 0;
            this.model.startTracking();
        },
        onRender: function() {
            var self = this;
            $("#CollectiblesUserPurchaseDate", this.el).datepicker().on('hide', function(ev) {
                ev.stopPropagation();

            });
            $('#CollectiblesUserConditionId option[value="' + this.model.get('condition_id') + '"]', this.el).prop('selected', 'selected');
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            if (this.stashCount > 0) {
                data.stashCount = this.stashCount;
            }

            if (this.wishlistCount > 0) {
                data.wishlistCount = this.wishlistCount;
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
        // TODO: update this to do what we did for the profile
        // only set the fields when we do the save...taht way if they
        // cancel we won't have to worry about remove values
        save: function(event) {
            var self = this;
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            var data = {
                'edition_size': $('[name=edition_size]', this.el).val(),
                'cost': $('[name=cost]', this.el).val(),
                'condition_id': $('[name=condition_id]', this.el).val(),
                'merchant': $('[name=merchant]', this.el).val(),
                'purchase_date': $('[name=purchase_date]', this.el).val(),
                'notes': $('[name=notes]', this.el).val(),
            };

            if ($('[name=artist_proof]', this.el).is(':checked')) {
                data['artist_proof'] = true;
            } else {
                data['artist_proof'] = false;
            }

            if ($('[name=notes_private]', this.el).is(':checked')) {
                data['notes_private'] = true;
            } else {
                data['notes_private'] = false;
            }

            $('.btn-primary', this.el).button('loading');

            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        }
    });

    _.extend(StashAddView.prototype, ErrorMixin);

    return StashAddView;
});