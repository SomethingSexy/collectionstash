define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/common/comment.add.mustache', 'views/common/mixin.error', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin) {

    var CommentAdd = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save'
        },
        initialize: function(options) {
            this.model.startTracking();
        },
        serializeData: function(){
            var data = this.model.toJSON();
            data.comment = data.comment.replace(/\\n/g, "\n");
            return data;
        },        
        onRender: function() {
            var self = this;
            this.errors = [];
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
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
                    self.onModelError(model, response, options);
                }
            });
        }
    });

    _.extend(CommentAdd.prototype, ErrorMixin);

    return CommentAdd;
});