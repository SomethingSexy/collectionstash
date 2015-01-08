(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['backbone'], factory);
    } else {
        // Browser globals
        root.UserUploadModel = factory(root.Backbone);
    }
}(this, function(backbone, PartsCollection) {

    return Backbone.Model.extend({
        url: function() {
            return '/user_uploads/upload/' + this.id;
        }
    });

}));