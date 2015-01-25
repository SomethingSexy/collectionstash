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
            this.permissions = options.permissions;

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

            this.upload_id = this.model.get('upload_id');

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
            if (this.mode === 'add') {
                data.permissions = {
                    edit_manufacturer: true
                };
            } else {
                data.permissions = this.permissions.toJSON();
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
            var $fileupload = $('.fileupload', this.el);
            $fileupload.fileupload({
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
                // since we are only allowing one, we are fine grabbing the only one that should be returned
                var files = data.jqXHR.responseJSON.files;
                if (files && _.isArray(files) && files.length === 1) {
                    var uploadId = files[0].id;
                    self.upload_id = uploadId;
                }
            }).bind('fileuploaddestroyed', function(e, data) {
                // save the model right away since we are deleting the photo right away we don't want
                // dummy data out there....in the end it would probably be better if the users checks to delete or replace
                // and then we save and have the server process it at the same time
                self.upload_id = null;
                self.model.save({
                    upload_id: null
                }, {
                    silent: true
                });
                // remove the photo object from the model
                if (self.model.photo) {
                    delete self.model.photo;
                }
            });

            if (this.mode === 'edit' && this.permissions.get('edit_manufacturer') === true && this.model.photo) {
                $fileupload.fileupload('option', 'done').call($fileupload, null, {
                    result: {
                        files: [this.model.photo.toJSON()]
                    }
                });
            }

        },
        saveManufacturer: function(event) {
            var self = this;

            var data = {};
            if (this.mode === 'add' || this.permissions.get('edit_manufacturer') === true) {
                data = {
                    title: $('[name=title]', this.el).val(),
                    url: $('[name=url]', this.el).val()
                };

                var bio = $('[name=bio]', this.el).val();
                if (bio) {
                    // this fixes all of those douchy curly quotes that aren't standard.
                    data.bio = bio.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');
                }

                if (this.upload_id) {
                    data.upload_id = this.upload_id;
                } else {
                    // make sure it is null and not undefined or something
                    data.upload_id = null;
                }
            }

            var isValid = true;

            var $button = $(event.currentTarget);
            $button.button('loading');

            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    // TODO: update the photo model with saved image data
                    if (self.mode === 'add') {
                        growl.onSuccess('Your manufacturer has been added!');
                    } else {
                        growl.onSuccess('Your edit has been successfully saved!');
                    }
                    $button.button('reset');
                    model.trigger('save:done');
                },
                error: function() {
                    $button.button('reset');
                }
            });
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