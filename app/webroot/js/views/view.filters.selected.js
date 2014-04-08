var SelectedFiltersView = Backbone.View.extend({
    el: '#fancy-filters-selected',
    events: {
        'click [data-role="remove"]': 'removeFilter'
    },
    initialize: function(options) {
        this.hounds = {};
    },
    render: function() {
        var self = this;

        return this;
    },
    removeFilter: function(event) {
        event.preventDefault();
        // grab the current url
        var uri = new URI(document.URL),
            $filter = $(event.currentTarget).closest('.filter'),
            type = $filter.data('type'),
            id = $filter.data('id').toString();

        var typeFilters = uri.query(true)[type];
        var arrayFilters = [];
        if (typeFilters && typeFilters.length > 1) {
            arrayFilters = typeFilters.split(',');
        }

        arrayFilters = _.without(arrayFilters, id);

        if (arrayFilters.length > 0) {
            var search = {};
            search[type] = arrayFilters.join(',');

            uri.setSearch(search);
        } else {
            uri.removeSearch(type);
        }

        window.location.href = uri.href();
    }
});