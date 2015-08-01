define(['backbone', 'backbone.pageable', 'models/model.company'], function(Backbone, pageable, CompanyModel) {
    var CompaniesCollection = Backbone.PageableCollection.extend({
        model: CompanyModel,
        initialize: function(models, props) {
            this.url = "/manufacturers/manufacturers/";
        },
        mode: "client",
        state: {
            pageSize: 25,
            query: {}
        },
        byTitle: function(title) {
            var filtered = this.fullCollection.filter(function(model) {
                return model.get('title').toLowerCase().indexOf(title.toLowerCase(), 0) === 0;
            });

            return new CompaniesCollection(filtered);
        }
    });
    return CompaniesCollection;
});