define(['marionette', 'text!templates/app/collectible/edit/collectible.search.mustache', 'text!templates/app/collectible/edit/collectible.search.item.mustache', 'text!templates/app/collectible/edit/collectible.search.item.part.mustache', 'text!templates/app/collectible/edit/collectible.search.empty.mustache', 'mustache', 'marionette.mustache', 'simplePagination'], function(Marionette, template, templateItem, templatePart, emptyTemplate) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });


    var PartView = Marionette.ItemView.extend({
        className: 'spacer',
        template: templatePart,
        events: {
            'click ._part': 'selectPart'
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.part = this.model.part.toJSON();
            return data;
        },
        selectPart: function() {
            this.trigger('part:selected', this.model);
        }
    });

    var CollectibleView = Marionette.CompositeView.extend({
        className: 'spacer',
        template: templateItem,
        itemView: PartView,
        itemViewContainer: "._parts",
        childEvents: {
            'part:selected': function(model) {
                this.trigger('part:selected', model);
            }
        },
        initialize: function() {
            this.collection = this.model.parts;
        }
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CollectibleView,
        itemViewContainer: "._collectibles",
        emptyView: NoItemsView,
        _initialEvents: function() {
            this.listenTo(this.collection, "sync", this._renderChildren);
        },
        childEvents: {
            'part:selected': function(model) {
                this.trigger('part:selected', model);
            }
        },
        initialize: function() {
            this.firstSearch = true;
        },
        events: {
            'click button._search': 'searchCollectible',
            'click ._cancel': 'cancelAdd'
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
        searchCollectible: function(event) {
            // TODO: Update to not allow searching
            // unless something was entered
            event.preventDefault();
            var query = $('.search-query', this.el).val();
            this.collection.setQuery(query);
            this.collection.fetch();
        },
        cancelAdd: function() {
            this.trigger('cancel');
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