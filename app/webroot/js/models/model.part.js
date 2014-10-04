define(['backbone', 'collections/collection.part.photos'], function(Backbone, CollectionPartPhotos) {
    return Backbone.Model.extend({
        url: '/attributes/part',
        parse: function(response) {
            if (!this.photos) {
                this.photos = new CollectionPartPhotos(response.AttributeUploads, {
                    parse: true
                });
            } else {
                this.photos.set(response.AttributeUploads);
            }
            delete response.AttributeUploads;
            return response;
        }
    });
});