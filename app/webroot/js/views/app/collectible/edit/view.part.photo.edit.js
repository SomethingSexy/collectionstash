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
            $('.fileupload', self.el).fileupload({
                // add: function(e, data) {
                //     var jqXHR = data.submit().success(function(result, textStatus, jqXHR) {
                //         result;
                //     }).error(function(jqXHR, textStatus, errorThrown) {}).complete(function(result, textStatus, jqXHR) {});
                // },
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
                imageMaxWidth: 800,
                imageMaxHeight: 800,
                imageCrop: true // Force cropped image
            });
            $('.fileupload', self.el).fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));
            $('.fileupload', self.el).fileupload('option', {
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
            $('.fileupload', self.el).on('hidden.bs.modal', function() {
                $('#fileupload table tbody tr.template-download').remove();
                pageEvents.trigger('upload:close');
            });
            $('.upload-url', self.el).on('click', function() {
                var url = $.trim($('.url-upload-input', self.el).val());
                if (url !== '') {
                    $.ajax({
                        dataType: 'json',
                        type: 'post',
                        data: $('.fileupload', self.el).serialize(),
                        url: '/attributes_uploads/upload/',
                        beforeSend: function(formData, jqForm, options) {
                            $('.fileupload-progress', self.el).removeClass('fade').addClass('active');
                            $('.fileupload-progress .progress .bar', self.el).css('width', '100%');
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (data && data.files.length) {
                                var that = $('.fileupload', self.el);
                                that.fileupload('option', 'done').call(that, null, {
                                    result: data
                                });
                            } else if (data.response && !data.response.isSuccess) {
                                // most like an error
                                $('span', '.component-message.error').text(data.response.errors[0].message);
                            }
                        },
                        complete: function() {
                            $('.fileupload-progress', self.el).removeClass('active').addClass('fade');
                            $('.fileupload-progress .progress .bar', self.el).css('width', '0%');
                        }
                    });
                }
            });
            var that = $('.fileupload', self.el);
            var uploads = self.collection.pluck('Upload');
            that.fileupload('option', 'done').call(that, null, {
                result: {
                    files: uploads
                }
            });
            return this;
        }
    });
});