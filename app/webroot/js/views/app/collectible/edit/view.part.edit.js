define(['require', 'underscore', 'backbone', 'marionette', 'text!templates/app/collectible/edit/part.edit.mustache', 'views/common/mixin.error', 'views/common/growl', 'mustache', 'marionette.mustache'], function(require, _, Backbone, Marionnette, template, ErrorMixin, growl) {
    var EditListing = Marionnette.ItemView.extend({
        template: template,
        events: {
            'click .save': 'save',
            'click .select-category': 'changeCategory',
            'click .category-container .item.name': 'selectCategory',
            'click .attribute-type': 'changeType'
        },
        initialize: function(options) {
            this.model.startTracking();
            this.manufacturers = options.manufacturers;
            this.artists = options.artists;
            this.scales = options.scales;
            this.collectible = options.collectible;
            if (this.model.isNew()) {
                // default the attribute to be a custom one
                if (this.collectible.get('custom')) {
                    this.model.set({
                        type: 'custom'
                    });
                } else if (this.collectible.get('original')) {
                    this.model.set({
                        type: 'original'
                    });
                } else {
                    this.model.set({
                        type: 'mass'
                    });
                }
            }
        },
        onRender: function() {
            if (this.model.get('manufacture_id')) {
                $('[name=manufacture_id] option[value=' + this.model.get('manufacture_id') + ']', this.el).attr('selected', 'selected');
            }
            if (this.model.get('artist_id')) {
                $('[name=artist_id] option[value=' + this.model.get('artist_id') + ']', this.el).attr('selected', 'selected');
            }
            if (this.model.get('scale_id')) {
                $('[name=scale_id] option[value=' + this.model.get('scale_id') + ']', this.el).attr('selected', 'selected');
            }
            var type = this.model.get('type');
            if (type) {
                $('[name=type][value=' + this.model.get('type') + ']', this.el).attr('checked', 'checked');
            }
            if (type === 'mass' || type === 'original') {
                $('._manufacturer', this.el).show();
                $('._artist', this.el).show();
            } else {
                $('._manufacturer', this.el).hide();
                $('._artist', this.el).hide();
            }
            this.errors = [];
            $(self.el).animate({
                scrollTop: 0
            });
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.manufacturers = this.manufacturers.toJSON();
            data.artists = this.artists.toJSON();
            data.scales = this.scales.toJSON();
            // we need this to determine how to render the view
            data.collectible = this.collectible.toJSON();
            if (this.model.isNew()) {
                data.showCount = true;
                data.showId = false;
            } else {
                data.showCount = false;
                data.showId = true;
            }
            if (data.type === 'mass') {
                data.isMass = true;
                data.showManufacturer = true;
            } else if (data.type === 'original') {
                data.isOriginal = true;
                data.showManufacturer = true;
            }
            data['uploadDirectory'] = uploadDirectory;
            return data;
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        changeCategory: function() {
            var $container = $('.category-container', this.el),
                isOpen = $('.category-container', this.el).data('open');
            if (isOpen) {
                $container.empty().data('open', false);
            } else {
                $container.html($('#attributes-category-tree').clone().html()).data('open', true);
            }
        },
        selectCategory: function(event) {
            var categoryId = $(event.currentTarget).attr('data-id');
            var categoryPath = $(event.currentTarget).attr('data-path-name');
            // eh just set to thie hidden field, since the save will handle
            // setting to the model
            $('[name=attribute_category_id]').val(categoryId);
            $('.select-category').text(categoryPath);
            $('.category-container', this.el).empty().data('open', false);
        },
        // This is for custom stuff, we will need to test this out still
        changeType: function(event) {
            var type = $('[name=type]:checked', this.el).val();
            if (type === 'mass' || type === 'original') {
                $('._manufacturer', this.el).show();
                $('._artist', this.el).show();
            } else {
                $('._manufacturer', this.el).hide();
                $('._artist', this.el).hide();
                $('[name=manufacture_id]', this.el).val('');
                $('[name=artist_id]', this.el).val('');
            }
        },
        save: function(event) {
            var self = this;
            var $button = $(event.currentTarget);
            $button.button('loading');
            var data = {
                attribute_category_id: $('[name=attribute_category_id]', this.el).val(),
                name: $('[name=name]', this.el).val(),
                description: $('[name=description]', this.el).val(),
                manufacture_id: $('[name=manufacture_id]', this.el).val(),
                artist_id: $('[name=artist_id]', this.el).val(),
                scale_id: $('[name=scale_id]', this.el).val()
            };
            // if this is a custom and not mass produced, then we have a type the user can select
            if (this.collectible.get('custom') && this.model.get('type') !== 'mass') {
                data.type = $('[name=type]:checked', this.el).val();
            }
            // if this is a new model, then we also need to grab the count 
            var isNew = false;
            if (this.model.isNew()) {
                isNew = true;
                data.count = parseInt($('[name=count]', this.el).val());
                data.collectible_id = this.collectible.get('id');
            }
            this.model.save(data, {
                wait: true,
                success: function(model, response, options) {
                    if (response.isEdit) {
                        growl.onSuccess('Your edit to the part has been successfully submitted!');
                    } else {
                        growl.onSuccess('Your edit has been successfully saved!');
                    }
                    $button.button('reset');
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        }
    });
    _.extend(EditListing.prototype, ErrorMixin);
    return EditListing;
});