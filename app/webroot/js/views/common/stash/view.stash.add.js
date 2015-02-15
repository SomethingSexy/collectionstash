define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/stash.add.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache', 'bootstrap-datepicker', 'select2'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {

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
            this.lastResults = [];
        },
        onRender: function() {
            var self = this;
            $("#CollectiblesUserPurchaseDate", this.el).datepicker().on('hide', function(ev) {
                ev.stopPropagation();

            });
            $('#CollectiblesUserConditionId option[value="' + this.model.get('condition_id') + '"]', this.el).prop('selected', 'selected');
            this.errors = [];

            $('.merchants-typeahead', this.el).select2({
                placeholder: 'Search or add a new merchant.',
                minimumInputLength: 1,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "/merchants/merchants",
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            query: term, // search term
                            page_limit: 100
                        };
                    },
                    results: function(data, page) {
                        self.lastResults = data;
                        return {
                            results: data
                        };
                    }
                },
                formatResult: function(item) {
                    return item.name;
                },
                formatSelection: function(item) {
                    return item.name;
                },
                createSearchChoice: function(term, data) {
                    if (self.lastResults.some(function(r) {
                        return r.name == term
                    })) {
                        return {
                            id: data.id,
                            name: term,
                            created: false
                        };
                    } else {
                        return {
                            id: term,
                            name: term,
                            created: true
                        };
                    }
                },
                allowClear: true,
                dropdownCssClass: "bigdrop"
            }).on('change', function(val, added, removed) {
                var data = $('.merchants-typeahead', self.el).select2('data');
                if (!data || !data.name) {
                    self.model.unset('merchant', {
                        forceUpdate: true
                    });
                    return;
                }
                if (data.created) {
                    self.model.set({
                        merchant: data.name,
                    }, {
                        forceUpdate: true
                    });
                } else {
                    self.model.set({
                        merchant: data.name,
                    }, {
                        forceUpdate: true
                    });
                }
            });
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
                // 'merchant': $('[name=merchant]', this.el).val(),
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