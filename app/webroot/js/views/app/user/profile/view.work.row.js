define(['require', 'marionette', 'text!templates/app/user/profile/work.row.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        tagName: 'tr',
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        modelEvents: {
            "change": "render"
        },
        events: {
            'click': 'selectCollectible'
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.type = 'Mass-produced';
            if (data.original) {
                data.type = 'Original';
            } else if (data.custom) {
                data.type = 'Custom';
            } else {

            }

            return data;
        },
        selectCollectible: function(event) {
            event.preventDefault();
            var collectible = this.model.toJSON();
            if (collectible.Status.id == 1) {
                window.location.href = '/collectibles/edit/' + collectible.id;
            } else if (collectible.Status.id == 2) {
                window.location.href = '/collectibles/view/' + collectible.id;
            } else {
                window.location.href = '/collectibles/view/' + collectible.id;
            }
        }
    });
});