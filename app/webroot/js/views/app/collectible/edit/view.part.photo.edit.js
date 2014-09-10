    var AttributePhotoView = Backbone.View.extend({
        template: 'attribute.photo.edit',
        className: "col-md-4",
        events: {},
        initialize: function(options) {
            this.eventManager = options.eventManager;
            // this.collection.on('reset', function() {
            // var self = this;
            // var data = {
            // uploads : this.collection.toJSON(),
            // uploadDirectory : uploadDirectory
            // };
            // dust.render(this.template, data, function(error, output) {
            // $(self.el).html(output);
            // });
            // }, this);
        },
        render: function() {
            var self = this;
            var data = {
                uploadDirectory: uploadDirectory,
                attribute: this.model.toJSON()
            };
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            $('.fileupload', self.el).fileupload({
                //dropZone : $('#dropzone')
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
            that.fileupload('option', 'done').call(that, null, {
                result: {
                    files: self.collection.toJSON()
                }
            });
            return this;
        }
    });