define(['backbone', 'jquery', 'mustache', 'dust', 'text!templates/app/collectible/edit/company.add.mustache', 'text!templates/app/collectible/edit/company.edit.mustache', 'select2', 'backbone.validation'], function(Backbone, $, Mustache, dust, addTemplate, editTemplate) {
    var lastResults = [];
    var ManufacturerView = Backbone.View.extend({
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
                this.template = editTemplate;
            } else if (options.mode === 'add') {
                this.template = addTemplate;
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
                self.brandArray.push({
                    id: brand.get('License').id,
                    text: brand.get('License').name
                });
            });
            lastResults = this.brandArray;
            this.listenTo(this.model, 'change:LicensesManufacture', this.renderBody);
        },
        renderBody: function() {
            var self = this;
            var data = {
                manufacturer: this.model.toJSON()
            };

            $('.modal-body', self.el).html(Mustache.render(this.template, data));

            $('.company-typeahead', self.el).select2({
                placeholder: 'Search or add a new brand.',
                data: {
                    results: this.brandArray,
                },
                createSearchChoice: function(term, data) {
                    if (lastResults.some(function(r) {
                        return r.text == term
                    })) {
                        return {
                            id: data.id,
                            text: term
                        };
                    } else {
                        return {
                            id: term,
                            text: term
                        };
                    }
                },
                allowClear: true,
                dropdownCssClass: "bigdrop"
            }).on('select2-removed', function() {
                $('.input-man-brand-error', self.el).text('');
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
            var self = this,
                valObj = $('.company-typeahead', this.el).select2('data');
            var brand = valObj && valObj.text ? valObj.text : '';
            brand = $.trim(brand);

            $('.input-man-brand-error', self.el).text('');

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