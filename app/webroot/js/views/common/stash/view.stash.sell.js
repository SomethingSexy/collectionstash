define(['require', 'backbone', 'marionette', 'text!templates/app/common/stash.sell.mustache', 'mustache', 'marionette.mustache'], function(require, Backbone, Marionnette, template) {
    return Marionnette.ItemView.extend({
        template: template,
        events: {
            "change input": "fieldChanged",
            "change select": "selectionChanged",
            'change textarea': 'fieldChanged',
        },
        initialize: function(options) {
            this.model.on('change:listing_type_id', this.render, this);
        },
        onRender: function() {
            if (this.model.get('listing_type_id')) {
                $('[name=listing_type_id][value=' + this.model.get('listing_type_id') + ']', this.el).attr('checked', 'checked');
            }
        },
        serializeData: function() {
            var data = this.model.toJSON();
            if (data.listing_type_id) {
                if (data.listing_type_id === '2') {
                    data.showSoldCost = true;
                } else if (data.listing_type_id === '3') {
                    data.showTradedFor = true;
                }
            }

            return data;
        },
        // render: function() {
        //     var self = this;
        //     var data = this.collectible.toJSON();
        //     data.model = this.model.toJSON();
        //     data.errors = this.errors;
        //     data.inlineErrors = {};
        //     _.each(this.errors, function(error) {
        //         if (error.inline) {
        //             data.inlineErrors[error.name] = error.message;
        //         }
        //     });

        //     dust.render(this.template, data, function(error, output) {
        //         $(self.el).html(output);
        //     });

        //     // $("#CollectiblesUserRemoveDate", this.el).datepicker().on('changeDate', function(e) {
        //     // self.fieldChanged(e);
        //     // });

        //     this.errors = [];

        //     return this;
        // },
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
});