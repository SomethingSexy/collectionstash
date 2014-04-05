// TODO: Need to make this configurable, or we have extending views 
$(function() {
    $('body').on('click', function(e) {
        $('[data-toggle="popover"]').each(function() {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
});

var FiltersView = Backbone.View.extend({
    //template : 'alert',
    el: '#fancy-filters',
    events: {

    },
    initialize: function(options) {
        this.hounds = {};
    },
    render: function() {
        var self = this;
        // setup popovers
        $('.filter', this.el).popover();

        // setup hounds
        $('.filter', this.el).each(function() {
            var type = $(this).data('type'),
                sourceKey = $(this).data('source-key'),
                source = $(this).data('source');

            self.hounds[type] = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: source + '?query=%QUERY',
                    filter: function(list) {
                        return $.map(list, function(item) {
                            return {
                                value: item[sourceKey],
                                id: item.id
                            };
                        });
                    }
                }
            });

            self.hounds[type].initialize();
        });

        $('.filter', this.el).on('shown.bs.popover', function() {
            var type = $(this).data('type');
            $('.typeahead-container .typeahead', $(this).data("bs.popover").$tip).typeahead({
                hint: true,
                highlight: true,
                minLength: 1,
                autoselect: true
            }, {
                name: type,
                displayKey: 'value',
                source: self.hounds[type].ttAdapter()
            }).on('typeahead:selected', function(event, selected, obj) {
                var uri = new URI(document.URL);
                var filterValue = selected.id;
                if (uri.query(true)[type]) {
                    filterValue = uri.query(true)[type] + ',' + selected.id;
                }

                var search = {};
                search[type] = filterValue;

                uri.setSearch(search);

                window.location.href = uri.href();
            });
        });

        return this;
    }
});