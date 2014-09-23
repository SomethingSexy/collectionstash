define(['marionette', 'text!templates/app/collectible/edit/collectible.search.mustache', 'text!templates/app/collectible/edit/collectible.search.item.mustache', 'text!templates/app/collectible/edit/collectible.search.empty.mustache', 'mustache', 'marionette.mustache', 'simplePagination'], function(Marionette, template, templateItem, emptyTemplate) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });

    var CollectibleView = Marionette.ItemView.extend({
        className: 'spacer',
        template: templateItem
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CollectibleView,
        itemViewContainer: "._collectibles",
        emptyView: NoItemsView,
        _initialEvents: function() {
            this.listenTo(this.collection, "sync", this._renderChildren);
        },
        initialize: function() {
            this.firstSearch = true;
        },
        events: {
            'click button._search': 'searchCollectible',
            'click li.attribute': 'selectAttribute'
        },
        searchCollectible: function(event) {
            // TODO: Update to not allow searching
            // unless something was entered
            event.preventDefault();
            var query = $('.search-query', this.el).val();
            this.collection.setQuery(query);
            this.collection.fetch();
        },
        serializeData: function() {
            var data = {
                showPagination: this.collection.state.totalPages > 1
            };
            return data;
        },
        onRender: function() {
            var self = this;

        },
        onCompositeCollectionRendered: function() {
            var self = this;
            if (this.collection.length > 0) {
                $('._pagination', this.el).css('display', 'inline-block');
            } else {
                $('._pagination', this.el).css('display', 'none');
            }

            if (this.firstSearch) {
                $('._pagination', this.el).pagination({
                    items: this.collection.state.totalRecords,
                    itemsOnPage: this.collection.state.pageSize,
                    cssStyle: 'pagination',
                    onPageClick: function(pageNumber, event) {
                        self.collection.getPage(pageNumber);
                    }
                });
                this.firstSearch = false;
            } else {
                $('._pagination', this.el).pagination('updateItems', this.collection.state.totalRecords);
            }
        },
        selectAttribute: function(event) {
            event.preventDefault();
            var attribute = JSON.parse($(event.currentTarget).attr('data-attribute'));
            this.model.clear({
                silent: true
            });
            this.model.set(attribute);
        }
    });
});