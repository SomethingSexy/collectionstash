define(['backbone'], function(Backbone) {

    return Backbone.Model.extend({
        url: '/attributes/part',
        parse: function(response) {

        }
    });
});