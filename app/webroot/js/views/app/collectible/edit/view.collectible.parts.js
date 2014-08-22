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
            }
        },
        events: {
            'click #add-existing-item-link': 'addExisting',
            'click #add-new-item-link': 'addNew'
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
            $('.modal-footer .save', '#attribute-collectible-add-new-dialog').click(function() {
                var url = '/attributes_collectibles/add.json';
                $('#attribute-collectible-add-new-dialog').find('form').ajaxSubmit({
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
                        $('#attribute-collectible-add-new-dialog').find('form').find('.error-message').remove();
                    },
                    // success identifies the function to invoke when the server response
                    // has been received
                    success: function(responseText, statusText, xhr, $form) {
                        if (responseText.response.isSuccess) {
                            $('#attribute-collectible-add-new-dialog').modal('hide');
                            var message = 'Part has been submitted!';
                            if (!responseText.response.data.isEdit) {
                                message = 'Part has been added!';
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
                                        $(':input[name="data[' + value.model + '][' + value.name + ']"]', $('#attribute-collectible-add-new-dialog').find('form')).after('<div class="error-message">' + value.message + '</div>');
                                    } else {
                                        $('#attribute-collectible-add-new-dialog').find('.component-message.error').children('span').text(value.message);
                                    }
                                });
                            }
                        }
                    }
                });
            });
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
            // Putting this at this level for now
            // $('.modal-footer .save', '#attribute-update-dialog').click(function() {
            //     var url = '/attributes/update.json';
            //     $('#attribute-update-dialog').find('form').ajaxSubmit({
            //         // dataType identifies the expected content type of the server response
            //         dataType: 'json',
            //         url: url,
            //         beforeSubmit: function(formData, jqForm, options) {
            //             $('#attribute-update-dialog').find('form').find('.error-message').remove();
            //             formData.push({
            //                 name: '_method',
            //                 type: 'text',
            //                 value: 'POST'
            //             });
            //         },
            //         // success identifies the function to invoke when the server response
            //         // has been received
            //         success: function(responseText, statusText, xhr, $form) {
            //             if (responseText.response.isSuccess) {
            //                 $('#attribute-update-dialog').modal('hide');
            //                 var message = 'Update has been submitted!';
            //                 if (!responseText.response.data.isEdit) {
            //                     message = 'The part was successfully updated!';
            //                 }
            //                 $.blockUI({
            //                     message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
            //                     showOverlay: false,
            //                     css: {
            //                         top: '100px',
            //                         'background-color': '#DDFADE',
            //                         border: '1px solid #93C49F',
            //                         'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
            //                         'border-radius': '4px 4px 4px 4px',
            //                         color: '#333333',
            //                         'margin-bottom': '20px',
            //                         padding: '8px 35px 8px 14px',
            //                         'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
            //                         'z-index': 999999
            //                     },
            //                     timeout: 2000
            //                 });
            //                 var data = responseText.response.data;
            //                 if (data.isEdit === false) {
            //                     // This will return the updated attribute data...we need
            //                     // to find the model and then update it
            //                     self.collection.each(function(attribute) {
            //                         if (attribute.toJSON().Attribute.id === data.Attribute.id) {
            //                             attribute.set({
            //                                 Attribute: data.Attribute
            //                             });
            //                         }
            //                     });
            //                 } else {
            //                     // do nothing
            //                 }
            //             } else {
            //                 if (responseText.response.errors) {
            //                     $.each(responseText.response.errors, function(index, value) {
            //                         if (value.inline) {
            //                             $(':input[name="data[' + value.model + '][' + value.name + ']"]', $('#attribute-update-dialog')).after('<div class="error-message">' + value.message + '</div>');
            //                         } else {
            //                             $('#attribute-update-dialog').find('.component-message.error').children('span').text(value.message);
            //                         }
            //                     });
            //                 }
            //             }
            //         }
            //     });
            // });
            // this.updateCollectibleAttributes = new UpdateCollectibleAttributes({
            //     $element: $('.attributes.collectible', self.el),
            //     $context: self.el,
            //     success: function(data) {
            //         if (data.isEdit === false) {
            //             // This will return the updated attribute data...we need
            //             // to find the model and then update it
            //             self.collection.each(function(attribute) {
            //                 if (attribute.toJSON().id === data.id) {
            //                     attribute.set({
            //                         count: data.count,
            //                         attribute_collectible_type_id: data.attribute_collectible_type_id,
            //                         attribute_collectible_type: data.attribute_collectible_type
            //                     });
            //                 }
            //             });
            //         } else {
            //             // do nothing
            //         }
            //     }
            // });
            // this.removeCollectibleAttributes = new RemoveAttributeLinks({
            //     $element: $('.attributes.collectible', self.el),
            //     $context: self.el,
            //     success: function(data) {
            //         if (data.isEdit === false) {
            //             // This will contain the id of the Attribute we
            //             // removed.  We will use that to find
            //             self.collection.each(function(attribute) {
            //                 if (attribute.toJSON().id === data.id) {
            //                     self.collection.remove(attribute);
            //                 }
            //             });
            //         } else {
            //             // do nothing
            //         }
            //     }
            // });
            // this.updateCollectibleAttributes.init();
            // this.removeCollectibleAttributes.init();
            return this;
        },
        addNew: function() {
            var self = this;
            var attribute = new AttributesCollectibleModel();
            this.renderAddNewView(attribute);
            $('#attribute-collectible-add-new-dialog').modal();
            $('#attribute-collectible-add-new-dialog', 'body').on('hidden.bs.modal', function() {
                self.addNewView.remove();
            });
        },
        renderAddNewView: function(attribute) {
            if (this.addNewView) {
                this.addNewView.remove();
            }
            this.addNewView = new AddAttributeView({
                model: attribute,
                manufacturers: this.manufacturers,
                artists: this.artists,
                scales: this.scales,
                collectible: this.collectible,
                type: 'new'
            });
            this.addNewView.on('view:category:select', function() {
                this.addNewView.remove();
                this.addNewView = new AttributeCategoryView({
                    model: attribute
                });
                this.addNewView.on('change:attribute_category_id', function() {
                    this.renderAddNewView(attribute);
                }, this);
                $('.modal-body', '#attribute-collectible-add-new-dialog').html(this.addNewView.render().el);
                $('.modal-footer .save', '#attribute-collectible-add-new-dialog').hide();
            }, this);
            $('.modal-body', '#attribute-collectible-add-new-dialog').html(this.addNewView.render().el);
            $('.modal-footer .save', '#attribute-collectible-add-new-dialog').show();
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