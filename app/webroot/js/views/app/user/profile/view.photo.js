define(function(require) {
    var Marionette = require('marionette'),
        template = require('text!templates/app/user/profile/photo.mustache'),
        mustache = require('mustache');
    require('marionette.mustache');
    
    return Marionette.ItemView.extend({
        className: 'tile photo',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
            this.gallery = typeof options.gallery === 'undefined' ? true : options.gallery;
        },
        serializeData: function() {
            var data = {};
            data = this.model.toJSON();
            data['permissions'] = this.permissions.toJSON();
            data.gallery = this.gallery;
            return data;
        },
        onRender: function() {
            // $('.stash-sell', this.el).attr('data-collectible-user-id', this.model.get('id'));
            // $('.remove-from-stash', this.el).attr('data-collectible-user-id', this.model.get('id'));
            $(this.el).data('id', this.model.get('id'));
        }
    });
});