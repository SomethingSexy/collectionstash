define(['backbone', 'marionette', 'text!templates/app/common/loader.mustache', 'mustache', 'marionette.mustache'], function(Backbone, Marionette, template) {
    return Marionette.ItemView.extend({
        template: template,
        onRender: function() {

        }
    });
});