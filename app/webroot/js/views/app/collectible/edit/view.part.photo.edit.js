define(function(require) {
    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        Mustache = require('mustache'),
        template = require('text!templates/app/collectible/edit/part.photo.edit.mustache'),
        uploadTemplate = require('text!templates/app/common/upload.mustache'),
        downloadTemplate = require('text!templates/app/common/download.mustache');
    require('jquery.fileupload');
    require('jquery.fileupload-process');
    require('jquery.fileupload-ui');
    require('jquery.fileupload-image');
    require('jquery.fileupload-validate');
    require('marionette.mustache');

    return Marionette.ItemView.extend({
        template: template,
        events: {},
        initialize: function(options) {
            this.eventManager = options.eventManager;
            this.collectible = options.collectible;
        },
        serializeData: function() {
            var data = {
                uploadDirectory: uploadDirectory,
                part: this.model.part.toJSON()
            };
            return data;
        },
        onClose: function() {
            // update the url to include the collectible id
            this.collection.fetch({
                url: this.collection.url + '/' + this.model.part.get('id')
            });
        },
        onRender: function() {
            var self = this;
            var uploads = this.collection.pluck('Upload');
            var $fileupload = $('.fileupload', this.el);
            $fileupload.fileupload({
                url: '/attributes_uploads/upload',
                dataType: 'json',
                maxFileSize: 2097152,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
                redirect: window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'),
                previewMaxWidth: 100,
                previewMaxHeight: 100,
                previewCrop: true,
                autoUpload: false,
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
            }).bind('fileuploadadd', function(e, data) {
                self.$('._error').empty();
                self.$('.url-upload-input').val('');
            });

            $fileupload.fileupload('option', 'done').call($fileupload, $.Event('done'), {
                result: {
                    files: uploads
                }
            });

            this.$('.fileupload').on('hidden.bs.modal', function() {
                $('#fileupload table tbody tr.template-download').remove();
                pageEvents.trigger('upload:close');
            });
            this.$('.upload-url').on('click', function() {
                var url = $.trim($('.url-upload-input', self.el).val());
                if (url !== '') {
                    $.ajax({
                        dataType: 'json',
                        type: 'post',
                        data: $('.fileupload', self.el).serialize(),
                        url: '/attributes_uploads/upload/',
                        beforeSend: function(formData, jqForm, options) {
                            self.$('.fileupload-progress').removeClass('fade').addClass('active');
                            self.$('.fileupload-progress .progress .bar').css('width', '100%');
                            self.$('._error').empty();
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (data && data.files.length) {
                                var that = self.$('.fileupload');
                                that.fileupload('option', 'done').call(that, $.Event('done'), {
                                    result: data
                                });

                                self.$('.url-upload-input').val('');
                            }
                        },
                        error: function(jqXHR, textStatus, error) {
                            var message;
                            if (_.isString(jqXHR.responseJSON)) {
                                message = jqXHR.responseJSON;
                            } else if (_.isObject(jqXHR.responseJSON) && jqXHR.responseJSON.file) {
                                message = jqXHR.responseJSON.file;
                            }

                            self.$('._error').html('<div class="alert alert-danger" role="alert">' + message + '</div>');
                        },
                        complete: function() {
                            self.$('.fileupload-progress').removeClass('active').addClass('fade');
                            self.$('.fileupload-progress .progress .bar').css('width', '0%');
                        }
                    });
                }
            });

            return this;
        }
    });
});