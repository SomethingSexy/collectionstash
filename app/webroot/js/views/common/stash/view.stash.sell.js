define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/stash.sell.mustache', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template) {
    return Marionnette.ItemView.extend({
        template: template,
        events: {
            "change input": "fieldChanged",
            "change select": "selectionChanged",
            'change textarea': 'fieldChanged',
            'click .save': 'save'
        },
        initialize: function(options) {
            this.model.startTracking();
            this.listenTo(this.model, 'change:listing_type_id', this.render);
        },
        onRender: function() {
            if (this.model.get('listing_type_id')) {
                $('[name=listing_type_id][value=' + this.model.get('listing_type_id') + ']', this.el).attr('checked', 'checked');
            }
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            if (data.listing_type_id) {
                if (data.listing_type_id === '2') {
                    data.showSoldCost = true;
                } else if (data.listing_type_id === '3') {
                    data.showTradedFor = true;
                }
            }

            data.errors = this.errors;
            data.inlineErrors = {};
            _.each(this.errors, function(error) {
                if (error.inline) {
                    data.inlineErrors[error.name] = error.message;
                }
            });

            return data;
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        selectionChanged: function(e) {
            var field = $(e.currentTarget);

            var value = $("option:selected", field).val();

            var data = {};

            data[field.attr('name')] = value;

            this.model.set(data, {
                forceUpdate: true
            });

        },
        fieldChanged: function(e) {

            var field = $(e.currentTarget);
            var data = {};
            if (field.attr('type') === 'checkbox') {
                if (field.is(':checked')) {
                    data[field.attr('name')] = true;
                } else {
                    data[field.attr('name')] = false;
                }
            } else {
                data[field.attr('name')] = field.val();
            }

            this.model.set(data, {
                forceUpdate: true
            });
        },
        onError: function() {
            $('.btn-primary', this.el).button('reset');
            this.removeErrors();
            var self = this;
            _.each(this.errors, function(error, attr) {
                $('[name="' + attr + '"]', self.el).addClass('invalid').attr('data-error', true);
                $('[name="' + attr + '"]', self.el).closest('.form-group').addClass('has-error');
                $('[name="' + attr + '"]', self.el).parent().find('._error').remove();
                var errorHtml = '';
                if (_.isArray(error)) {
                    if (error.length === 1) {
                        errorHtml = error[0];
                    } else {
                        _.each(error, function(message) {
                            errorHtml += '<p>' + message + '</p>';
                        });
                    }
                } else {
                    errorHtml = error;
                }

                $('[name="' + attr + '"]', self.el).after('<span class="help-block _error">' + errorHtml + '</span>');
            });
        },
        removeErrors: function() {
            $('input[data-error=true]', this.el).removeClass('invalid').closest('.form-group').removeClass('has-error').children('._error').empty();
        },
        save: function(event) {
            var self = this;
            var $button = $(event.currentTarget);
            $button.button('loading');
            this.model.save({
                'sale': true
            }, {
                wait: true,
                success: function(model, response, options) {
                    $button.button('reset');
                    if (response.response.isSuccess) {
                        $('#stash-sell-dialog').modal('hide');
                        csStashSuccessMessage('You have successfully added the collectible to your sale/trade list!');

                        // now we need to mark it for sale.  Normally we would use backbone to
                        // re-render the view but we aren't that far yet
                        if (self.tiles) {
                            $('.menu', self.$stashItem).find('.stash-sell').parent().remove();
                            $('.menu .marked-for-sale', self.$stashItem).removeClass('hidden');
                        } else {
                            $('.menu', self.$stashItem).find('.stash-sell').parent().remove();
                        }
                    }
                },
                error: function(model, xhr, options) {
                    $('.btn-primary', self.el).button('reset');
                    self.errors = xhr.responseJSON;
                    self.onError();
                }
            });
        }
    });
});