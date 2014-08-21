define(['backbone', 'models/model.part', 'backbone.trackit'], function(Backbone, PartModel) {

    return Backbone.Model.extend({
        urlRoot: '/attributes_collectibles/part',
        parse: function(response) {
            if (!this.part) {
                this.part = new PartModel(response.Attribute);
            } else {
                this.part.set(response.Attribute);
            }

            delete response.Attribute;

            return response;
        }
    });
});