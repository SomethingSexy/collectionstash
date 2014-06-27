define(['require', 'marionette', 'text!templates/app/user/profile/history.mustache', 'views/app/user/profile/view.history.row', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, template, CollectibleView, mustache) {

    return Marionette.CompositeView.extend({
        className: 'table-responsive',
        template: template,
        itemViewContainer: "tbody",
        itemView: CollectibleView,
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        itemViewOptions: function(model, index) {
            return {
                permissions: this.permissions
            };
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.renderMore);
            this.listenTo(this.collection, "sync", this.renderMore);
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