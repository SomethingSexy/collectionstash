define(['marionette', 'text!templates/app/collectible/edit/collectible.search.mustache', 'mustache', 'marionette.mustache'], function(Marionette, template) {

    return Marionette.CollectionView.extend({
        template: template,
        events: {
            'click button.search': 'searchCollectible',
            'click a.page': 'gotoPage',
            'click a.next': 'next',
            'click a.previous': 'previous',
            'click li.attribute': 'selectAttribute'
        },
        initialize: function(options) {
            var self = this;
            this.collection.on('reset', function() {
                $('table', self.el).empty();
                _.each(this.collection.models, function(collectible) {
                    // This renders the collectible
                    $('table.collectibles', self.el).append(new AttributeSearchCollectibleView({
                        model: collectible
                    }).render().el);
                    // This renders the parts
                    $('table.collectibles', self.el).append(new AttributeSearchCollectibleAttrView({
                        model: collectible
                    }).render().el);
                });
                var pagesArray = [];
                // ya fuck you dust
                for (var i = 1; i <= this.collection.state.totalPages; i++) {
                    pagesArray.push(i);
                }
                var data = {
                    pages: pagesArray
                };
                if (this.collection.currentPage) {
                    data['paginator'] = {
                        currentPage: this.collection.state.currentPage,
                        firstPage: this.collection.state.firstPage,
                        perPage: this.collection.state.perPage,
                        totalPages: this.collection.state.totalPages,
                        total: this.collection.state.total
                    };
                } else {
                    data['paginator'] = this.collection.state;
                }
                dust.render('paging', data, function(error, output) {
                    $('.paging', self.el).html(output);
                });
                $(self.el).animate({
                    scrollTop: 0
                });
            }, this);
        },
        searchCollectible: function(event) {
            // TODO: Update to not allow searching
            // unless something was entered
            event.preventDefault();
            var query = $('.search-query', this.el).val();
            this.collection.searchQuery = query;
            this.collection.fetch();
        },
        gotoPage: function(e) {
            e.preventDefault();
            var page = $(e.target).text();
            this.collection.goTo(page);
        },
        next: function(e) {
            e.preventDefault();
            if (typeof this.collection.currentPage === 'undefined') {
                this.collection.currentPage = 1;
            }
            this.collection.requestNextPage();
        },
        previous: function(e) {
            e.preventDefault();
            this.collection.requestPreviousPage();
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