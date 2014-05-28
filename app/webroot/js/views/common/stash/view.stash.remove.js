define(['require', 'backbone', 'marionette', 'text!templates/app/common/stash.remove.mustache', 'mustache', 'marionette.mustache', 'bootstrap-datepicker'], function(require, Backbone, Marionnette, template) {

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
            data.errors = this.errors;
            data.inlineErrors = {};
            _.each(this.errors, function(error) {
                if (error.inline) {
                    data.inlineErrors[error.name] = error.message;
                }
            });

            return data;
        },
        changeReasonEvent: function(event) {
            var value = $("option:selected", '[name=collectible_user_remove_reason_id]').val();
            this.model.set('collectible_user_remove_reason_id', value);
        },
        onClose: function() {
            if (this.model.changedAttributes(['collectible_user_remove_reason_id'])) {
                var prev = this.model.previousAttributes();
                this.model.set('collectible_user_remove_reason_id', prev['collectible_user_remove_reason_id'], {
                    silent: true
                });
            }
        },
        // TODO: update this to do what we did for the profile
        // only set the fields when we do the save...taht way if they
        // cancel we won't have to worry about remove values
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
                    $button.button('reset');

                    if (xhr.status === 500) {
                        self.errors = xhr.responseJSON.response.errors;
                        self.render();
                    }
                }
            });
        }
    });

    return StashRemoveView;
});