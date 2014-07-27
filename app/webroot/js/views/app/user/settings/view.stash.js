define(['require', 'underscore', 'marionette', 'text!templates/app/user/settings/stash.mustache', 'mustache', 'marionette.mustache', 'backbone.validation'], function(require, _, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save'
        },
        initialize: function() {
            Backbone.Validation.bind(this, {
                valid: function(view, attr, selector) {
                    view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
                    view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                    view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').removeClass('has-error');
                    // do something
                },
                invalid: function(view, attr, error, selector) {
                    view.errors = {};
                    view.errors[attr] = error;
                    view.onError();
                    // do something
                }
            });
            this.errors = {};
        },
        onRender: function() {
            $('[name=privacy] option[value=' + this.model.get('privacy') + ']', this.el).prop('selected', 'selected');
        },
        onError: function() {
            $('.btn-primary', this.el).button('reset');
            var self = this;
            _.each(this.errors, function(error, attr) {
                $('[name="' + attr + '"]', self.el).addClass('invalid').attr('data-error', true);
                $('[name="' + attr + '"]', self.el).closest('.form-group').addClass('has-error');
                $('[name="' + attr + '"]', self.el).parent().find('._error').remove();
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

                $('[name="' + attr + '"]', self.el).after('<span class="help-block _error">' + errorHtml + '</span>');
            });
        },
        save: function(event) {
            var self = this;
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            var data = {
                'privacy': $('[name=privacy]', this.el).val(),
            };

            $('.btn-primary', this.el).button('loading');

            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                },
                error: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                    self.errors = response.responseJSON;
                    self.onError();
                }
            });
        }
    });
});