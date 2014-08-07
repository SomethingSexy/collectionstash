define(['marionette', 'text!templates/app/collectible/edit/tag.mustache', 'mustache', 'underscore', 'marionette.mustache'], function(Marionette, template, mustache, _) {
    return Marionette.ItemView.extend({
        template: template,
        className: "list-group-item",
        tagName: 'li',
        events: {
            'click .remove-tag': 'removeTag'
        },
        removeTag: function() {
            this.model.destroy({
                wait: true,
                success: function(model, response) {
                    var message = "The tag has been successfully deleted!";
                    if (response.response.data) {
                        if (response.response.data.hasOwnProperty('isEdit')) {
                            if (response.response.data.isEdit) {
                                message = "Your edit has been successfully submitted!";
                            }
                        }
                    }
                    $.blockUI({
                        message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
                        showOverlay: false,
                        css: {
                            top: '100px',
                            'background-color': '#DDFADE',
                            border: '1px solid #93C49F',
                            'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                            'border-radius': '4px 4px 4px 4px',
                            color: '#333333',
                            'margin-bottom': '20px',
                            padding: '8px 35px 8px 14px',
                            'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                            'z-index': 999999
                        },
                        timeout: 2000
                    });
                },
            });
        }
    });
});