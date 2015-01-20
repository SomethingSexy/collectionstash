define(function(require) {
    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        $ = require('jquery'),
        Mustache = require('mustache'),
        dust = require('dust'),
        addTemplate = require('text!templates/app/collectible/edit/company.add.mustache'),
        editTemplate = require('text!templates/app/collectible/edit/company.edit.mustache'),
        uploadTemplate = require('text!templates/app/common/upload.mustache'),
        downloadTemplate = require('text!templates/app/common/download.mustache'),
        ErrorMixin = require('views/common/mixin.error'),
        growl = require('views/common/growl');
    require('select2');
    require('marionette.mustache');
    require('jquery.fileupload');
    require('jquery.fileupload-fp');
    require('jquery.fileupload-ui');


    var lastResults = [];
    var CompanyView = Marionette.ItemView.extend({
        getTemplate: function() {
            if (this.mode === 'edit') {
                return editTemplate;
            } else if (this.mode === 'add') {
                return addTemplate;
            }
        },
        events: {
            'click .save': 'saveManufacturer',
            'click .manufacturer-brand-add': 'addBrand'
        },
        initialize: function(options) {
            var self = this;
            this.mode = options.mode;

            if (this.mode === 'add') {

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

            if (!this.model.get('LicensesManufacture')) {
                this.model.LicensesManufacture = [];
            } else {
                this.model.LicensesManufacture = this.model.get('LicensesManufacture');
            }

            this.listenTo(this.model, 'change:LicensesManufacture', this.render);
            this.listenTo(this.model, 'validated:valid', function() {

            });
            this.listenTo(this.model, 'validated:invalid', function(model, invalid) {
                self.onValiationError(invalid);
            });
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.LicensesManufacture = this.model.LicensesManufacture;
            if (this.model.photo) {
                data.photo = this.model.photo.toJSON();
            }
            return data;
        },
        onRender: function() {
            var self = this;

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

            // we will need to process or handle a change button.
            // The change button would remove the file but not 
            // delete it.  A new file could be selected, once saved
            // the manufacturer add/edit would handle removing it
            $('.fileupload', this.el).fileupload({
                url: '/uploads/add',
                maxFileSize: 2097152,
                maxNumberOfFiles: 1,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
                imageMaxWidth: 800,
                imageMaxHeight: 800,
                imageCrop: true, // Force cropped image,
                autoUpload: true,
                // sequentialUploads: true,
                uploadTemplateId: null,
                downloadTemplateId: null,
                uploadTemplate: function(o) {
                    var output = '';
                    _.each(o.files, function(file, index) {
                        file.autoUpload = true;
                        output += Mustache.render(uploadTemplate, file)
                    });

                    return output;

                },
                downloadTemplate: function(o) {
                    var output = '';
                    _.each(o.files, function(file, index) {
                        output += Mustache.render(downloadTemplate, file)
                    });

                    return output;
                }
            }).bind('fileuploaddone', function(e, data) {
                data.result;
                data.textStatus;
                // since we are only allowing one, we are fine grabbing the only one that should be returned
                var files = data.jqXHR.responseJSON.files;
                if (files && _.isArray(files) && files.length === 1) {
                    var uploadId = files[0].id;
                    self.model.set('upload_id', uploadId);
                }

            });
        },
        saveManufacturer: function(event) {
            var self = this;

            var data = {};
            if (this.mode === 'add') {
                data = {
                    title: $('[name=title]', this.el).val(),
                    bio: $('[name=bio]', this.el).val(),
                    url: $('[name=url]', this.el).val()
                };
            }


            var isValid = true;

            var $button = $(event.currentTarget);
            $button.button('loading');

            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    if (this.mode === 'add') {
                        growl.onSuccess('Your manufacturer has been added!');
                    } else {
                        growl.onSuccess('Your edit has been successfully saved!');
                    }
                    $button.button('reset');
                },
                error: function() {
                    $button.button('reset');
                }
            });
            // }
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
                // Also check first to see if this exists already
                var brands = this.model.LicensesManufacture;
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
                    this.model.trigger("change:LicensesManufacture");
                } else {
                    $('.input-man-brand-error', self.el).text('That brand has already been added.');
                }
            }
        }
    });

    _.extend(CompanyView.prototype, ErrorMixin);

    return CompanyView;
});