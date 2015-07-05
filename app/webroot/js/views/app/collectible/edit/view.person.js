define(['marionette', 'text!templates/app/collectible/edit/person.mustache', 'mustache', 'underscore', 'views/common/growl', 'marionette.mustache'], function(Marionette, template, mustache, _, growl) {
    var PersonView = Marionette.ItemView.extend({
        template: template,
        className: "list-group-item",
        tagName: 'li',
        events: {
            'click .remove-artist': 'removeArtist'
        },
        removeArtist: function() {
            this.model.destroy({
                wait: true,
                success: function(model, response) {
                    var message = "The artist has been successfully deleted!";
                    if (response.response.data) {
                        if (response.response.data.hasOwnProperty('isEdit')) {
                            if (response.response.data.isEdit) {
                                message = "Your edit has been successfully submitted!";
                            }
                        }
                    }
                    growl.onSuccess(message);
                }
            });
        }
    });
    return PersonView;
});