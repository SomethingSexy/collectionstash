define(['marionette', 'text!templates/app/collectible/edit/collectible.parts.mustache', 'views/app/collectible/edit/view.collectible.part', 'mustache', 'underscore', 'marionette.mustache', 'bootstrap'], function(Marionette, template, PartView, mustache, _) {

    return Marionette.CompositeView.extend({
        template: template,
        className: "",
        itemViewContainer: "._parts-list",
        // emptyView: NoItemsView,
        itemView: PartView,
        itemEvents: {
            'edit:collectible:part': function(event, view, model) {
                this.trigger('edit:collectible:part', model);
            },
            'edit:part': function(event, view, model) {
                this.trigger('edit:part', model);
            },
            'remove:part': function(event, view, model) {
                this.trigger('remove:part', model);
            },
            'remove:part:duplicate': function(event, view, model) {
                this.trigger('remove:part:duplicate', model);
            }
        },
        itemViewOptions: function(model, index) {
            return {
                status: this.status,
                artists: this.artists,
                manufacturers: this.manufacturers,
                categories: this.categories,
                collectible: this.model,
                scales: this.scales
            };
        },
        initialize: function(options) {
            this.status = options.status;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
            this.collectible = options.collectible;
            this.scales = options.scales;
        },
        onRender: function() {
            var self = this;
            //TODO: this should be in a modal view for this guy
            $('.modal-footer .save', '#attribute-collectible-add-existing-dialog').click(function() {
                var url = '/attributes_collectibles/add.json';
                // If we are passing in an override admin or the options are set to admin mode
                $('#attribute-collectible-add-existing-dialog').find('form').ajaxSubmit({
                    // dataType identifies the expected content type of the server response
                    dataType: 'json',
                    url: url,
                    beforeSubmit: function(formData, jqForm, options) {
                        formData.push({
                            name: '_method',
                            type: 'text',
                            value: 'POST'
                        });
                        formData.push({
                            name: 'data[AttributesCollectible][collectible_id]',
                            type: 'text',
                            value: collectibleId
                        });
                        //TODO:
                        //self._clearFormErrors();
                    },
                    // success identifies the function to invoke when the server response
                    // has been received
                    success: function(responseText, statusText, xhr, $form) {
                        if (responseText.response.isSuccess) {
                            $('#attribute-collectible-add-existing-dialog').modal('hide');
                            var message = 'Part has been added!';
                            if (responseText.response.data.hasOwnProperty('isEdit')) {
                                if (responseText.response.data.isEdit) {
                                    message = 'Part has been submitted!';
                                }
                            }
                            $.blockUI({
                                message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
                                showOverlay: false,
                                css: {
                                    top: '100px',
                                    'background-color': '#DDFADE',
                                    border: '1px solid #93C49F',
                                    'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                                    'border-radius': '4px 4px 4px 4px',
                                    color: '#333333',
                                    'margin-bottom': '20px',
                                    padding: '8px 35px 8px 14px',
                                    'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                                    'z-index': 999999
                                },
                                timeout: 2000
                            });
                            var data = responseText.response.data;
                            if (data.isEdit === false) {
                                var attribute = new AttributesCollectibleModel(data);
                                self.collection.add(attribute);
                                $('.attributes-list', self.el).append(new AttributeView({
                                    model: attribute,
                                    status: self.status,
                                    artists: self.artists,
                                    manufacturers: self.manufacturers,
                                    categories: self.categories,
                                    collectible: self.collectible,
                                    scales: self.scales
                                }).render().el);
                            }
                        } else {
                            if (responseText.response.errors) {
                                $.each(responseText.response.errors, function(index, value) {
                                    if (value.inline) {
                                        $(':input[name="data[' + value.model + '][' + value.name + ']"]', $('#attribute-collectible-add-existing-dialog').find('form')).after('<div class="error-message">' + value.message + '</div>');
                                    } else {
                                        $('#attribute-collectible-add-existing-dialog').find('.component-message.error').children('span').text(value.message);
                                    }
                                });
                            }
                        }
                    }
                });
            });

            return this;
        },
        addExisting: function() {
            var self = this;
            var attribute = new AttributesCollectibleModel();
            // when the attribute gets selected
            // remove the view and then show the add
            attribute.on('change', function() {
                this.renderAddExistingView(attribute);
            }, this);
            this.renderAddExistingView(attribute);
            $('#attribute-collectible-add-existing-dialog').modal();
            $('#attribute-collectible-add-existing-dialog', 'body').on('hidden.bs.modal', function() {
                self.addExistingView.remove();
            });
        },
        renderAddExistingView: function(attribute) {
            if (this.addExistingView) {
                this.addExistingView.remove();
            }
            this.addExistingView = new AddExistingAttributeView({
                model: attribute,
                collectible: this.collectible
            });
            this.addExistingView.on('view:search:collectible', function() {
                this.addExistingView.remove();
                this.addExistingView = new AddExistingAttributeCollectibleSearchView({
                    collection: new PaginatedCollection(),
                    model: attribute
                });
                $('.modal-body', '#attribute-collectible-add-existing-dialog').html(this.addExistingView.render().el);
                $('.modal-footer .save', '#attribute-collectible-add-existing-dialog').hide();
            }, this);
            this.addExistingView.on('view:search:part', function() {
                this.addExistingView.remove();
                this.addExistingView = new AddExistingAttributePartSearchView({
                    collection: new PaginatedPart(),
                    model: attribute,
                    artists: this.artists,
                    manufacturers: this.manufacturers,
                    categories: this.categories
                });
                $('.modal-body', '#attribute-collectible-add-existing-dialog').html(this.addExistingView.render().el);
                $('.modal-footer .save', '#attribute-collectible-add-existing-dialog').hide();
            }, this);
            $('.modal-body', '#attribute-collectible-add-existing-dialog').html(this.addExistingView.render().el);
            $('.modal-footer .save', '#attribute-collectible-add-existing-dialog').show();
        }
    });

});