define(['backbone', 'collections/collection.part.photos'], function(Backbone, CollectionPartPhotos) {
    return Backbone.Model.extend({
        url: '/attributes/part',
        parse: function(response) {
            if (!this.photos) {
                this.photos = new CollectionPartPhotos(response.AttributesUpload, {
                    parse: true
                });
            } else if (typeof response.AttributesUpload !== 'undefined') { // if they are defined, then set, otherwise ignore cause on update they might not get sent down
                this.photos.set(response.AttributesUpload);
            }
            delete response.AttributesUpload;
            return response;
        }
    });
});