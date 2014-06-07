define(['require', 'backbone', 'marionette', 'text!templates/app/common/stash.add.mustache', 'mustache', 'marionette.mustache', 'bootstrap-datepicker'], function(require, Backbone, Marionnette, template) {

    return Marionnette.ItemView.extend({
        template: template,
        events: {
            // TODO Add event for change of reason
            'click .save': 'save'
        },
        initialize: function(options) {
            this.collectible = options.collectible;
            this.reasons = options.reasons;

            this.model.startTracking();
        },
        onRender: function() {
            var self = this;
            $("#CollectiblesUserPurchaseDate", this.el).datepicker().on('hide', function(ev) {
                ev.stopPropagation();
               
            });
            $('#CollectiblesUserConditionId option[value="' + this.model.get('condition_id') + '"]', this.el).prop('selected', 'selected');
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.Collectible = this.model.collectible.toJSON();
            return data;
        },
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
        removeErrors: function(){
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
                'edition_size': $('[name=edition_size]', this.el).val(),
                'artist_proof': $('[name=artist_proof]', this.el).val(),
                'cost': $('[name=cost]', this.el).val(),
                'condition_id': $('[name=condition_id]', this.el).val(),
                'merchant': $('[name=merchant]', this.el).val(),
                'purchase_date': $('[name=purchase_date]', this.el).val(),
                'notes': $('[name=notes]', this.el).val(),
                'notes_private': $('[name=notes_private]', this.el).val(),
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