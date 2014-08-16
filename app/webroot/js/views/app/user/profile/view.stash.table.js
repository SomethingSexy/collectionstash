define(['require', 'marionette', 'views/app/user/profile/view.stash', 'text!templates/app/user/profile/stash.table.mustache', 'text!templates/app/user/profile/stash.table.empty.mustache', 'views/app/user/profile/view.stash.collectible.row', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, StashView, template, emptyTemplate, CollectibleView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate,
        tagName: 'tr'
    });


    return StashView.extend({
        className: 'table-responsive',
        template: template,
        itemViewContainer: "tbody",
        itemView: CollectibleView,
        emptyView: NoItemsView,
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.renderMore);
            this.listenTo(this.collection, "sync", this.renderMore);
        },
        serializeData: function() {
            var data = {
                showPagination: this.collection.state.totalPages > 1
            };
            return data;
        },
        renderMore: function() {
            var self = this;
            var ItemView;

            $(this.itemViewContainer, this.el).empty();

            this.startBuffering();
            this.collection.each(function(item, index) {
                ItemView = this.getItemView(item);
                this.addItemView(item, ItemView, index);
            }, this);
            this.endBuffering();

            $('._pagination', this.el).pagination('updateItems', this.collection.state.totalRecords);
        },
        onItemRemoved: function() {

        },
        onRender: function() {
            var self = this;
            $('._pagination', this.el).pagination({
                items: this.collection.state.totalRecords,
                itemsOnPage: this.collection.state.pageSize,
                cssStyle: 'pagination',
                onPageClick: function(pageNumber, event) {
                    self.collection.getPage(pageNumber);
                }
            });
        }
    });
});