define(['require', 'marionette', 'dust', 'text!templates/app/user/settings/profile.dust', 'marionette-dust', 'backbone.validation'], function(require, Marionette, dust, template) {

    dust.loadSource(dust.compile(template, 'user.settings.profile'));

    return Marionette.ItemView.extend({
        template: 'user.settings.profile',
        events: {
            'click .save': 'save'
        },
        initialize: function() {
            Backbone.Validation.bind(this, {
                invalid: function(view, attr, error) {
                    // do something
                    console.log(error);
                }
            });
        },
        onRender: function() {

        },
        save: function(event) {
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            var data = {
                'first_name': $('[name=first_name]', this.el).val(),
                'last_name': $('[name=last_name]', this.el).val(),
                'email': $('[name=email]', this.el).val(),
                'display_name': $('[name=display_name]', this.el).val()
            };

            this.model.save(data, {
                wait: true
            });
        }
    });
});