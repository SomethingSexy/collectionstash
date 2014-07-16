define(['require', 'marionette', 'text!templates/app/user/profile/submissions.mustache', 'text!templates/app/user/profile/submission.empty.mustache', 'views/app/user/profile/view.submission', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, template, emptyTemplate, WorkView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate,
        tagName: 'tr'
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemViewContainer: "tbody",
        emptyView: NoItemsView,
        itemView: WorkView,
        itemViewOptions: function(model, index) {
            return {
                permissions: this.permissions
            };
        },
        serializeData: function() {
            var data = {
                showPagination: this.collection.state.totalPages > 1
            };
            return data;
        },
        sorts: {
            'Collectible.status_id': -1,
            'created': -1,
            'id': -1,
            'name': -1,
            'Collectible.collectibletype_id': -1
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.render);
            this.listenTo(this.collection, "sync", this.renderMore);
        },
        events: {
            'click ._sort': 'sort'
        },
        initialize: function(options) {
            this.permissions = options.permissions;
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
        },
        sort: function(event) {
            var sort = $(event.currentTarget).data('sort');
            this.sorts[sort] = this.sorts[sort] === -1 ? 1 : -1;
            this.collection.setSorting(sort, this.sorts[sort]);
            this.collection.fetch();
        }
    });
});