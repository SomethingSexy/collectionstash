define(['backbone'], function(Backbone) {
    var Brands = Backbone.Collection.extend({
        comparator: function(brand) {
            return brand.get("License").name.toLowerCase();
        }
    });

    return Brands;
});