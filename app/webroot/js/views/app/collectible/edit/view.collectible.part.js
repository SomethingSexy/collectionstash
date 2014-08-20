define(['marionette', 'text!templates/app/collectible/edit/collectible.part.mustache', 'mustache', 'underscore', 'marionette.mustache'], function(Marionette, template, mustache, _) {
    return Marionette.ItemView.extend({
        template: template,
        tagName: "div",
        className: 'row spacer attribute',
        events: {
            'click .edit-attribute-photo-link': 'addPhoto',
            'click .edit-attribute-link': 'edit',
            'click .remove-duplicate-attribute': 'duplicate'
        },
        initialize: function(options) {
            this.status = options.status;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
            this.collectible = options.collectible;
            this.scales = options.scales;
            // this.model.on('change', this.render, this);
        },
        serializeData: function() {
            var data = this.model.toJSON();
            var status = this.status.toJSON();
            // If the status is draft or submitted, don't allow
            // remove the of the part...once the admin piece
            // comes in update to allow if admin
            // Remove Collectible Attribute will remove the attribute
            // if it is the only one, so we don't need remove really
            if (status.status.id === '1' || status.status.id === '2' && !adminMode) {
                data.allowRemoveAttribute = false;
            } else {
                data.allowRemoveAttribute = true;
            }

            data.part = this.model.part.toJSON();
            // we need to build out some stuff for editing and removing
            // var attribute = {};
            // attribute.categoryId = data.Attribute.AttributeCategory.id;
            // attribute.categoryName = data.Attribute.AttributeCategory.path_name;
            // attribute.name = data.Attribute.name;
            // attribute.description = data.Attribute.description;
            // if (data.Part.scale_id) {
            //     attribute.scaleId = data.Attribute['scale_id'];
            // } else {
            //     attribute.scaleId = null;
            // }
            // if (data.Attribute['manufacture_id']) {
            //     attribute.manufacturerId = data.Attribute['manufacture_id'];
            // } else {
            //     attribute.manufacturerId = null;
            // }
            // if (data.Attribute['artist_id']) {
            //     attribute.artistId = data.Attribute['artist_id'];
            // } else {
            //     attribute.artistId = null;
            // }
            // attribute.id = data.Attribute.id;
            // var attributeCollectible = {};
            // attributeCollectible.id = data.id;
            // attributeCollectible.attributeId = data['attribute_id'];
            // attributeCollectible.categoryName = data.Attribute.AttributeCategory['path_name'];
            // attributeCollectible.count = data.count;
            // attributeCollectible.attributeCollectibleTypeId = data['attribute_collectible_type_id'];
            data.uploadDirectory = uploadDirectory;
            data.collectible = this.collectible.toJSON();

            if (data.collectible.custom) {
                if (data.attribute_collectible_type === 'added') {
                    data.attribute_collectible_type_label = 'Owned';
                } else if (data.attribute_collectible_type === 'wanted') {
                    data.attribute_collectible_type_label = 'Wanted';
                } else if (data.attribute_collectible_type === 'preorder') {
                    data.attribute_collectible_type_label = 'Preordered';
                } else {
                    attribute_collectible_type_label = '';
                }
            }

            return data;
        },
        onRender: function() {
            var self = this;


            // dust.render(this.template, attributeModel, function(error, output) {
            //     $(self.el).html(output);
            // });
            // $(self.el).attr('data-id', this.model.toJSON().Attribute.id).attr('data-attribute-collectible-id', this.model.toJSON().id).attr('data-attached', true);

            $('span.popup', self.el).popover({
                placement: 'bottom',
                html: 'true',
                template: '<div class="popover" onmouseover="$(this).mouseleave(function() {$(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
            }).click(function(e) {
                e.preventDefault();
            }).mouseenter(function(e) {
                $(this).popover('show');
            });
            // $(self.el).attr('data-attribute', JSON.stringify(attribute));
            // $(self.el).attr('data-attribute-collectible', JSON.stringify(attributeCollectible));
            return this;
        },
        addPhoto: function() {
            var self = this;
            var attribute = self.model.toJSON();
            // Hmmm, well, it might make sense at some point
            // to merge the upload stuff, directly into the attribute
            // model data but the plugin requires it's data in a special
            //format, so for now we are going to fetch each time we
            // need it, oh well.
            $.blockUI({
                message: 'Loading...',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: ' #F1F1F1',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    color: '#222',
                    background: 'none repeat scroll 0 0 #F1F1F',
                    'border-radius': '5px 5px 5px 5px',
                    'box-shadow': '0 0 10px rgba(0, 0, 0, 0.5)'
                }
            });
            var uploads = new AttributeUploads([], {
                'id': attribute.Attribute.id
            });
            uploads.fetch({
                success: function() {
                    if (self.photoEditView) {
                        self.photoEditView.remove();
                    }
                    self.photoEditView = new AttributePhotoView({
                        collection: uploads,
                        model: self.model
                    });
                    $.unblockUI();
                    $('body').append(self.photoEditView.render().el);
                    $('#attribute-upload-dialog', 'body').modal({
                        backdrop: 'static'
                    });
                    $('#attribute-upload-dialog', 'body').on('hidden.bs.modal', function() {
                        self.photoEditView.remove();
                        self.model.fetch();
                    });
                }
            });
        },
        edit: function() {
            var self = this;
            this.renderEditView();
            $('#attribute-update-dialog').modal();
            $('#attribute-update-dialog', 'body').on('hidden.bs.modal', function() {
                self.addEditView.remove();
            });
        },
        renderEditView: function(attribute) {
            var self = this;
            if (this.addEditView) {
                this.addEditView.remove();
            }
            this.addEditView = new AddAttributeView({
                model: this.model,
                manufacturers: this.manufacturers,
                artists: this.artists,
                scales: this.scales,
                collectible: this.collectible,
                type: 'edit'
            });
            this.addEditView.on('view:category:select', function() {
                this.addEditView.remove();
                this.addEditView = new AttributeCategoryView({
                    model: this.model
                });
                this.addEditView.on('change:attribute_category_id', function() {
                    this.renderEditView();
                }, this);
                $('.modal-body', '#attribute-update-dialog').html(this.addEditView.render().el);
                $('.modal-footer .save', '#attribute-update-dialog').hide();
            }, this);
            $('.modal-body', '#attribute-update-dialog').html(this.addEditView.render().el);
            $('.modal-footer .save', '#attribute-update-dialog').show();
        },
        duplicate: function() {
            var self = this;
            if (this.duplicateView) {
                this.duplicateView.remove();
            }
            this.duplicateView = new AttributeDuplicateView({
                artists: this.artists,
                manufacturers: this.manufacturers,
                categories: this.categories,
                // this will be the attribute that is being replaced
                model: this.model
            });
            // I already have a change event that will rerender this
            // guy when the attribute changes so I don't need anything else
            // I just need to trigger a hidden event on the modal
            // which will then delete the view, I think that is all I need to do
            $('body').append(this.duplicateView.render().el);
            // the view that is being rendered, shouldn't know it is a
            // modal
            $('#attributeDuplicateModal', 'body').modal({
                backdrop: 'static'
            });
            $('#attributeDuplicateModal', 'body').on('hidden.bs.modal', function() {
                self.duplicateView.remove();
            });
            this.duplicateView.on('modal:close', function() {
                $('#attributeDuplicateModal', 'body').modal('hide');
            }, this);
        }
    });
});