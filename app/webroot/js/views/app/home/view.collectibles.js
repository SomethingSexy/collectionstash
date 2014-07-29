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
        className: 'carousel slide',
        serializeData: function() {

            // {@gt key="{paginator.currentPage}" value="{paginator.firstPage}" type="number"}

            // {/gt}         

            // {@ne key="{paginator.total}" value="0"}
            //     {@ne key="{paginator.totalPages}" value="{paginator.currentPage}" type="number"}

            //     {/ne}
            // {/ne}   
            var data = {
                showPrevious: this.collection.state.currentPage > 1,
                showNext: (this.collection.state.total !== 0 && this.collection.state.total !== this.collection.state.currentPage)
            };
            return data;
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "sync", this.render);
        },
        events: {
            'click .next': 'next',
            'click .previous': 'previous'
        },
        next: function(event) {
            this.collection.getNextPage();
        },
        previous: function(event) {
            this.collection.getPreviousPage();
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