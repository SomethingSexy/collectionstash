define(['require', 'marionette', 'text!templates/app/common/filter.mustache', 'mustache', 'uri', 'marionette.mustache', 'bootstrap', 'select2'], function(require, Marionette, template, mustache, URI) {

    return Marionette.ItemView.extend({
        className: 'filter-btn',
        template: template,
        events: {
            'change select': 'change',
            'click .remove-from-stash': 'removeFromStash'
        },
        onRender: function() {
            var self = this;
            // setup popovers
            $('.filter', this.el).popover();

            // setup hounds

            var type = $(this).data('type');
            $('select', this.el).select2({
                width: '250px'
            });
        },
        change: function(event) {
            if (event.val && event.val !== '') {
                var uri = new URI(document.URL),
                    type = $(event.target).data('type'),
                    multiple = $(event.target).data('multiple');
                var filterValue = event.val;
                if (multiple === 1) {
                    filterValue = $('select', this.el).val();
                }

                this.trigger('filter:selected', type, filterValue);
            }
        }
    });
});