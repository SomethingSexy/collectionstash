define(['backbone', 'jquery',
    'views/common/view.alert',
    'views/app/collectible/edit/view.collectible.delete',
    'views/app/collectible/edit/view.manufacturer',
    'views/app/collectible/edit/view.collectible',
    'views/app/collectible/edit/view.persons',
    'views/app/collectible/edit/view.tags',
    'models/model.collectible',
    'models/model.status',
    'views/view.status',
    'collections/collection.collectibles',
    'collections/collection.parts',
    // todo: old templates remove as converted to mustache and individual view files
    'text!templates/collectibles/collectible.default.dust',
    'text!templates/collectibles/photo.default.dust',
    'text!templates/collectibles/attributes.default.dust',
    'text!templates/collectibles/attributecollectible.default.dust',
    'text!templates/collectibles/status.dust',
    'text!templates/collectibles/message.dust',
    'text!templates/collectibles/message.error.severe.dust',
    'text!templates/collectibles/message.duplist.dust',
    'text!templates/collectibles/manufacturer.add.dust',
    'text!templates/collectibles/manufacturer.edit.dust',
    'text!templates/collectibles/modal.dust',
    'text!templates/collectibles/manufacturer.series.add.dust',
    'text!templates/collectibles/attribute.upload.dust',
    'text!templates/collectibles/directional.dust',
    'text!templates/collectibles/attribute.add.existing.dust',
    'text!templates/collectibles/attribute.add.existing.search.dust',
    'text!templates/common/paging.dust',
    'text!templates/collectibles/directional.custom.dust',
    'text!templates/collectibles/collectible.custom.dust',
    'text!templates/collectibles/attribute.add.existing.search.part.dust',
    'text!templates/collectibles/attribute.default.dust',
    'text!templates/attributes/attributes.remove.duplicate.dust',
    'text!templates/collectibles/collectible.original.dust',
    'text!templates/collectibles/directional.original.dust',
    'text!templates/collectibles/attribute.add.new.dust',
    'text!templates/collectibles/collectible.delete.dust',
    'text!templates/common/alert.dust',
    'jquery.form', 'jquery.treeview', 'cs.core.tree', 'jquery.getimagedata', 'jquery.iframe-transport', 'cors/jquery.postmessage-transport', 'jquery.fileupload', 'jquery.fileupload-fp', 'jquery.fileupload-ui', "jquery.ui.widget", 'blockui', 'backbone.validation', 'cs.attribute'
], function(Backbone, $, AlertView, CollectibleDeleteView, ManufacturerView, CollectibleView, PersonsView, TagsView, CollectibleModel, Status, StatusView, PaginatedCollection, PaginatedPart, collectibleTemplate, photoTemplate, attributesTemplate, attributeTemplate, statusTemplate, messageTemplate, messageSevereTemplate, dupListTemplate, manufacturerAddTemplate, manufacturerEditTemplate, modalTemplate, manufacturerSeriesAddTemplate, attributeUploadTemplate, directionalTemplate, attributeAddExistingTemplate, attributeAddExistingSearchTemplate, pagingTemplate, directionalCustomTemplate, customTemplate, attributeAddExistingSearchPartTemplate, partTemplate, attributeRemoveDuplicate, originalTemplate, directionalOriginalTemplate, attributeAddNewTemplate, collectibleDeleteTemplate, alertTemplate) {
    /**
     * TODO: Known Issues:
     * - If you add a brand to a manufacturer, then go back to that list and find a brand, it won't
     *  exist in there
     *
     * TODO: Enhancements
     *  - Update so that there is a standard modal view to render things too
     */

    dust.loadSource(dust.compile(collectibleTemplate, 'collectible.default.edit'));
    dust.loadSource(dust.compile(photoTemplate, 'photo.default.edit'));
    dust.loadSource(dust.compile(attributesTemplate, 'attributes.default.edit'));
    dust.loadSource(dust.compile(attributeTemplate, 'attributecollectible.default.edit'));
    dust.loadSource(dust.compile(statusTemplate, 'status.edit'));
    dust.loadSource(dust.compile(messageTemplate, 'message.edit'));
    dust.loadSource(dust.compile(messageSevereTemplate, 'message.error.severe'));
    dust.loadSource(dust.compile(dupListTemplate, 'message.duplist'));
    dust.loadSource(dust.compile(manufacturerAddTemplate, 'manufacturer.add'));
    dust.loadSource(dust.compile(manufacturerEditTemplate, 'manufacturer.edit'));
    dust.loadSource(dust.compile(modalTemplate, 'modal'));
    dust.loadSource(dust.compile(manufacturerSeriesAddTemplate, 'manufacturer.series.add'));
    dust.loadSource(dust.compile(attributeUploadTemplate, 'attribute.photo.edit'));
    dust.loadSource(dust.compile(directionalTemplate, 'directional.page'));
    dust.loadSource(dust.compile(attributeAddExistingTemplate, 'attribute.add.existing'));
    dust.loadSource(dust.compile(attributeAddExistingSearchTemplate, 'attribute.add.existing.search'));
    dust.loadSource(dust.compile(directionalCustomTemplate, 'directional.custom'));
    dust.loadSource(dust.compile(customTemplate, 'collectible.custom.edit'));
    dust.loadSource(dust.compile(attributeAddExistingSearchPartTemplate, 'attribute.add.existing.search.part'));
    dust.loadSource(dust.compile(partTemplate, 'attribute.default.edit'));
    dust.loadSource(dust.compile(attributeRemoveDuplicate, 'attribute.remove.duplicate'));
    dust.loadSource(dust.compile(pagingTemplate, 'paging'));
    dust.loadSource(dust.compile(originalTemplate, 'collectible.original.edit'));
    dust.loadSource(dust.compile(directionalOriginalTemplate, 'directional.original'));
    dust.loadSource(dust.compile(attributeAddNewTemplate, 'attribute.add.new'));
    dust.loadSource(dust.compile(collectibleDeleteTemplate, 'collectible.delete'));
    dust.loadSource(dust.compile(alertTemplate, 'alert'));

    var printId = '10';
    var pageEvents = _.extend({}, Backbone.Events);
    var ErrorModel = Backbone.Model.extend({});
    var Errors = Backbone.Collection.extend({
        model: ErrorModel
    });


    // replaced with collection/collection.collectibles
    // var PaginatedCollection = Backbone.Paginator.requestPager.extend({
    //     model: CollectibleModel,
    //     paginator_core: {
    //         // the type of the request (GET by default)
    //         type: 'GET',
    //         // the type of reply (jsonp by default)
    //         dataType: 'json',
    //         // the URL (or base URL) for the service
    //         url: function() {
    //             var url = '/collectibles/search/page:' + this.currentPage + '?q=' + this.searchQuery;
    //             if (this.selectedSort) {
    //                 url = url + '/sort:' + this.selectedSort + '/direction:' + this.sortDirection;
    //             }
    //             return url;
    //         }
    //     },
    //     paginator_ui: {
    //         // the lowest page index your API allows to be accessed
    //         firstPage: 1,
    //         // which page should the paginator start from
    //         // (also, the actual page the paginator is on)
    //         currentPage: 1,
    //         // how many items per page should be shown
    //         perPage: 25,
    //         // a default number of total pages to query in case the API or
    //         // service you are using does not support providing the total
    //         // number of pages for us.
    //         // 10 as a default in case your service doesn't return the total
    //         //totalPages : totalSubmissionPages,
    //         //total : totalSubmission
    //     },
    //     server_api: {
    //         // how many results the request should skip ahead to
    //         // customize as needed. For the Netflix API, skipping ahead based on
    //         // page * number of results per page was necessary.
    //         'page': function() {
    //             return this.currentPage;
    //         }
    //     },
    //     parse: function(response) {
    //         // Be sure to change this based on how your results
    //         // are structured (e.g d.results is Netflix specific)
    //         var tags = response.results;
    //         //Normally this.totalPages would equal response.d.__count
    //         //but as this particular NetFlix request only returns a
    //         //total count of items for the search, we divide.
    //         this.totalPages = response.metadata.paging.pageCount;
    //         this.paginator_ui.totalPages = response.metadata.paging.pageCount;
    //         this.total = response.metadata.paging.count;
    //         this.pagingHtml = response.metadata.pagingHtml;
    //         return tags;
    //     }
    // });
    var Collectibles = Backbone.Collection.extend({
        model: CollectibleModel
    });
    var Brand = Backbone.Model.extend({});
    var Brands = Backbone.Collection.extend({
        model: Brand,
        comparator: function(brand) {
            return brand.get("License").name.toLowerCase();
        }
    });
    var CollectibleTagModel = Backbone.Model.extend({});
    var CollectibleTypeModel = Backbone.Model.extend({});
    var CollectibleUploadModel = Backbone.Model.extend({});
    var CollectibleUploads = Backbone.Collection.extend({
        model: CollectibleUploadModel,
        initialize: function(models, options) {
            this.id = options.id;
        },
        url: function() {
            return '/collectibles_uploads/uploads/' + this.id;
        },
        parse: function(resp, xhr) {
            var retVal = [];
            _.each(resp, function(upload) {
                var parsedUpload = upload.CollectiblesUpload;
                parsedUpload['Upload'] = upload.Upload;
                retVal.push(parsedUpload);
            });
            return retVal;
        }
    });
    // These two are used for the popup to add photos to an attribute
    var AttributeUploadModel = Backbone.Model.extend({});
    var AttributeUploads = Backbone.Collection.extend({
        model: AttributeUploadModel,
        initialize: function(models, options) {
            this.id = options.id;
        },
        url: function() {
            return '/attributes_uploads/view/' + this.id;
        },
        parse: function(resp, xhr) {
            return resp.response.data.files;
        }
    });
    var ManufacturerModel = Backbone.Model.extend({
        urlRoot: '/manufactures/manufacturer',
        validation: {
            title: [{
                pattern: /^[A-Za-z0-9 _]*$/,
                msg: 'Invalid characters'
            }, {
                required: true
            }],
            url: [{
                pattern: 'url',
                msg: 'Must be a valid url.'
            }, {
                required: false
            }]
        }
    });
    var CurrencyModel = Backbone.Model.extend({});
    var Currencies = Backbone.Collection.extend({
        model: CurrencyModel
    });
    var Scale = Backbone.Model.extend({});
    var Scales = Backbone.Collection.extend({
        model: Scale
    });

    var ManufacturerList = Backbone.Collection.extend({
        model: ManufacturerModel,
        initialize: function(models) {
            for (var i = 0; i < models.length; i++) {
                if (models[i].Manufacture) {
                    models[i] = models[i].Manufacture;
                    delete models[i].Manufacture;
                }
            };
        },
        comparator: function(man) {
            return man.get("title").toLowerCase();
        }
    });
    // TODO: this won't work for deleting an attribute :(
    // need to update this be more robust and fit the backbone style
    var AttributesCollectibleModel = Backbone.Model.extend({
        urlRoot: '/attributes_collectibles/attribute',
        parse: function(resp, xhr) {
            var retVal = {};
            retVal = resp.AttributesCollectible;
            retVal.Attribute = resp.Attribute;
            retVal.Revision = resp.Revision;
            return retVal;
        }
    });
    var AttributeModel = Backbone.Model.extend({
        parse: function(resp, xhr) {
            return resp.response.data;
        }
    });
    var Attributes = Backbone.Collection.extend({
        model: AttributesCollectibleModel
    });
    var TagModel = Backbone.Model.extend({
        urlRoot: function() {
            return '/collectibles/tag/' + adminMode + '/';
        }
    });
    var Tags = Backbone.Collection.extend({
        model: TagModel,
        urlRoot: '/collectibles/tags'
    });
    var ArtistModel = Backbone.Model.extend({
        urlRoot: function() {
            return '/collectibles/artist/' + adminMode + '/';
        }
    });
    var Artists = Backbone.Collection.extend({
        model: ArtistModel,
        urlRoot: '/collectibles/artists'
    });
    /**
     *TODO : The methods to handle adding attributes
     * should probably be rewritten when there is time
     * to be more backboney
     */
    var AttributesView = Backbone.View.extend({
        template: 'attributes.default.edit',
        className: "col-md-12",
        events: {
            'click #add-existing-item-link': 'addExisting',
            'click #add-new-item-link': 'addNew'
        },
        initialize: function(options) {
            var self = this;
            this.status = options.status;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
            this.collectible = options.collectible;
            this.scales = options.scales;
            this.collection.on('remove', this.renderList, this);
        },
        render: function() {
            var self = this;
            var data = {
                'collectibleId': collectibleId,
                collectible: this.collectible.toJSON()
            };
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            $('.modal-footer .save', '#attribute-collectible-add-new-dialog').click(function() {
                var url = '/attributes_collectibles/add.json';
                // If we are passing in an override admin or the options are set to admin mode
                if (adminMode) {
                    url = '/admin/attributes_collectibles/add.json';
                }
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
                if (adminMode) {
                    url = '/admin/attributes_collectibles/add.json';
                }
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
            $('.modal-footer .save', '#attribute-update-dialog').click(function() {
                var url = '/attributes/update.json';
                // If we are passing in an override admin or the options are set to admin mode
                if (self.options.adminPage) {
                    url = '/admin/attributes/update.json';
                }
                $('#attribute-update-dialog').find('form').ajaxSubmit({
                    // dataType identifies the expected content type of the server response
                    dataType: 'json',
                    url: url,
                    beforeSubmit: function(formData, jqForm, options) {
                        $('#attribute-update-dialog').find('form').find('.error-message').remove();
                        formData.push({
                            name: '_method',
                            type: 'text',
                            value: 'POST'
                        });
                    },
                    // success identifies the function to invoke when the server response
                    // has been received
                    success: function(responseText, statusText, xhr, $form) {
                        if (responseText.response.isSuccess) {
                            $('#attribute-update-dialog').modal('hide');
                            var message = 'Update has been submitted!';
                            if (self.options.adminPage || !responseText.response.data.isEdit) {
                                message = 'The part was successfully updated!';
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
                                // This will return the updated attribute data...we need
                                // to find the model and then update it
                                self.collection.each(function(attribute) {
                                    if (attribute.toJSON().Attribute.id === data.Attribute.id) {
                                        attribute.set({
                                            Attribute: data.Attribute
                                        });
                                    }
                                });
                            } else {
                                // do nothing
                            }
                        } else {
                            if (responseText.response.errors) {
                                $.each(responseText.response.errors, function(index, value) {
                                    if (value.inline) {
                                        $(':input[name="data[' + value.model + '][' + value.name + ']"]', $('#attribute-update-dialog')).after('<div class="error-message">' + value.message + '</div>');
                                    } else {
                                        $('#attribute-update-dialog').find('.component-message.error').children('span').text(value.message);
                                    }
                                });
                            }
                        }
                    }
                });
            });
            this.updateCollectibleAttributes = new UpdateCollectibleAttributes({
                'adminPage': adminMode,
                $element: $('.attributes.collectible', self.el),
                $context: self.el,
                success: function(data) {
                    if (data.isEdit === false) {
                        // This will return the updated attribute data...we need
                        // to find the model and then update it
                        self.collection.each(function(attribute) {
                            if (attribute.toJSON().id === data.id) {
                                attribute.set({
                                    count: data.count,
                                    attribute_collectible_type_id: data.attribute_collectible_type_id,
                                    attribute_collectible_type: data.attribute_collectible_type
                                });
                            }
                        });
                    } else {
                        // do nothing
                    }
                }
            });
            this.removeCollectibleAttributes = new RemoveAttributeLinks({
                'adminPage': adminMode,
                $element: $('.attributes.collectible', self.el),
                $context: self.el,
                success: function(data) {
                    if (data.isEdit === false) {
                        // This will contain the id of the Attribute we
                        // removed.  We will use that to find
                        self.collection.each(function(attribute) {
                            if (attribute.toJSON().id === data.id) {
                                self.collection.remove(attribute);
                            }
                        });
                    } else {
                        // do nothing
                    }
                }
            });
            this.collection.each(function(attribute) {
                $('.attributes-list', self.el).append(new AttributeView({
                    model: attribute,
                    status: self.status,
                    artists: self.artists,
                    manufacturers: self.manufacturers,
                    categories: self.categories,
                    collectible: self.collectible,
                    scales: self.scales
                }).render().el);
            });
            this.updateCollectibleAttributes.init();
            this.removeCollectibleAttributes.init();
            return this;
        },
        renderList: function() {
            var self = this;
            $('.attributes-list', self.el).empty();
            this.collection.each(function(attribute) {
                $('.attributes-list', self.el).append(new AttributeView({
                    model: attribute,
                    status: self.status,
                    artists: self.artists,
                    manufacturers: self.manufacturers,
                    categories: self.categories,
                    collectible: self.collectible,
                    scales: self.scales
                }).render().el);
            });
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
    var AttributeView = Backbone.View.extend({
        template: 'attributecollectible.default.edit',
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
            this.model.on('change', this.render, this);
        },
        render: function() {
            var self = this;
            var attributeModel = this.model.toJSON();
            var status = this.status.toJSON();
            // If the status is draft or submitted, don't allow
            // remove the of the part...once the admin piece
            // comes in update to allow if admin
            // Remove Collectible Attribute will remove the attribute
            // if it is the only one, so we don't need remove really
            if (status.status.id === '1' || status.status.id === '2' && !adminMode) {
                attributeModel.allowRemoveAttribute = false;
            } else {
                attributeModel.allowRemoveAttribute = true;
            }
            // we need to build out some stuff for editing and removing
            var attribute = {};
            attribute.categoryId = attributeModel.Attribute.AttributeCategory.id;
            attribute.categoryName = attributeModel.Attribute.AttributeCategory.path_name;
            attribute.name = attributeModel.Attribute.name;
            attribute.description = attributeModel.Attribute.description;
            if (attributeModel.Attribute.scale_id) {
                attribute.scaleId = attributeModel.Attribute['scale_id'];
            } else {
                attribute.scaleId = null;
            }
            if (attributeModel.Attribute['manufacture_id']) {
                attribute.manufacturerId = attributeModel.Attribute['manufacture_id'];
            } else {
                attribute.manufacturerId = null;
            }
            if (attributeModel.Attribute['artist_id']) {
                attribute.artistId = attributeModel.Attribute['artist_id'];
            } else {
                attribute.artistId = null;
            }
            attribute.id = attributeModel.Attribute.id;
            var attributeCollectible = {};
            attributeCollectible.id = attributeModel.id;
            attributeCollectible.attributeId = attributeModel['attribute_id'];
            attributeCollectible.categoryName = attributeModel.Attribute.AttributeCategory['path_name'];
            attributeCollectible.count = attributeModel.count;
            attributeCollectible.attributeCollectibleTypeId = attributeModel['attribute_collectible_type_id'];
            attributeModel.uploadDirectory = uploadDirectory;
            attributeModel.collectible = this.collectible.toJSON();
            dust.render(this.template, attributeModel, function(error, output) {
                $(self.el).html(output);
            });
            $(self.el).attr('data-id', this.model.toJSON().Attribute.id).attr('data-attribute-collectible-id', this.model.toJSON().id).attr('data-attached', true);
            $('span.popup', self.el).popover({
                placement: 'bottom',
                html: 'true',
                template: '<div class="popover" onmouseover="$(this).mouseleave(function() {$(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
            }).click(function(e) {
                e.preventDefault();
            }).mouseenter(function(e) {
                $(this).popover('show');
            });
            $(self.el).attr('data-attribute', JSON.stringify(attribute));
            $(self.el).attr('data-attribute-collectible', JSON.stringify(attributeCollectible));
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
    var AttributePhotoView = Backbone.View.extend({
        template: 'attribute.photo.edit',
        className: "col-md-4",
        events: {},
        initialize: function(options) {
            this.eventManager = options.eventManager;
            // this.collection.on('reset', function() {
            // var self = this;
            // var data = {
            // uploads : this.collection.toJSON(),
            // uploadDirectory : uploadDirectory
            // };
            // dust.render(this.template, data, function(error, output) {
            // $(self.el).html(output);
            // });
            // }, this);
        },
        render: function() {
            var self = this;
            var data = {
                uploadDirectory: uploadDirectory,
                attribute: this.model.toJSON()
            };
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            $('.fileupload', self.el).fileupload({
                //dropZone : $('#dropzone')
            });
            $('.fileupload', self.el).fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));
            $('.fileupload', self.el).fileupload('option', {
                url: '/attributes_uploads/upload',
                maxFileSize: 2097152,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                process: [{
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 2097152 // 2MB
                }, {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                }, {
                    action: 'save'
                }]
            });
            $('.fileupload', self.el).on('hidden.bs.modal', function() {
                $('#fileupload table tbody tr.template-download').remove();
                pageEvents.trigger('upload:close');
            });
            $('.upload-url', self.el).on('click', function() {
                var url = $.trim($('.url-upload-input', self.el).val());
                if (url !== '') {
                    $.ajax({
                        dataType: 'json',
                        type: 'post',
                        data: $('.fileupload', self.el).serialize(),
                        url: '/attributes_uploads/upload/',
                        beforeSend: function(formData, jqForm, options) {
                            $('.fileupload-progress', self.el).removeClass('fade').addClass('active');
                            $('.fileupload-progress .progress .bar', self.el).css('width', '100%');
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (data && data.files.length) {
                                var that = $('.fileupload', self.el);
                                that.fileupload('option', 'done').call(that, null, {
                                    result: data
                                });
                            } else if (data.response && !data.response.isSuccess) {
                                // most like an error
                                $('span', '.component-message.error').text(data.response.errors[0].message);
                            }
                        },
                        complete: function() {
                            $('.fileupload-progress', self.el).removeClass('active').addClass('fade');
                            $('.fileupload-progress .progress .bar', self.el).css('width', '0%');
                        }
                    });
                }
            });
            var that = $('.fileupload', self.el);
            that.fileupload('option', 'done').call(that, null, {
                result: {
                    files: self.collection.toJSON()
                }
            });
            return this;
        }
    });
    var PhotoView = Backbone.View.extend({
        template: 'photo.default.edit',
        className: "",
        events: {},
        initialize: function(options) {
            this.eventManager = options.eventManager;
            this.collection.on('reset', function() {
                var self = this;
                var data = {
                    uploads: this.collection.toJSON(),
                    uploadDirectory: uploadDirectory
                };
                dust.render(this.template, data, function(error, output) {
                    $(self.el).html(output);
                });
            }, this);
        },
        render: function() {
            var self = this;
            var data = {
                uploads: this.collection.toJSON(),
                uploadDirectory: uploadDirectory
            };
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            $('#fileupload').fileupload({
                //dropZone : $('#dropzone')
            });
            $('#fileupload').fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));
            $('#fileupload').fileupload('option', {
                url: '/collectibles_uploads/upload',
                maxFileSize: 2097152,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                process: [{
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 2097152 // 2MB
                }, {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                }, {
                    action: 'save'
                }]
            });
            $('#upload-dialog').on('hidden.bs.modal', function() {
                $('#fileupload table tbody tr.template-download').remove();
                pageEvents.trigger('upload:close');
            });
            $('#upload-url').on('click', function() {
                var url = $.trim($('.url-upload-input').val());
                if (url !== '') {
                    $.ajax({
                        dataType: 'json',
                        type: 'post',
                        data: $('#fileupload').serialize(),
                        url: '/collectibles_uploads/upload/',
                        beforeSend: function(formData, jqForm, options) {
                            $('.fileupload-progress').removeClass('fade').addClass('active');
                            $('.fileupload-progress .progress .bar').css('width', '100%');
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (data && data.files.length) {
                                var that = $('#fileupload');
                                that.fileupload('option', 'done').call(that, null, {
                                    result: data
                                });
                            } else if (data.response && !data.response.isSuccess) {
                                // most like an error
                                $('span', '.component-message.error').text(data.response.errors[0].message);
                            }
                        },
                        complete: function() {
                            $('.fileupload-progress').removeClass('active').addClass('fade');
                            $('.fileupload-progress .progress .bar').css('width', '0%');
                        }
                    });
                }
            });
            $(self.el).on('click', '.upload-link', function() {
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
                $.ajax({
                    dataType: 'json',
                    url: '/collectibles_uploads/view/' + collectibleId,
                    beforeSend: function(formData, jqForm, options) {},
                    success: function(data, textStatus, jqXHR) {
                        if (data && data.response.data.files.length) {
                            var that = $('#fileupload');
                            that.fileupload('option', 'done').call(that, null, {
                                result: data.response.data
                            });
                        }
                        $.unblockUI();
                        $('.url-upload-input', '#upload-dialog').val('');
                        $('span', '.component-message.error').text('');
                        $('#upload-dialog').modal();
                    }
                });
            });
            return this;
        }
    });
    var SeriesView = Backbone.View.extend({
        events: {
            'click span.item': 'selectSeries'
        },
        initialize: function(options) {},
        render: function() {
            var self = this;
            $(self.el).html(this.model.toJSON().response.data);
            return this;
        },
        selectSeries: function(event) {
            var name = $(event.currentTarget).attr('data-path');
            var id = $(event.currentTarget).attr('data-id');
            pageEvents.trigger('series:select', id, name);
        }
    });
    var ManufacturerSeriesView = Backbone.View.extend({
        template: 'manufacturer.series.add',
        modal: 'modal',
        events: {
            'click .add-series': 'showAdd',
            'click .add.submit': 'addSeries'
        },
        initialize: function(options) {
            var self = this;
            Backbone.Validation.bind(this, {
                valid: function(view, attr, selector) {
                    view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
                    view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                    view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').removeClass('has-error');
                    // do something
                },
                invalid: function(view, attr, error, selector) {
                    view.$('[' + selector + '~="' + attr + '"]').addClass('invalid').attr('data-error', error);
                    view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').addClass('has-error');
                    view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                    view.$('[' + selector + '~="' + attr + '"]').after('<span class="help-block _error">' + error + '</span>');
                    // do something
                }
            });
            this.manufacturer = options.manufacturer;
        },
        remove: function() {
            //this.model.off('change');
            Backbone.View.prototype.remove.call(this);
        },
        renderBody: function() {
            var self = this;
            var data = {
                manufacturer: this.manufacturer.toJSON()
            };
            dust.render(this.template, data, function(error, output) {
                $('.modal-body', self.el).html(output);
            });
            $('.modal-body', self.el).append(this.model.toJSON().response.data);
        },
        render: function() {
            var self = this;
            dust.render(this.modal, {
                modalId: 'manufacturerSeriesModal',
                modalTitle: 'Manufacturer Categories'
            }, function(error, output) {
                $(self.el).html(output);
            });
            $(self.el).find('.btn-primary.save').remove();
            this.renderBody();
            return this;
        },
        showAdd: function(event) {
            this.hideMessage();
            var $target = $(event.currentTarget);
            var $inputWrapper = $('<div></div>').addClass('item').addClass('input');
            var $input = $('<input />').attr('type', 'input').attr('maxlength', '100');
            var $submit = $('<button></button>').text('Submit').addClass('add').addClass('submit');
            var $cancel = $('<button></button>').text('Cancel').addClass('add').addClass('cancel');
            $inputWrapper.append($input);
            $inputWrapper.append($submit);
            $inputWrapper.append($cancel);
            $target.parent('span.actions').after($inputWrapper);
        },
        closeAdd: function(event) {
            var $target = $(event.currentTarget);
            $target.parent('div.input').remove();
        },
        addSeries: function(event) {
            var self = this;
            var seriesId = $(event.currentTarget).parent('div.input').parent('li').children('span.name').attr('data-id');
            var name = $(event.currentTarget).parent('div.input').children('input').val();
            $.ajax({
                url: '/series/add.json',
                dataType: 'json',
                data: 'data[Series][parent_id]=' + seriesId + '&data[Series][name]=' + name,
                type: 'post',
                beforeSend: function(xhr) {},
                error: function(jqXHR, textStatus, errorThrown) {
                    var $messageContainer = $('.message-container', self.el);
                    $('h4', $messageContainer).text('');
                    $('ul', $messageContainer).empty();
                    if (jqXHR.status === 401) {
                        $('h4', $messageContainer).text('You must be logged in to do that!');
                    } else if (jqXHR.status === 400) {
                        var response = JSON.parse(jqXHR.responseText);
                        $('h4', $messageContainer).text('Oops! Something wasn\'t filled out correctly.');
                        if (response && response.response && response.response.errors) {
                            _.each(response.response.errors, function(error) {
                                _.each(error.message, function(message) {
                                    $('ul', $messageContainer).append($('<li></li>').text(message));
                                });
                            });
                        }
                    } else {
                        $('h4', $messageContainer).text('Something really bad happened.');
                    }
                    $messageContainer.show();
                },
                success: function(data) {
                    self.hideMessage();
                    if (data.response.isSuccess) {
                        //TODO: Once this part is more backboney then we can just add
                        // render
                        // let's try and add it to the current list
                        var $parentLi = $(event.currentTarget).parent('div.input').parent('li');
                        var $ul = $('ul', $parentLi);
                        if ($ul.length === 0) {
                            $parentLi.append($('<ul></ul>'));
                            $ul = $('ul', $parentLi);
                        }
                        var $series = $('<li></li>');
                        $series.append('<span class="item name" data-id=" ' + data.response.data.id + '" data-path="' + data.response.data.name + '">' + data.response.data.name + '</span>');
                        $series.append('<span class="item actions"> <a class="action add-series"> Add</a></span>');
                        $ul.append($series);
                        self.closeAdd(event);
                        // first check to see if
                    } else {
                        //data.errors[0][name];
                    }
                }
            });
        },
        hideMessage: function() {
            $('.message-container', this.el).hide();
        }
    });

    var MessageView = Backbone.View.extend({
        template: 'message.edit',
        className: "col-md-12",
        events: {},
        initialize: function(options) {
            if (options.errors && _.size(options.errors) > 0) {
                this.hasErrors = true;
                this.errors = options.errors;
            } else {
                this.hasErrors = false;
            }
        },
        render: function() {
            var self = this;
            var data = {
                hasErrors: this.hasErrors,
            };
            if (this.hasErrors) {
                data.errors = this.errors.toJSON();
            }
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            return this;
        }
    });
    var SevereMessageView = Backbone.View.extend({
        template: 'message.error.severe',
        className: "col-md-12",
        events: {},
        initialize: function(options) {},
        render: function() {
            var self = this;
            var data = {};
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            return this;
        }
    });
    var DupListMessageView = Backbone.View.extend({
        template: 'message.duplist',
        className: "col-md-12",
        events: {},
        initialize: function(options) {},
        render: function() {
            var self = this;
            var data = {
                collectibles: this.collection.toJSON(),
                uploadDirectory: uploadDirectory
            };
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            return this;
        }
    });



    /**
     * This should be able to handle but adding an update
     *
     * If a model is added with an id, then it will be an update
     * otherwise it will be an add
     *
     * TODO: Once this gets updated to use proper models, we can
     * update the automatic field stuff, using data-name for
     * now because we want to maintain the name attribute
     */
    var AddAttributeView = Backbone.View.extend({
        template: 'attribute.add.new',
        events: {
            'click .select-category': 'selectCategory',
            "change input": "fieldChanged",
            "change select": "selectionChanged",
            'change textarea': 'fieldChanged',
            'click .attribute-type': 'toggleType'
        },
        initialize: function(options) {
            this.manufacturers = options.manufacturers;
            this.artists = options.artists;
            this.scales = options.scales;
            this.collectible = options.collectible;
            // edit vs new
            this.type = options.type;
            var hasType = this.model.has('Attribute');
            // default the attribute to be a custom one
            if (this.collectible.get('custom') && !hasType) {
                this.model.set({
                    'Attribute': {
                        type: 'custom'
                    }
                });
            } else if (this.collectible.get('original') && !hasType) {
                this.model.set({
                    'Attribute': {
                        type: 'original'
                    }
                });
            } else if (!hasType) {
                this.model.set({
                    'Attribute': {
                        type: 'mass'
                    }
                });
            }
        },
        render: function() {
            var self = this;
            var data = this.model.toJSON();
            data.manufacturers = this.manufacturers.toJSON();
            data.artists = this.artists.toJSON();
            data.scales = this.scales.toJSON();
            // we need this to determine how to render the view
            data.collectible = this.collectible.toJSON();
            if (this.type === 'new') {
                data.showCount = true;
                data.showId = false;
            } else {
                data.showCount = false;
                data.showId = true;
            }
            data['uploadDirectory'] = uploadDirectory;
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            $(self.el).animate({
                scrollTop: 0
            });
            return this;
        },
        selectCategory: function() {
            this.trigger('view:category:select');
        },
        selectionChanged: function(e) {
            var field = $(e.currentTarget);
            var value = $("option:selected", field).val();
            var type = field.attr('data-type');
            var data = {};
            if (type) {
                // else we need to get the type
                // set the new one
                data = this.model.get(type);
            }
            data[field.attr('data-name')] = value;
            // silent because we don't want to trigger a change
            // if this is an edit
            this.model.set(data, {
                silent: true
            });
        },
        fieldChanged: function(e) {
            var field = $(e.currentTarget);
            var type = field.attr('data-type');
            var data = {};
            if (type) {
                // else we need to get the type
                // set the new one
                data = this.model.get(type);
            }
            if (field.attr('type') === 'checkbox') {
                if (field.is(':checked')) {
                    data[field.attr('data-name')] = true;
                } else {
                    data[field.attr('data-name')] = false;
                }
            } else {
                data[field.attr('data-name')] = field.val();
            }
            // silent because we don't want to trigger a change
            // if this is an edit
            this.model.set(data, {
                silent: true
            });
        },
        toggleType: function(event) {
            var field = $(event.currentTarget);
            var type = field.attr('data-type');
            var data = {};
            if (type) {
                // else we need to get the type
                // set the new one
                data = this.model.get(type);
            }
            data[field.attr('data-name')] = field.val();
            // silent because we don't want to trigger a change
            // if this is an edit
            this.model.set(data, {
                silent: true
            });
            this.render();
        }
    });
    var AttributeCategoryView = Backbone.View.extend({
        //template : $('#attributes-category-tree').clone().html(),
        events: {
            'click .item': 'selectCategory'
        },
        initialize: function(options) {},
        render: function() {
            var self = this;
            $(self.el).html($('#attributes-category-tree').clone().html());
            $(self.el).animate({
                scrollTop: 0
            });
            return this;
        },
        selectCategory: function(event) {
            var categoryId = $(event.currentTarget).attr('data-id');
            var categoryPath = $(event.currentTarget).attr('data-path-name');
            var attribute = {
                Attribute: {
                    AttributeCategory: {}
                }
            };
            if (this.model.has('Attribute')) {
                attribute.Attribute = this.model.get('Attribute');
                if (!attribute.Attribute.hasOwnProperty('AttributeCategory')) {
                    attribute.Attribute.AttributeCategory = {};
                }
            }
            attribute.Attribute.attribute_category_id = categoryId;
            attribute.Attribute.AttributeCategory.path_name = categoryPath;
            this.model.set(attribute, {
                silent: true
            });
            this.trigger('change:attribute_category_id');
        }
    });
    /**
     * Main view when adding an existing attribute
     */
    var AddExistingAttributeView = Backbone.View.extend({
        template: 'attribute.add.existing',
        events: {
            'click #select-attribute-link': 'searchCollectible',
            'click #select-attribute-link-by-part': 'searchPart'
        },
        initialize: function(options) {
            // pssing in the collectible model, used to determine collectible type
            this.collectible = options.collectible;
        },
        render: function() {
            var self = this;
            var data = this.model.toJSON();
            if (data.hasOwnProperty('id')) {
                data['hasAttribute'] = true;
            } else {
                data['hasAttribute'] = false;
            }
            data['uploadDirectory'] = uploadDirectory;
            // if the collectible is a custom, then we will display
            // the type field
            data['collectible'] = this.collectible.toJSON();
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });
            $(self.el).animate({
                scrollTop: 0
            });
            return this;
        },
        searchCollectible: function() {
            this.trigger('view:search:collectible');
        },
        searchPart: function() {
            this.trigger('view:search:part');
        }
    });
    var AddExistingAttributePartSearchView = Backbone.View.extend({
        template: 'attribute.add.existing.search.part',
        events: {
            'click #filters .filter-links': 'selectFilter',
            'click a.page': 'gotoPage',
            'click a.next': 'next',
            'click a.previous': 'previous',
            'click tr.attribute': 'selectAttribute'
        },
        initialize: function(options) {
            var self = this;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
            this.collection.filters = {};
            this.collection.on('reset', function() {
                $('table', self.el).empty();
                _.each(this.collection.models, function(collectible) {
                    $('table', self.el).append(new AttributeSearchPartAttrView({
                        model: collectible
                    }).render().el);
                });
                var pagesArray = [];
                // ya fuck you dust
                for (var i = 1; i <= this.collection.paginator_ui.totalPages; i++) {
                    pagesArray.push(i);
                }
                var data = {
                    pages: pagesArray
                };
                if (this.collection.currentPage) {
                    data['paginator'] = {
                        currentPage: this.collection.currentPage,
                        firstPage: this.collection.firstPage,
                        perPage: this.collection.perPage,
                        totalPages: this.collection.totalPages,
                        total: this.collection.paginator_ui.total
                    };
                } else {
                    data['paginator'] = this.collection.paginator_ui;
                }
                dust.render('paging', data, function(error, output) {
                    $('.paging', self.el).html(output);
                });
                $(self.el).animate({
                    scrollTop: 0
                });
            }, this);
        },
        render: function() {
            var self = this;
            dust.render(this.template, {
                artists: this.artists.toJSON(),
                manufacturers: this.manufacturers.toJSON(),
                categories: this.categories.toJSON()
            }, function(error, output) {
                $(self.el).html(output);
            });
            return this;
        },
        selectFilter: function(event) {
            var selectedType = $(event.currentTarget).closest('.filter').attr('data-type');
            var selectedFilter = $(event.currentTarget).attr('data-filter');
            if (!this.collection.filters.hasOwnProperty(selectedType)) {
                this.collection.filters[selectedType] = [];
            }
            if ($(event.currentTarget).is(':checked')) {
                this.collection.filters[selectedType].push(selectedFilter);
            } else {
                // remove it
                this.collection.filters[selectedType].splice($.inArray(selectedFilter, this.collection.filters[selectedType]), 1);
            }
            this.collection.fetch();
        },
        gotoPage: function(e) {
            e.preventDefault();
            var page = $(e.target).text();
            this.collection.goTo(page);
        },
        next: function(e) {
            e.preventDefault();
            if (typeof this.collection.currentPage === 'undefined') {
                this.collection.currentPage = 1;
            }
            this.collection.requestNextPage();
        },
        previous: function(e) {
            e.preventDefault();
            this.collection.requestPreviousPage();
        },
        selectAttribute: function(event) {
            event.preventDefault();
            var attribute = JSON.parse($(event.currentTarget).attr('data-attribute'));
            this.model.clear({
                silent: true
            });
            this.model.set(attribute);
        }
    });
    var AddExistingAttributeCollectibleSearchView = Backbone.View.extend({
        template: 'attribute.add.existing.search',
        events: {
            'click button.search': 'searchCollectible',
            'click a.page': 'gotoPage',
            'click a.next': 'next',
            'click a.previous': 'previous',
            'click li.attribute': 'selectAttribute'
        },
        initialize: function(options) {
            var self = this;
            this.collection.on('reset', function() {
                $('table', self.el).empty();
                _.each(this.collection.models, function(collectible) {
                    // This renders the collectible
                    $('table.collectibles', self.el).append(new AttributeSearchCollectibleView({
                        model: collectible
                    }).render().el);
                    // This renders the parts
                    $('table.collectibles', self.el).append(new AttributeSearchCollectibleAttrView({
                        model: collectible
                    }).render().el);
                });
                var pagesArray = [];
                // ya fuck you dust
                for (var i = 1; i <= this.collection.paginator_ui.totalPages; i++) {
                    pagesArray.push(i);
                }
                var data = {
                    pages: pagesArray
                };
                if (this.collection.currentPage) {
                    data['paginator'] = {
                        currentPage: this.collection.currentPage,
                        firstPage: this.collection.firstPage,
                        perPage: this.collection.perPage,
                        totalPages: this.collection.totalPages,
                        total: this.collection.paginator_ui.total
                    };
                } else {
                    data['paginator'] = this.collection.paginator_ui;
                }
                dust.render('paging', data, function(error, output) {
                    $('.paging', self.el).html(output);
                });
                $(self.el).animate({
                    scrollTop: 0
                });
            }, this);
        },
        render: function() {
            var self = this;
            dust.render(this.template, {}, function(error, output) {
                $(self.el).html(output);
            });
            return this;
        },
        searchCollectible: function(event) {
            // TODO: Update to not allow searching
            // unless something was entered
            event.preventDefault();
            var query = $('.search-query', this.el).val();
            this.collection.searchQuery = query;
            this.collection.fetch();
        },
        gotoPage: function(e) {
            e.preventDefault();
            var page = $(e.target).text();
            this.collection.goTo(page);
        },
        next: function(e) {
            e.preventDefault();
            if (typeof this.collection.currentPage === 'undefined') {
                this.collection.currentPage = 1;
            }
            this.collection.requestNextPage();
        },
        previous: function(e) {
            e.preventDefault();
            this.collection.requestPreviousPage();
        },
        selectAttribute: function(event) {
            event.preventDefault();
            var attribute = JSON.parse($(event.currentTarget).attr('data-attribute'));
            this.model.clear({
                silent: true
            });
            this.model.set(attribute);
        }
    });
    var AttributeSearchCollectibleAttrView = Backbone.View.extend({
        tagName: 'tr',
        events: {},
        initialize: function(options) {},
        render: function() {
            // Update this to be a thumb nail, then the name and the manufacturer or artist, and then collectible type
            // Then underneath a list of all parts
            var self = this;
            var collectible = this.model.toJSON();
            var row = '<td colspan="4">';
            if ($.isArray(collectible.AttributesCollectible) && collectible.AttributesCollectible.length > 0) {
                row += '<ul class="list-unstyled">';
                $.each(collectible.AttributesCollectible, function(index, attribute) {
                    // If there is no category then don't show it, I think this is a problem
                    // right now because we have some attributes that are still in the features list
                    if (typeof attribute.Attribute.AttributeCategory !== 'undefined') {
                        var pathName = attribute.Attribute.AttributeCategory.path_name;
                        var name = attribute.Attribute.name;
                        if (name === '') {
                            name = attribute.Attribute.description;
                        }
                        row += '<li class="attribute" data-id="attribute.Attribute.id" data-attribute=\'' + JSON.stringify(attribute.Attribute) + '\'>';
                        row += '<table>';
                        var attribute = attribute;
                        attribute.AttributesUpload = attribute.Attribute.AttributesUpload;
                        attribute.uploadDirectory = uploadDirectory;
                        dust.render('attribute.default.edit', attribute, function(error, output) {
                            row += output;
                        });
                        row += '</table>';
                        row += '</li>';
                    }
                });
                row += '</ul>';
            } else {
                row += 'No Parts';
            }
            row += '</td>';
            $(self.el).html(row);
            return this;
        },
        buildRow: function() {}
    });
    /**
     *This view is for when we are searching for attributes by part/artist
     * and then displaying those parts
     */
    var AttributeSearchPartAttrView = Backbone.View.extend({
        tagName: 'tr',
        className: 'attribute',
        template: 'attribute.default.edit',
        events: {},
        initialize: function(options) {},
        render: function() {
            var self = this;
            var attribute = this.model.toJSON();
            attribute.uploadDirectory = uploadDirectory;
            dust.render(this.template, attribute, function(error, output) {
                $(self.el).html(output);
            });
            var attrData = attribute.Attribute;
            attrData.AttributesUpload = attribute.AttributesUpload;
            $(self.el).attr('data-attribute', JSON.stringify(attrData));
            return this;
        },
    });
    var AttributeSearchCollectibleView = Backbone.View.extend({
        tagName: 'tr',
        events: {},
        initialize: function(options) {},
        render: function() {
            // Update this to be a thumb nail, then the name and the manufacturer or artist, and then collectible type
            // Then underneath a list of all parts
            var self = this;
            var collectible = this.model.toJSON();
            var row = '<td rowspan="2">' + collectible.Collectible.name;
            if (collectible.Collectible.exclusive) {
                row += ' | Exclusive';
            }
            if (collectible.Collectible.variant) {
                row += ' | Variant';
            }
            row += '</td>';
            row += '<td style="min-width: 100px; max-width: 100px;">';
            if (collectible.CollectiblesUpload && !_.isEmpty(collectible.CollectiblesUpload)) {
                _.each(collectible.CollectiblesUpload, function(collectibleUpload) {
                    if (collectibleUpload.primary) {
                        row += '<a class="thumbnail col-md-12" data-gallery="gallery" href="/' + uploadDirectory + '/' + collectibleUpload.Upload.name + '"><img src="/' + uploadDirectory + '/' + collectibleUpload.Upload.name + '" alt=""></a>';
                    }
                });
            } else {
                row += '<a class="thumbnail col-md-12" href="#"><img src="/img/no-photo.png" alt=""></a>';
            }
            row += '</td>';
            row += '<td>' + collectible.Collectibletype.name + '</td>';
            row += '<td>' + collectible.Manufacture.title + '</td>';
            row += '<td>' + collectible.License.name + '</td>';
            var $listItem = $(self.el).html(row);
            return this;
        },
    });
    /**
     * This view is for setting duplicates, this is a modal.
     *
     * This is how I should do all modals going forward
     */
    var AttributeDuplicateView = Backbone.View.extend({
        template: 'attribute.remove.duplicate',
        modal: 'modal',
        events: {
            'click #select-attribute-link': 'searchCollectible',
            'click #select-attribute-link-by-part': 'searchPart',
            'click .save': 'save'
        },
        initialize: function(options) {
            var self = this;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
        },
        remove: function() {
            if (this.currentView) {
                this.currentView.remove();
            }
            Backbone.View.prototype.remove.call(this);
        },
        renderBody: function() {
            var self = this;
            var data = {
                attribute: this.model.toJSON(),
                uploadDirectory: uploadDirectory
            };
            if (this.replacementAttribute) {
                data.replacementAttribute = {
                    Attribute: this.replacementAttribute.toJSON()
                };
                $(self.el).find('.btn-primary.save').show();
            }
            dust.render(this.template, data, function(error, output) {
                $('.modal-body', self.el).html(output);
            });
        },
        render: function() {
            var self = this;
            dust.render(this.modal, {
                modalId: 'attributeDuplicateModal',
                modalTitle: 'Replace Duplicate Part'
            }, function(error, output) {
                $(self.el).html(output);
            });
            $(self.el).find('.btn-primary.save').text('Replace').hide();
            this.renderBody();
            return this;
        },
        hideMessage: function() {
            $('.message-container', this.el).hide();
        },
        searchCollectible: function() {
            var self = this;
            if (this.replacementAttribute) {
                this.replacementAttribute.off();
                delete this.replacementAttribute;
            }
            // since I don't offer a cancel button :), just
            // create a new one
            this.replacementAttribute = new Backbone.Model();
            if (this.currentView) {
                this.currentView.remove();
            }
            this.replacementAttribute.on('change', function() {
                self.renderBody();
            }, this);
            this.currentView = new AddExistingAttributeCollectibleSearchView({
                collection: new PaginatedCollection(),
                model: this.replacementAttribute
            });
            $('.modal-body', self.el).html(this.currentView.render().el);
            $('.modal-footer .save', self.el).hide();
        },
        searchPart: function() {
            var self = this;
            if (this.replacementAttribute) {
                this.replacementAttribute.off();
                delete this.replacementAttribute;
            }
            this.replacementAttribute = new Backbone.Model();
            this.replacementAttribute = new Backbone.Model();
            if (this.currentView) {
                this.currentView.remove();
            }
            this.replacementAttribute.on('change', function() {
                self.renderBody();
            }, this);
            this.currentView = new AddExistingAttributePartSearchView({
                collection: new PaginatedPart(),
                model: this.replacementAttribute,
                artists: this.artists,
                manufacturers: this.manufacturers,
                categories: this.categories
            });
            $('.modal-body', self.el).html(this.currentView.render().el);
            $('.modal-footer .save', self.el).hide();
        },
        save: function() {
            var self = this;
            // need to pass Attribute.id (which is the model one), Attribute.link = true, Attribute.replace_attribute_id (which is the new one)
            // upon success, we will then update the attribute passed in with the new information and trigger an update
            // create a temp model to act on here because the this.model is
            // an attributes collectible model and we need a subset of that
            // to update the attribute
            $('.save', this.el).button('loading');
            var saveModel = new AttributeModel({
                Attribute: {
                    id: this.model.get('Attribute').id,
                    link: true,
                    'replace_attribute_id': this.replacementAttribute.get('id'),
                    // not sure a reason is necessary for this one
                    reason: 'Duplicate'
                }
            });
            // since this is not a 100% delete we will do a post here
            // instead of a destroy
            saveModel.save({}, {
                url: '/attributes/remove',
                success: function(model, response) {
                    $('.save', this.el).button('reset');
                    if (response.response.isSuccess) {
                        var message = "";
                        // upon success of us switching out the model,
                        // we need to first check to see if is an edit
                        // or not.
                        if (model.get('isEdit')) {
                            message = 'Replacement has been submitted for approval.';
                            self.trigger('modal:close');
                        } else {
                            message = 'Part has been replaced.';
                            var data = {};
                            data.Attribute = model.get('Attribute');
                            data.Attribute.Scale = model.get('Scale');
                            data.Attribute.Manufacture = model.get('Manufacture');
                            data.Attribute.Artist = model.get('Artist');
                            data.Attribute.AttributeCategory = model.get('AttributeCategory');
                            data.Attribute.AttributesUpload = model.get('AttributesUpload');
                            self.model.set(data);
                            self.trigger('modal:close');
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
                        // if it is an edit we flash the message and close
                        // without updating the this.model.
                        // if we did update it, when we modify this.model
                        // and we be done
                    }
                }
            });
        }
    });

    var hasDupList = false;

    return {
        start: function() {
            //TODO Loading usless stuff depending on type.
            // load data from server, populate objects then determine
            // what templates to load from there
            $.blockUI({
                message: '<img src="/img/ajax-loader-circle.gif" />',
                showOverlay: false,
                css: {
                    top: '100px',
                    border: 'none',
                    'background-color': 'transparent',
                    'z-index': 999999
                }
            });


            // Soooo, I could probably make this call when rendering the edit.ctp
            $.ajax({
                url: "/collectibles/getCollectible/" + collectibleId,
                dataType: "json",
                cache: false,
                error: function(jqXHR, textStatus, errorThrown) {
                    jqXHR;
                    textStatus;
                    errorThrown;
                },
                complete: function(jqXHR, textStatus) {
                    jqXHR;
                    textStatus;
                },
                success: function(data, textStatus, jqXHR) {
                    $.unblockUI();
                    // Setup the current model
                    var collectibleModel = new CollectibleModel(data.response.data.collectible.Collectible);
                    var collectibleTypeModel = new CollectibleTypeModel(data.response.data.collectible.Collectibletype);
                    // Setup the manufacturer list, this will contain all data for each manufacturer
                    var manufacturerList = new ManufacturerList(data.response.data.manufacturers);
                    var allManufacturerList = new ManufacturerList(data.response.data.manufacturesList);
                    var currencies = new Currencies(data.response.data.currencies);
                    var scales = new Scales(data.response.data.scales);
                    var attributes = new Attributes(data.response.data.collectible.AttributesCollectible);
                    var uploads = new CollectibleUploads(data.response.data.collectible.CollectiblesUpload, {
                        'id': data.response.data.collectible.Collectible.id
                    });
                    var brands = new Brands(data.response.data.brands);
                    var tags = new Tags(data.response.data.collectible.CollectiblesTag);
                    var artists = new Artists(data.response.data.collectible.ArtistsCollectible);
                    var variants = new Backbone.Collection(data.response.data.variants);
                    var status = new Status();
                    status.set({
                        id: data.response.data.collectible.Collectible.id,
                        status: data.response.data.collectible.Status
                    }, {
                        silent: true
                    });
                    // This could probably go in the init method but works here for now
                    var selectedManufacturer = null;
                    _.each(manufacturerList.models, function(manufacturer) {
                        if (manufacturer.get('id') === collectibleModel.get('manufacture_id')) {
                            selectedManufacturer = manufacturer;
                            return;
                        }
                    });
                    // Setup global events
                    pageEvents.on('status:change:error', function(errors) {
                        if (messageView) {
                            messageView.remove();
                            messageView = null;
                        }
                        messageView = new MessageView({
                            errors: new Errors(errors)
                        });
                        $('#message-container').html(messageView.render().el);
                    });
                    pageEvents.on('status:change:error:severe', function() {
                        if (messageView) {
                            messageView.remove();
                            messageView = null;
                        }
                        messageView = new SevereMessageView({});
                        $('#message-container').html(messageView.render().el);
                    });
                    pageEvents.on('status:change:dupList', function(collectibles) {
                        hasDupList = true;
                        status.set({
                            'hasDupList': hasDupList
                        });
                        if (messageView) {
                            messageView.remove();
                            messageView = null;
                        }
                        messageView = new DupListMessageView({
                            collection: new Collectibles(collectibles)
                        });
                        $('#message-container').html(messageView.render().el);
                    });
                    pageEvents.on('upload:close', function() {
                        uploads.fetch();
                    });
                    // Setup views
                    var collectibleViewData = {
                        model: collectibleModel,
                        manufacturers: manufacturerList,
                        manufacturer: selectedManufacturer,
                        currencies: currencies,
                        scales: scales,
                        status: status,
                        collectibleType: collectibleTypeModel,
                        brands: brands,
                        template: 'collectible.default.edit',
                        pageEvents: pageEvents
                    };
                    if (collectibleModel.get('custom')) {
                        collectibleViewData.template = 'collectible.custom.edit';
                        collectibleViewData.customStatuses = new Backbone.Collection(data.response.data.customStatuses);
                    } else if (collectibleModel.get('original')) {
                        collectibleViewData.template = 'collectible.original.edit';
                    }
                    //TODO: At some point it might warrant a whole new view for customs
                    var collectibleView = new CollectibleView(collectibleViewData);
                    $('#photo-container').append(new PhotoView({
                        collection: uploads,
                        eventManager: pageEvents
                    }).render().el);
                    $('#collectible-container').append(new PersonsView({
                        collection: artists,
                        collectibleType: collectibleTypeModel
                    }).render().el);
                    $('#collectible-container').append(collectibleView.render().el);
                    $('#attributes-container').append(new AttributesView({
                        collection: attributes,
                        status: status,
                        artists: new Backbone.Collection(data.response.data.artists),
                        manufacturers: allManufacturerList,
                        categories: new Backbone.Collection(data.response.data.categories),
                        collectible: collectibleModel,
                        scales: scales
                    }).render().el);
                    var statusView = new StatusView({
                        model: status,
                        collectible: collectibleModel,
                        // we set allow edit here and then base everything off of status in the template, kind of sucks
                        allowEdit: true,
                        allowDelete: allowDelete
                    });
                    $('#status-container').html(statusView.render().el);
                    // if there is a delete from the status view that is a prompt
                    statusView.on('delete:collectible:prompt', function() {
                        // display modal about delete, this is most likely for cases where this
                        // collectible is active and we need to delete it
                        // this will allow the user to enter in an id to replace this collectible with
                        var collectibleDeleteView = new CollectibleDeleteView({
                            model: collectibleModel,
                            variants: variants
                        });
                        var modal = new Backbone.BootstrapModal({
                            content: collectibleDeleteView,
                            title: 'Delete Collectible',
                            animate: true,
                            okCloses: false
                        }).open();
                    }, this);
                    $('#collectible-container').append(new TagsView({
                        collection: tags
                    }).render().el);


                    // Make sure we only have one
                    var messageView = null;
                    // If the status has changed and I am on the view
                    //page and they change the status and it is a draft
                    // go to the edit page
                    status.on('sync', function() {
                        if (this.toJSON().status.id === '2' || this.toJSON().status.id === '4') {
                            window.location.href = '/collectibles/view/' + this.id;
                        }
                    }, status);
                    collectibleModel.on('destroy', function() {
                        window.location.href = '/users/home';
                    });
                    if (collectibleModel.get('custom')) {
                        // view is overkill here
                        dust.render('directional.custom', {}, function(error, output) {
                            $('#directional-text-container').html(output);
                        });
                    } else if (collectibleModel.get('original')) {
                        // view is overkill here
                        dust.render('directional.original', {}, function(error, output) {
                            $('#directional-text-container').html(output);
                        });
                    } else {
                        // view is overkill here
                        dust.render('directional.page', {}, function(error, output) {
                            $('#directional-text-container').html(output);
                        });
                    }
                }
            });
        }
    }
});