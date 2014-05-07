define(['require', 'marionette', 'text!templates/app/user/profile/user.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        onRender: function() {
            // this.$el.removeClass('active completed');

            // if (this.model.get('completed')) {
            // 	this.$el.addClass('completed');
            // } else {
            // 	this.$el.addClass('active');
            // }
        }
    });
});