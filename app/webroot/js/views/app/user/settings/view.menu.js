define(['require', 'marionette', 'dust', 'text!templates/app/user/settings/menu.dust', 'marionette-dust'], function(require, Marionette, dust, template) {

    dust.loadSource(dust.compile(template, 'user.settings.menu'));

    return Marionette.ItemView.extend({
        template: 'user.settings.menu',
        events: {

        },
        onRender: function() {

        }
    });
});