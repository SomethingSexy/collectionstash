define(['backbone', 'marionette', 'jquery', 'dust', 'mustache', 'marionette.mustache',
    'views/common/view.alert',
    'views/app/collectible/edit/view.collectible.delete',
    'views/app/collectible/edit/view.collectible',
    'views/app/collectible/edit/view.persons',
    'views/app/collectible/edit/view.tags',
    'views/app/collectible/edit/view.collectible.parts',
    'views/app/collectible/edit/view.collectible.part.edit',
    'views/app/collectible/edit/view.part.edit',
    'views/common/modal.region',
    'models/model.collectible',
    'models/model.status',
    'views/view.status',
    'collections/collection.collectibles',
    'collections/collection.parts',
    'collections/collection.collectible.parts',
    'models/model.company',
    'collections/collection.brands',
    'text!templates/app/collectible/edit/layout.parts.mustache',
    // todo: old templates remove as converted to mustache and individual view files
    'text!templates/collectibles/collectible.default.dust',
    'text!templates/collectibles/photo.default.dust',
    'text!templates/collectibles/status.dust',
    'text!templates/collectibles/message.dust',
    'text!templates/collectibles/message.error.severe.dust',
    'text!templates/collectibles/message.duplist.dust',
    'text!templates/collectibles/modal.dust',
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
    'text!templates/common/alert.dust',
    'jquery.form', 'jquery.treeview', 'cs.core.tree', 'jquery.getimagedata', 'jquery.iframe-transport', 'cors/jquery.postmessage-transport', 'jquery.fileupload', 'jquery.fileupload-fp', 'jquery.fileupload-ui', "jquery.ui.widget", 'blockui', 'backbone.validation'
], function(Backbone, Marionette, $, dust, mustache, marionetteMustache, AlertView, CollectibleDeleteView, CollectibleView, PersonsView, TagsView, PartsView, CollectiblePartEditView, PartEditView, ModalRegion, CollectibleModel, Status, StatusView, PaginatedCollection, PaginatedPart, CollectibleParts, CompanyModel, Brands, partsLayoutTemplate, collectibleTemplate, photoTemplate, statusTemplate, messageTemplate, messageSevereTemplate, dupListTemplate, modalTemplate, attributeUploadTemplate, directionalTemplate, attributeAddExistingTemplate, attributeAddExistingSearchTemplate, pagingTemplate, directionalCustomTemplate, customTemplate, attributeAddExistingSearchPartTemplate, partTemplate, attributeRemoveDuplicate, originalTemplate, directionalOriginalTemplate, alertTemplate) {
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
    dust.loadSource(dust.compile(statusTemplate, 'status.edit'));
    dust.loadSource(dust.compile(messageTemplate, 'message.edit'));
    dust.loadSource(dust.compile(messageSevereTemplate, 'message.error.severe'));
    dust.loadSource(dust.compile(dupListTemplate, 'message.duplist'));
    dust.loadSource(dust.compile(modalTemplate, 'modal'));
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

    var CurrencyModel = Backbone.Model.extend({});
    var Currencies = Backbone.Collection.extend({
        model: CurrencyModel
    });
    var Scale = Backbone.Model.extend({});
    var Scales = Backbone.Collection.extend({
        model: Scale
    });

    var ManufacturerList = Backbone.Collection.extend({
        model: CompanyModel,
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
            return '/collectibles/tag/';
        }
    });
    var Tags = Backbone.Collection.extend({
        model: TagModel,
        urlRoot: '/collectibles/tags'
    });
    var ArtistModel = Backbone.Model.extend({
        urlRoot: function() {
            return '/collectibles/artist/';
        }
    });
    var Artists = Backbone.Collection.extend({
        model: ArtistModel,
        urlRoot: '/collectibles/artists'
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
                for (var i = 1; i <= this.collection.state.totalPages; i++) {
                    pagesArray.push(i);
                }
                var data = {
                    pages: pagesArray
                };
                if (this.collection.currentPage) {
                    data['paginator'] = {
                        currentPage: this.collection.state.currentPage,
                        firstPage: this.collection.state.firstPage,
                        perPage: this.collection.state.perPage,
                        totalPages: this.collection.state.totalPages,
                        total: this.collection.state.total
                    };
                } else {
                    data['paginator'] = this.collection.state;
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
            var row = '<td rowspan="2">' + collectible.name;
            if (collectible.exclusive) {
                row += ' | Exclusive';
            }
            if (collectible.variant) {
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

    // doing this until everything is converted over to a
    // marionette app
    function renderPartsView(layout, options) {
        var partsView = new PartsView(options);

        partsView.on('edit:collectible:part', function(model) {
            layout.modal.show(new CollectiblePartEditView({
                model: model,
                // later this will come from the app
                collectible: options.model
            }));

            model.once('sync', function(model, response, options) {
                if (_.isArray(response)) {
                    // App.comments.add(response);
                }

                layout.modal.hideModal();
            });
        });

        partsView.on('edit:part', function(model) {
            layout.modal.show(new PartEditView({
                model: model,
                // later this will come from the app
                collectible: options.model,
                manufacturers: options.manufacturers,
                artists: options.artists,
                scales: options.scales
            }));

            model.once('sync', function(model, response, options) {
                if (_.isArray(response)) {
                    // App.comments.add(response);
                }

                layout.modal.hideModal();
            });
        });

        layout.parts.show(partsView);
    }


    return {
        start: function() {
            // Setup the current model
            var collectibleModel = new CollectibleModel(rawCollectible.Collectible);
            var collectibleTypeModel = new CollectibleTypeModel(rawCollectible.Collectibletype);
            // Setup the manufacturer list, this will contain all data for each manufacturer
            var manufacturerList = new ManufacturerList(rawManufacturers);
            var allManufacturerList = new ManufacturerList(rawManufacturesList);
            var currencies = new Currencies(rawCurrencies);
            var scales = new Scales(rawScales);
            var parts = new CollectibleParts(rawParts, {
                parse: true
            });
            var uploads = new CollectibleUploads(rawCollectible.CollectiblesUpload, {
                'id': collectibleId
            });
            var brands = new Brands(rawBrands);
            var tags = new Tags(rawCollectible.CollectiblesTag);
            var artists = new Artists(rawCollectible.ArtistsCollectible);
            var variants = new Backbone.Collection(rawVariants);
            var status = new Status();
            status.set({
                id: collectibleId,
                status: rawCollectible.Status
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
            status.on('status:change:error', function(errors) {
                if (messageView) {
                    messageView.remove();
                    messageView = null;
                }
                messageView = new MessageView({
                    errors: new Errors(errors)
                });
                $('#message-container').html(messageView.render().el);
            });
            status.on('status:change:error:severe', function() {
                if (messageView) {
                    messageView.remove();
                    messageView = null;
                }
                messageView = new SevereMessageView({});
                $('#message-container').html(messageView.render().el);
            });
            status.on('status:change:dupList', function(collectibles) {
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
                uploads.fetch({
                    reset: true
                });
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
                collectibleViewData.customStatuses = new Backbone.Collection(rawCustomStatuses);
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
            $('#collectible-container').append(new TagsView({
                collection: tags
            }).render().el);

            // TODO: make this it's own region so we can utilize the modal stuff
            // and it will handle rendering and destroying views since we will be 
            // utilizing this space when we add existing

            var PartsLayout = Backbone.Marionette.Layout.extend({
                template: partsLayoutTemplate,
                className: 'row',
                regions: {
                    actions: '._actions',
                    parts: '._parts',
                    // main: '._main',
                    modal: ModalRegion
                }
            });

            var partsLayout = new PartsLayout();
            $('#parts-container').html(partsLayout.render().el);

            renderPartsView(partsLayout, {
                collection: parts,
                status: status,
                artists: new Backbone.Collection(rawArtists),
                manufacturers: allManufacturerList,
                categories: new Backbone.Collection(rawCategories),
                model: collectibleModel,
                scales: scales
            });

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
    }
});