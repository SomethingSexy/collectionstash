define(['backbone', 'models/model.collectible.part'], function(Backbone, CollectiblePartModel) {

    return Backbone.Collection.extend({
        url: 'attributes_collectibles/parts',
        model: CollectiblePartModel
    });
});