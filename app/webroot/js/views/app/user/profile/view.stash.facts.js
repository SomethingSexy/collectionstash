define(['require', 'marionette', 'text!templates/app/user/profile/stash.facts.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        onRender: function() {

        }
    });
});