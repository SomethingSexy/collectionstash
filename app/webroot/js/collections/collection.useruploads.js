(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['backbone', 'models/model.userupload'], factory);
    } else {
        // Browser globals
        root.UserUploadsCollection = factory(root.Backbone, root.UserUploadModel);
    }
}(this, function(backbone, UserUploadModel) {

    return Backbone.Collection.extend({
        model: UserUploadModel
    });

}));