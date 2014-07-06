define(['require', 'marionette', 'text!templates/app/user/profile/sale.table.mustache', 'text!templates/app/user/profile/sale.table.empty.mustache', 'views/app/user/profile/view.sale.row', 'mustache', 'marionette.mustache', 'simplePagination'], function(require, Marionette, template, emptyTemplate, CollectibleView, mustache) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate,
        tagName: 'tr'
    });

    return Marionette.CompositeView.extend({
        className: 'table-responsive',
        template: template,
        itemViewContainer: "tbody",
        emptyView: NoItemsView,
        itemView: CollectibleView,
        sorts: {
            'active': -1,
            'created': -1,
            'Collectible.average_price': -1
        },
        initialize: function(options) {
            this.permissions = options.permissions;
        },
        itemViewOptions: function(model, index) {
            return {
                permissions: this.permissions
            };
        },
        itemEvents: {
            "stash:mark:sold": function(event, view, id) {
                this.trigger('stash:mark:sold', id);
            },
            "stash:remove": function(event, view, id) {
                this.trigger('stash:remove', id);
            },
            "stash:sell": function(event, view, id) {
                this.trigger('stash:sell', id);
            }
        },
        serializeData: function() {
            var data = {
                showPagination: this.collection.state.totalPages > 1
            };
            return data;
        },    
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.renderMore);
            this.listenTo(this.collection, "sync", this.renderMore);
        },
        events: {
            'click ._sort': 'sort'
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