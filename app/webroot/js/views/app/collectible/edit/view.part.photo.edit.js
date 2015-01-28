define(function(require) {
    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        template = require('text!templates/app/collectible/edit/part.photo.edit.mustache');
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
            this.$('.fileupload').fileupload({
                // add: function(e, data) {
                //     var jqXHR = data.submit().success(function(result, textStatus, jqXHR) {
                //         result;
                //     }).error(function(jqXHR, textStatus, errorThrown) {}).complete(function(result, textStatus, jqXHR) {});
                // },
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
                imageMaxWidth: 800,
                imageMaxHeight: 800,
                imageCrop: true // Force cropped image
            }).bind('fileuploadadd', function(e, data) {
                self.$('._error').empty();
                self.$('.url-upload-input').val('');
            });
            this.$('.fileupload').fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));
            this.$('.fileupload').fileupload('option', {
                url: '/attributes_uploads/upload',
                maxFileSize: 2097152,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                process: [{
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 2097152 // 2MB
                }, {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                }, {
                    action: 'save'
                }]
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
                                that.fileupload('option', 'done').call(that, null, {
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
            var that = this.$('.fileupload');
            var uploads = this.collection.pluck('Upload');
            that.fileupload('option', 'done').call(that, null, {
                result: {
                    files: uploads
                }
            });
            return this;
        }
    });
});