define(['marionette', 'text!templates/app/collectible/edit/collectible.part.mustache', 'mustache', 'underscore', 'marionette.mustache'], function(Marionette, template, mustache, _) {
    return Marionette.ItemView.extend({
        template: template,
        tagName: "div",
        className: 'row spacer attribute',
        events: {
            'click ._edit-part-photo': 'addPhoto',
            'click ._edit-part': 'edit',
            'click ._remove-part-duplicate': 'duplicate',
            'click ._edit-part-collectible': 'editCollectiblePart',
            'click ._remove-part': 'removePart'
        },
        initialize: function(options) {
            this.status = options.status;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
            this.collectible = options.collectible;
            this.scales = options.scales;
            // if the collectible part has changed or the actual part has changed, re-render
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model.part, 'change', this.render);
            this.listenTo(this.model.part.photos, 'add', this.render);
            this.listenTo(this.model.part.photos, 'remove', this.render);
        },
        serializeData: function() {
            var data = this.model.toJSON();
            var status = this.status.toJSON();
            // If the status is draft or submitted, don't allow
            // remove the of the part...once the admin piece
            // comes in update to allow if admin
            // Remove Collectible Attribute will remove the attribute
            // if it is the only one, so we don't need remove really
            if (status.status.id === '1' || status.status.id === '2') {
                data.allowRemoveAttribute = false;
            } else {
                data.allowRemoveAttribute = true;
            }

            // don't allow them to edit
            if (data.status_id === '2') {
                data.isNew = true;
            } else {
                if (data.isEdit || this.model.part.get('isEdit')) {
                    data.hasEdit = true;
                }
            }

            data.part = this.model.part.toJSON();
            data.part.photos = this.model.part.photos.toJSON();
            data.uploadDirectory = uploadDirectory;
            data.collectible = this.collectible.toJSON();
            if (data.collectible.custom) {
                if (data.attribute_collectible_type_id == 1) {
                    data.attribute_collectible_type_label = 'Owned';
                } else if (data.attribute_collectible_type_id == 2) {
                    data.attribute_collectible_type_label = 'Wanted';
                } else if (data.attribute_collectible_type_id == 3) {
                    data.attribute_collectible_type_label = 'Preordered';
                } else {
                    attribute_collectible_type_label = '';
                }
            }
            if (data.part.type === 'mass' || data.part.type === 'original') {
                if (data.part.manufacture_id) {
                    data.part._label = data.part.Manufacture.title;
                } else if (data.part.artist_id) {
                    data.part._label = data.part.Artist.name;
                } else {
                    data.part._label = 'Unknown';
                }
            } else if (data.part.type === 'custom') {
                data.part._label = data.part.User.username;
            } else if (data.part.type === 'generic') {
                data.part._label = 'Generic';
            }
            return data;
        },
        onRender: function() {
            var self = this;
            $('span.popup', self.el).popover({
                placement: 'bottom',
                html: 'true',
                template: '<div class="popover" onmouseover="$(this).mouseleave(function() {$(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
            }).click(function(e) {
                e.preventDefault();
            }).mouseenter(function(e) {
                $(this).popover('show');
            });
            return this;
        },
        editCollectiblePart: function() {
            this.trigger('edit:collectible:part', this.model);
        },
        edit: function() {
            this.trigger('edit:part', this.model.part);
        },
        removePart: function() {
            this.trigger('remove:part', this.model);
        },
        addPhoto: function() {
            this.trigger('edit:part:photo', this.model);
        },
        duplicate: function() {
            this.trigger('remove:part:duplicate', this.model);
        }
    });
});