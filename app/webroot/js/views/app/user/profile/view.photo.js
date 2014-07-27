define(['require', 'marionette', 'text!templates/app/user/profile/photo.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        className: 'tile photo',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            data['permissions'] = this.permissions.toJSON();
            return data;
        },
        onRender: function() {
            // $('.stash-sell', this.el).attr('data-collectible-user-id', this.model.get('id'));
            // $('.remove-from-stash', this.el).attr('data-collectible-user-id', this.model.get('id'));
        }
    });
});