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

        this.manufacturerHound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/manufactures/data?query=%QUERY',
                filter: function(list) {
                    return $.map(list, function(manufacturer) {
                        return {
                            value: manufacturer
                        };
                    });
                }
            }
        });
        this.manufacturerHound.initialize();
    },
    render: function() {
        var self = this;
        var manPopover = $('.filter.manufacturer', this.el).popover();

        $('.filter', this.el).on('shown.bs.popover', function() {
            $('.manufacturer-typeahead .typeahead', manPopover.data("bs.popover").$tip).typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'manufacturers',
                displayKey: 'value',
                source: self.manufacturerHound.ttAdapter()
            });
        });

        return this;
    }
});