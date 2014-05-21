define(['require', 'backbone', 'marionette', 'text!templates/app/common/stash.remove.mustache', 'mustache', 'marionette.mustache', 'bootstrap-datepicker'], function(require, Backbone, Marionnette, template) {

    return Marionnette.ItemView.extend({
        template: template,
        events: {
            "change input": "fieldChanged",
            "change select": "selectionChanged",
            'change textarea': 'fieldChanged',
        },
        initialize: function(options) {
            this.collectible = options.collectible;
            this.reasons = options.reasons;
            // this is determing if we require a reason or not depending on what is using it
            this.changeReason = (typeof options.changeReason === 'undefined') ? true : options.changeReason;
            this.model.on('change:collectible_user_remove_reason_id', function() {
                this.model.unset('sold_cost');
                this.render();

            }, this);
        },
        onRender: function() {
            $("#CollectiblesUserRemoveDate", this.el).datepicker().on('changeDate', function(e) {
                self.fieldChanged(e);
            });
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            data.reasons = this.reasons.toJSON();
            data.changeReason = this.changeReason;

            data.errors = this.errors;
            data.inlineErrors = {};
            _.each(this.errors, function(error) {
                if (error.inline) {
                    data.inlineErrors[error.name] = error.message;
                }
            });

            return data;
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
        remove: function() {
            Backbone.View.prototype.remove.call(this);
            this.model.off();
        }
    });

    return StashRemoveView;
});