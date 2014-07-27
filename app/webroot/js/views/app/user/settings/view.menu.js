define(['require', 'marionette', 'text!templates/app/user/settings/menu.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        events: {
            'click ._privacy': 'privacy',
            'click ._profile': 'profile'
        },
        onRender: function() {

        },
        profile: function(event) {
            event.preventDefault();
            this.trigger('navigate:profile');
        },
        privacy: function(event) {
            event.preventDefault();
            this.trigger('navigate:stash');
        }
    });
});