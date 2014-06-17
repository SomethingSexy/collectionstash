define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/stash.remove.mustache', 'mustache', 'marionette.mustache', 'bootstrap-datepicker'], function(require, _, Backbone, Marionnette, template) {

    return Marionnette.ItemView.extend({
        template: template,
        events: {
            // TODO Add event for change of reason
            'click .save': 'save',
            'change #CollectiblesUserRemoveReason': 'changeReasonEvent'
        },
        initialize: function(options) {
            this.collectible = options.collectible;
            this.reasons = options.reasons;

            this.model.startTracking();

            // this is determing if we require a reason or not depending on what is using it
            this.changeReason = (typeof options.changeReason === 'undefined') ? true : options.changeReason;
            this.listenTo(this.model, 'change:collectible_user_remove_reason_id', function() {
                this.model.unset('sold_cost');
                this.render();
            });

        },
        onRender: function() {
            var self = this;
            $("#CollectiblesUserRemoveDate", this.el).datepicker().on('hide', function(ev) {
                ev.stopPropagation();

            });
            $('#CollectiblesUserRemoveReason option[value="' + this.model.get('collectible_user_remove_reason_id') + '"]', this.el).prop('selected', 'selected');
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            data.reasons = this.reasons.toJSON();
            data.changeReason = this.changeReason;

            //TODO: If there is no changeReason and its active, I need to grab reason for display collectible_user_remove_reason
            // this is if this is being marked as sold, then this is most likely
            // set already on the model
            if (data.active && !data.changeReason) {
                data.collectible_user_remove_reason = "TODO";
            }
            var removeReason = this.model.get('collectible_user_remove_reason_id');

            var showSale = false,
                showTrade = false,
                showDate = false;
            if (removeReason === '1') {
                showSale = true;
                showDate = true;
            } else if (removeReason === '2') {
                showTrade = true;
                showDate = true;
            }

            data.showSale = showSale;
            data.showTrade = showTrade;
            data.showDate = showDate;

            return data;
        },
        changeReasonEvent: function(event) {
            var value = $("option:selected", '[name=collectible_user_remove_reason_id]').val();
            this.model.set('collectible_user_remove_reason_id', value);
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
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
        // TODO: update this to do what we did for the profile
        // only set the fields when we do the save...taht way if they
        // cancel we won't have to worry about remove values
        save: function(event) {
            var self = this;
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            var data = {
                'collectible_user_remove_reason_id': $('[name=collectible_user_remove_reason_id]', this.el).val(),
                'sold_cost': $('[name=sold_cost]', this.el).val(),
                'traded_for': $('[name=traded_for]', this.el).val(),
                'remove_date': $('[name=remove_date]', this.el).val(),
                'sale': true
            };

            $('.btn-primary', this.el).button('loading');

            this.model.destroy({
                url: this.model.url('delete', data),
                wait: true,
                success: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                },
                error: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                    self.errors = response.responseJSON;
                    self.onError();
                }
            });
        }
    });

    return StashRemoveView;
});