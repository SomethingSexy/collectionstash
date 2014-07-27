(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['backbone'], factory);
    } else {
        // Browser globals
        root.Status = factory(root.Backbone);
    }
}(this, function(backbone) {
    var Status = Backbone.Model.extend({
        urlRoot: '/collectibles/status',
        parse: function(response, xhr) {
            return response.response.data;
        }
    });
    return Status;
}));