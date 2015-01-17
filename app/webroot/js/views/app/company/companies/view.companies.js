define(function(require) {

    var Marionette = require('marionette'),
        template = require('text!templates/app/company/companies/companies.mustache'),
        emptyTemplate = require('text!templates/app/company/companies/companies.empty.mustache'),
        CompanyView = require('views/app/company/companies/view.company'),
        mustache = require('mustache');
    require('marionette.mustache');
    require('simplePagination');

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });

    return Marionette.CompositeView.extend({
        className: '',
        template: template,
        itemViewContainer: "._companies",
        emptyView: NoItemsView,
        itemView: CompanyView,
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
            "stash:listing:edit": function(event, view, id) {
                this.trigger('stash:listing:edit', id);
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