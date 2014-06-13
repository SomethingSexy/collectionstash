define(['require', 'backbone', 'marionette', 'text!templates/app/common/comment.add.mustache', 'mustache', 'marionette.mustache'], function(require, Backbone, Marionnette, template) {

    return Marionnette.ItemView.extend({
        template: template,
        events: {
            // TODO Add event for change of reason
            'click .save': 'save'
        },
        initialize: function(options) {
            // this.collectible = options.collectible;
            // this.reasons = options.reasons;

            this.model.startTracking();
        },
        onRender: function() {
            var self = this;
            this.errors = [];
        },
        // serializeData: function() {
        //     var data = this.model.toJSON();
        //     data.Collectible = this.model.collectible.toJSON();
        //     return data;
        // },
        onClose: function() {
            this.model.resetAttributes();
            this.model.stopTracking();
        },
        onError: function() {
            $('.btn-primary', this.el).button('reset');
            this.removeErrors();
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
        removeErrors: function() {
            $('input[data-error=true]', this.el).removeClass('invalid').closest('.form-group').removeClass('has-error').children('._error').empty();
        },
        // TODO: update this to do what we did for the profile
        // only set the fields when we do the save...taht way if they
        // cancel we won't have to worry about remove values
        save: function(event) {
            var self = this;
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            var data = {
                'comment': $('[name=comment]', this.el).val(),
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

    return StashRemoveView;
});