define(['require', 'marionette', 'text!templates/app/user/profile/stash.collectible.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template
    });
});