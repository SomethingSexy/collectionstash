define(['backbone', 'backbone.pageable', 'models/model.company'], function(Backbone, pageable, CompanyModel) {
    return Backbone.PageableCollection.extend({
        model: CompanyModel,
        initialize: function(models, props) {
            this.url = "/manufacturers/manufacturers/";
        },
        mode: "client",
        state: {
            pageSize: 25,
            query: {}
        }
    });
});