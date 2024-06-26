define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/stash.remove.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache', 'bootstrap-datepicker'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {

    var RemoveStashView = Marionnette.ItemView.extend({
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

            if (options.removeReasonId) {
                this.model.set('collectible_user_remove_reason_id', options.removeReasonId);
            }

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
            var removeReason = this.model.get('collectible_user_remove_reason_id');
            if (data.active && !data.changeReason) {
                data.collectible_user_remove_reason = this.reasons.get(removeReason).get('reason');
            }

            var showSale = false,
                showTrade = false,
                showDate = false;
            // using == might be string or number
            if (removeReason == '1') {
                showSale = true;
                showDate = true;
            } else if (removeReason == '2') {
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
        // TODO: update this to do what we did for the profile
        // only set the fields when we do the save...taht way if they
        // cancel we won't have to worry about remove values
        save: function(event) {
            var self = this;
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            var data = {
                'sold_cost': $('[name=sold_cost]', this.el).val(),
                'traded_for': $('[name=traded_for]', this.el).val(),
                'remove_date': $('[name=remove_date]', this.el).val(),
                'sale': true
            };

            // if we can change the reason, then set it, otherwise it should already be added
            if (this.changeReason) {
                data['collectible_user_remove_reason_id'] = $('[name=collectible_user_remove_reason_id]', this.el).val();
            }

            $('.btn-primary', this.el).button('loading');

            this.model.destroy({
                url: this.model.url('delete', data),
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

    _.extend(RemoveStashView.prototype, ErrorMixin);

    return RemoveStashView;
});