define(['require', 'marionette', 'text!templates/app/home/carousel.item.mustache', 'mustache', 'underscore', 'marionette.mustache'], function(require, Marionette, template, mustache, _) {

    var nophoto = '/img/no-photo.png';
    return Marionette.ItemView.extend({
        className: 'col-sm-3',
        template: template,
        serializeData: function() {
            var data = this.model.toJSON();
            var url = nophoto;
            if (data.CollectiblesUpload) {
                _.each(data.CollectiblesUpload, function(upload) {
                    if (upload.primary) {
                        url = '/' + uploadDirectory + '/' + upload.Upload.name;
                    }
                });
            }

            return {
                id: data.id,
                url: url,

            };
        }
    });
});