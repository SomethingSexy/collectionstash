define(['backbone', 'jquery', 'select2', 'backbone.validation'], function(Backbone, $) {
    var ManufacturerView = Backbone.View.extend({
        template: 'manufacturer.add',
        modal: 'modal',
        events: {
            "change #inputManName": "fieldChanged",
            "change #inputManUrl": "fieldChanged",
            'change textarea': 'fieldChanged',
            'click .save': 'saveManufacturer',
            'click .manufacturer-brand-add': 'addBrand'
        },
        initialize: function(options) {
            var self = this;
            this.mode = options.mode;
            if (options.mode === 'edit') {
                this.template = 'manufacturer.edit';
            } else if (options.mode === 'add') {
                this.template = 'manufacturer.add';
            }
            if (this.mode === 'add') {
                Backbone.Validation.bind(this, {
                    valid: function(view, attr, selector) {
                        view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
                        view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                        view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').removeClass('has-error');
                        // do something
                    },
                    invalid: function(view, attr, error, selector) {
                        view.$('[' + selector + '~="' + attr + '"]').addClass('invalid').attr('data-error', error);
                        view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').addClass('has-error');
                        view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                        view.$('[' + selector + '~="' + attr + '"]').after('<span class="help-block _error">' + error + '</span>');
                        // do something
                    }
                });
            }
            this.brands = options.brands;
            this.brandArray = [];
            options.brands.each(function(brand) {
                self.brandArray.push(brand.get('License').name);
            });
            this.listenTo(this.model, 'change:LicensesManufacture', this.renderBody);
            // this.manufacturerBrands = new Bloodhound({
            //     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            //     queryTokenizer: Bloodhound.tokenizers.whitespace,
            //     // `states` is an array of state names defined in "The Basics"
            //     local: $.map(this.brandArray, function(state) {
            //         return {
            //             value: state
            //         };
            //     })
            // });
            // // kicks off the loading/processing of `local` and `prefetch`
            // this.manufacturerBrands.initialize();
        },
        renderBody: function() {
            var self = this;
            var data = {
                manufacturer: this.model.toJSON()
            };
            dust.render(this.template, data, function(error, output) {
                $('.modal-body', self.el).html(output);
            });
            $('.manufacturer-brand .typeahead', self.el).select2({
                data: {
                    results: this.brandArray,
                    // text: 'tag'
                },
                // formatSelection: format,
                // formatResult: format
            });
        },
        render: function() {
            var self = this;
            dust.render(this.modal, {
                modalId: 'manufacturerModal',
                modalTitle: 'Manufacturer'
            }, function(error, output) {
                $(self.el).html(output);
            });
            this.renderBody();
            return this;
        },
        selectionChanged: function(e) {
            var field = $(e.currentTarget);
            var value = $("option:selected", field).val();
            var data = {};
            data[field.attr('name')] = value;
            this.model.set(data);
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
            this.model.set(data);
        },
        saveManufacturer: function() {
            var self = this;
            var isValid = true;
            if (this.mode === 'add') {
                isValid = this.model.isValid(true);
            }
            if (isValid) {
                $('.btn-primary', this.el).button('loading');
                this.model.save({}, {
                    error: function() {
                        $('.btn-primary', self.el).button('reset');
                    }
                });
            }
        },
        addBrand: function() {
            var self = this;
            $('.input-man-brand-error', self.el).text('');
            var brand = $('#inputManBrand', self.el).val();
            brand = $.trim(brand);
            $('.inline-error', self.el).text('');
            $('.form-group ', self.el).removeClass('has-error');
            if (brand !== '') {
                if (!this.model.get('LicensesManufacture')) {
                    this.model.set({
                        LicensesManufacture: []
                    }, {
                        silent: true
                    });
                }
                // Also check first to see if this exists already
                var brands = this.model.get('LicensesManufacture');
                var add = true;
                _.each(brands, function(existingBrand) {
                    if (existingBrand.License && existingBrand.License.name) {
                        if (existingBrand.License.name.toLowerCase() === brand.toLowerCase()) {
                            add = false
                        }
                    }
                });
                if (add) {
                    brands.push({
                        License: {
                            name: brand
                        }
                    });
                    this.model.set({
                        LicensesManufacture: brands
                    }, {
                        silent: true
                    });
                    this.model.trigger("change:LicensesManufacture");
                } else {
                    $('.input-man-brand-error', self.el).text('That brand has already been added.');
                }
            }
        }
    });

    return ManufacturerView;
});