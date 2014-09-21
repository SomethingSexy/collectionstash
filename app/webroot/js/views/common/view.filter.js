define(['require', 'underscore', 'marionette', 'text!templates/app/common/filter.mustache', 'mustache', 'uri', 'marionette.mustache', 'bootstrap', 'select2'], function(require, _, Marionette, template, mustache, URI) {

    return Marionette.ItemView.extend({
        className: 'filter-btn col-md-3',
        template: template,
        events: {
            'change select': 'change',
        },
        onRender: function() {
            var self = this;
            // setup popovers
            $('.filter', this.el).popover();
            var type = $(this).data('type');
            var selectProps = {
                width: '250px',
                allowClear: true
            };

            if (this.model.get('placeholder')) {
                selectProps['placeholder'] = this.model.get('placeholder');
            }

            if (this.model.get('selected')) {
                _.each(this.model.get('selected'), function(value) {
                    $('select option[value=' + value + ']', self.el).prop('selected', 'selected');
                });
            }

            $('select', this.el).select2(selectProps);
        },
        change: function(event) {
            if (event.val && event.val !== '') {
                var uri = new URI(document.URL),
                    type = $(event.target).data('type'),
                    multiple = $(event.target).data('multiple');
                var filterValue = event.val;
                if (multiple) {
                    filterValue = $('select', this.el).val();
                }

                this.model.set('selected', filterValue, {
                    silent: true
                });

                this.trigger('filter:selected', type, filterValue);
            }
        }
    });
});