define(['require', 'marionette', 'text!templates/app/common/carousel.mustache', 'text!templates/app/user/profile/sale.table.empty.mustache', 'views/app/home/view.collectible', 'mustache', 'marionette.mustache'], function(require, Marionette, template, emptyTemplate, CollectibleView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate,
        tagName: 'tr'
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemViewContainer: ".thumbnails",
        emptyView: NoItemsView,
        itemView: CollectibleView,
        serializeData: function() {
            var data = {
                showPagination: this.collection.state.totalPages > 1
            };
            return data;
        },    
        _initialEvents: function() {
            this.listenTo(this.collection, "sync", this.render);
        },
        events: {
            'click ._sort': 'sort'
        },
        onRender: function() {
            var self = this;
            // $('._pagination', this.el).pagination({
            //     items: this.collection.state.totalRecords,
            //     itemsOnPage: this.collection.state.pageSize,
            //     cssStyle: 'pagination',
            //     onPageClick: function(pageNumber, event) {
            //         self.collection.getPage(pageNumber);
            //     }
            // });
        }
    });
});