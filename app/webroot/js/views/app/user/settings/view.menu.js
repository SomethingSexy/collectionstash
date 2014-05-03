define(['require', 'marionette', 'dust', 'text!templates/app/user/settings/menu.dust', 'marionette-dust'], function(require, Marionette, dust, template) {

    dust.loadSource(dust.compile(template, 'user.settings.menu'));

    return Marionette.ItemView.extend({
        template: 'user.settings.menu',
        events: {
        	'click ._privacy' : 'privacy',
        	'click ._profile' : 'profile'
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