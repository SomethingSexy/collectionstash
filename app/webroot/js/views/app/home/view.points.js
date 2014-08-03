define(['require', 'marionette', 'text!templates/app/home/points.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        onRender: function() {

        }
    });
});