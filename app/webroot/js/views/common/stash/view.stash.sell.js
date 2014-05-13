define(['require', 'backbone', 'marionette', 'text!templates/app/common/stash.sell.mustache', 'mustache', 'marionette.mustache'], function(require, Backbone, Marionnette, template) {
    return Marionnette.ItemView.extend({
        template: template,
        events: {
            "change input": "fieldChanged",
            "change select": "selectionChanged",
            'change textarea': 'fieldChanged',
            'click .save': 'save'
        },
        initialize: function(options) {
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
});