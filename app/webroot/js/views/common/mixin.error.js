define(['require', 'underscore', 'marionette', 'text!templates/app/common/alert.error.mustache', 'text!templates/app/common/alert.message.mustache', 'mustache', 'marionette.mustache'], function(require, _, Marionette, template, messageTemplate, mustache) {

    return {
        onError: function() {
            $('.btn-primary', this.el).button('reset');
            this.removeErrors();
            var self = this;
            _.each(this.errors, function(error, attr) {
                self.addFieldError(attr, error);
            });
        },
        addFieldError: function(attr, error) {
            $('[name="' + attr + '"]', this.el).addClass('invalid').attr('data-error', true);
            $('[name="' + attr + '"]', this.el).closest('.form-group').addClass('has-error');
            $('[name="' + attr + '"]', this.el).parent().find('._error').remove();
            var errorHtml = '';
            if (_.isArray(error)) {
                if (error.length === 1) {
                    errorHtml = error[0];
                } else {
                    _.each(error, function(message) {
                        errorHtml += '<p>' + message + '</p>';
                    });
                }
            } else {
                errorHtml = error;
            }

            $('[name="' + attr + '"]', this.el).after('<span class="help-block _error">' + errorHtml + '</span>');
        },
        onGlobalError: function(message) {
            $('._globalError', this.el).html(Marionette.Renderer.render(template, {
                message: message
            }));
        },
        onGlobalMessage: function(message){
            $('._globalMessage', this.el).html(Marionette.Renderer.render(messageTemplate, {
                message: message
            }));
        },
        removeGlobalMessage: function(){
            $('._globalMessage', this.el).empty();
        },
        removeErrors: function() {
            $('input[data-error=true]', this.el).removeClass('invalid').closest('.form-group').removeClass('has-error').children('._error').empty();
            $('._globalError', this.el).empty();
        },
        onModelError: function(model, response, options) {
            $('.btn-primary', self.el).button('reset');
            if (response.status == 400) {
                this.errors = response.responseJSON;
                this.onError();
            } else if (response.status == 401) {
                this.onGlobalError('You must be logged in to comment');
            } else {
                if (response.responseJSON.response.message) {
                    this.onGlobalError(response.responseJSON.response.message);
                }
            }
        },
        onValiationError: function(errors) {
            this.errors = errors;
            this.onError();
        }
    };
});