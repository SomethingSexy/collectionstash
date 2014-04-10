// $(function() {
//     $('body').on('click', function(e) {
//         $('[data-toggle="popover"]').each(function() {
//             //the 'is' for buttons that trigger popups
//             //the 'has' for icons within a button that triggers a popup
//             if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
//                 $(this).popover('hide');
//             }
//         });
//     });
// });

var FiltersView = Backbone.View.extend({
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

        var type = $(this).data('type');
        $('select', this.el).select2({
            width: 'element'
        }).on('change', function(event) {
            if (event.val && event.val !== '') {
                var uri = new URI(document.URL),
                    type = $(event.target).data('type'),
                    multiple = $(event.target).data('multiple');
                var filterValue = event.val;
                if (uri.query(true)[type] && multiple === 1) {
                    filterValue = uri.query(true)[type] + ',' + event.val;
                }

                var search = {};
                search[type] = filterValue;

                uri.setSearch(search);

                window.location.href = uri.href();
            }
        });


        return this;
    }
});