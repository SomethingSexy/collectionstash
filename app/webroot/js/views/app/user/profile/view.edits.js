define(['require', 'marionette', 'text!templates/app/user/profile/edits.mustache', 'text!templates/app/user/profile/edits.empty.mustache', 'views/app/user/profile/view.edit', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, template, emptyTemplate, WorkView, mustache) {

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
        _initialEvents: function() {
            this.listenTo(this.collection, "sync", this._renderChildren);
        },
        sorts: {
            'Collectible.status_id': -1,
            'created': -1,
            'id': -1,
            'name': -1,
            'Collectible.collectibletype_id': -1
        },
        events: {
            'click ._sort': 'sort'
        },
        initialize: function(options) {
            this.permissions = options.permissions;
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