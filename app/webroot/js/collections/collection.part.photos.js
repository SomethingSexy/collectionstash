define(['backbone', 'models/model.part.photo'], function(Backbone, PartPhotoModel) {

    return Backbone.Collection.extend({
        url: 'attributes_collectibles/view',
        model: PartPhotoModel
    });
});