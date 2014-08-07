define(['backbone'], function(Backbone) {
    var SeriesModel = Backbone.Model.extend({
        url: function() {
            var mode = "";
            if (this.get('mode')) {
                mode = "/" + this.get('mode');
            }
            return '/series/get/' + this.id + mode;
        }
    });

    return SeriesModel;
});