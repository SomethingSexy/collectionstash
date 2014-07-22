define(['require', 'marionette', 'text!templates/app/user/profile/edit.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        tagName: 'tr',
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        modelEvents: {
            "change": "render"
        },
        serializeData: function() {
            var data = this.model.toJSON();

            if (_.isArray(data.Edits) && data.Edits.length > 0) {
                var edit = data.Edits[0].edit_type;
                if (edit === 'CollectiblesUpload') {
                    data.edit_type = 'Collectible Photo';
                } else if (edit === 'Collectible') {
                    data.edit_type = 'Collectible';
                } else if (edit === 'Attribute') {
                    data.edit_type = 'Part';
                } else if (edit === 'AttributesCollectible') {
                    data.edit_type = 'Collectible Part';
                } else if (edit === 'Upload') {
                    data.edit_type = 'Collectible Photo';
                } else if (edit === 'CollectiblesTag') {
                    data.edit_type = 'Collectible Tag';
                } else if (edit === 'Tag') {
                    data.edit_type = 'Tag';
                } else if (edit === 'ArtistsCollectible') {
                    data.edit_type = 'Collectible Artist';
                } else if (edit === 'AttributesUpload') {
                    data.edit_type = 'Part Photo';
                }

            }

            return data;
        }
    });
});