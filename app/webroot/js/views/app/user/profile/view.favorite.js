define(function(require) {
    var Marionette = require('marionette'),
        template = require('text!templates/app/user/profile/favorite.mustache'),
        mustache = require('mustache');
    require('marionette.mustache');
    return Marionette.ItemView.extend({
        className: 'tile photo',
        template: template,
        events: {
            'click ._toggle': 'toggle'
        },
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
        toggle: function() {
            // for now just remove, if toggle is useful
            // add later
            this.model.destroy({
                silent: true
            });
            this.el.querySelector('.tile-actions').style.display = 'none';
            this.el.querySelector('img').style.opacity = '0.25';
        }
    });
});