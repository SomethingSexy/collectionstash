define(function(require) {
    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        $ = require('jquery'),
        dust = require('dust'),
        mustache = require('mustache'),
        marionetteMustache = require('marionette.mustache'),
        _ = require('underscore'),
        AlertView = require('views/common/view.alert'),
        CollectibleDeleteView = require('views/app/collectible/edit/view.collectible.delete'),
        CollectibleView = require('views/app/collectible/edit/view.collectible'),
        PersonsView = require('views/app/collectible/edit/view.persons'),
        TagsView = require('views/app/collectible/edit/view.tags'),
        PartsView = require('views/app/collectible/edit/view.collectible.parts'),
        CollectiblePartEditView = require('views/app/collectible/edit/view.collectible.part.edit'),
        PartEditView = require('views/app/collectible/edit/view.part.edit'),
        PartPhotoEditView = require('views/app/collectible/edit/view.part.photo.edit'),
        PartRemoveView = require('views/app/collectible/edit/view.collectible.part.remove'),
        PartRemoveDuplicateView = require('views/app/collectible/edit/view.collectible.part.remove.duplicate'),
        PartAddExistingView = require('views/app/collectible/edit/view.part.add.existing'),
        CollectibleSearchView = require('views/app/collectible/edit/view.collectible.search'),
        ModalRegion = require('views/common/modal.region'),
        CollectibleModel = require('models/model.collectible'),
        Status = require('models/model.status'),
        StatusView = require('views/view.status'),
        PaginatedCollection = require('collections/collection.collectibles'),
        PaginatedPart = require('collections/collection.parts'),
        CollectibleParts = require('collections/collection.collectible.parts'),
        CompanyModel = require('models/model.company'),
        Brands = require('collections/collection.brands'),
        partsLayoutTemplate = require('text!templates/app/collectible/edit/layout.parts.mustache'),
        collectibleTemplate = require('text!templates/collectibles/collectible.default.dust'),
        photoTemplate = require('text!templates/collectibles/photo.default.dust'),
        statusTemplate = require('text!templates/collectibles/status.dust'),
        messageTemplate = require('text!templates/collectibles/message.dust'),
        messageSevereTemplate = require('text!templates/collectibles/message.error.severe.dust'),
        dupListTemplate = require('text!templates/collectibles/message.duplist.dust'),
        modalTemplate = require('text!templates/collectibles/modal.dust'),
        directionalTemplate = require('text!templates/collectibles/directional.dust'),
        pagingTemplate = require('text!templates/common/paging.dust'),
        directionalCustomTemplate = require('text!templates/collectibles/directional.custom.dust'),
        customTemplate = require('text!templates/collectibles/collectible.custom.dust'),
        originalTemplate = require('text!templates/collectibles/collectible.original.dust'),
        directionalOriginalTemplate = require('text!templates/collectibles/directional.original.dust'),
        alertTemplate = require('text!templates/common/alert.dust');
    require('jquery.form');
    require('jquery.treeview');
    require('cs.core.tree');
    require('jquery.getimagedata');
    require('jquery.iframe-transport');
    require('cors/jquery.postmessage-transport');
    require('jquery.fileupload');
    require('jquery.fileupload-fp');
    require('jquery.fileupload-ui');
    require("jquery.ui.widget");
    require('blockui');
    require('backbone.validation');
    require('backbone.bootstrap-modal');
    require('jquery.blueimp-gallery');
    /**
     * TODO: Known Issues:
     * - If you add a brand to a manufacturer, then go back to that list and find a brand, it won't
     *  exist in there
     */
    dust.loadSource(dust.compile(collectibleTemplate, 'collectible.default.edit'));
    dust.loadSource(dust.compile(photoTemplate, 'photo.default.edit'));
    dust.loadSource(dust.compile(statusTemplate, 'status.edit'));
    dust.loadSource(dust.compile(messageTemplate, 'message.edit'));
    dust.loadSource(dust.compile(messageSevereTemplate, 'message.error.severe'));
    dust.loadSource(dust.compile(dupListTemplate, 'message.duplist'));
    dust.loadSource(dust.compile(modalTemplate, 'modal'));
    dust.loadSource(dust.compile(directionalTemplate, 'directional.page'));
    dust.loadSource(dust.compile(directionalCustomTemplate, 'directional.custom'));
    dust.loadSource(dust.compile(customTemplate, 'collectible.custom.edit'));
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
    var hasDupList = false;
    // doing this until everything is converted over to a
    // marionette app
    function renderPartsView(layout, options) {
        var partsView = new PartsView(options);
        partsView.on('edit:collectible:part', _.partial(renderCollectiblePartEdit, _, layout, options));
        partsView.on('edit:part', _.partial(renderEditPart, _, layout, options));
        partsView.on('edit:part:photo', _.partial(renderEditPartPhoto, _, layout, options));
        partsView.on('remove:part', _.partial(renderRemovePart, _, layout, options));
        partsView.on('remove:part:duplicate', _.partial(renderRemovePartDuplicate, layout, options));
        // TODO: this won't work here, needs to come from the layout
        layout.on('add:part', _.partial(renderAddPart, layout, options));
        layout.on('add:existing:part', _.partial(renderAddExistingPart, layout, options));
        layout.parts.show(partsView);
    }

    function renderCollectiblePartEdit(model, layout, options) {
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
    }

    function renderEditPart(model, layout, options) {
        var editPartView = new PartEditView({
            model: model,
            // later this will come from the app
            collectible: options.model,
            manufacturers: options.manufacturers,
            artists: options.artists,
            scales: options.scales
        });
        layout.modal.show(editPartView);
        model.once('sync', function(model, response, options) {
            if (_.isArray(response)) {
                // App.comments.add(response);
            }
            layout.modal.hideModal();
        });
    }

    function renderEditPartPhoto(model, layout, options) {
        var editPartView = new PartPhotoEditView({
            model: model,
            collectible: options.model,
            collection: model.part.photos
        });
        layout.modal.show(editPartView);
        $('#attribute-upload-dialog', 'body').on('hidden.bs.modal', function() {
            self.photoEditView.remove();
            self.model.fetch();
        });
        model.once('sync', function(model, response, options) {
            if (_.isArray(response)) {
                // App.comments.add(response);
            }
            layout.modal.hideModal();
        });
    }

    function renderAddExistingPart(layout, options, part) {
        var parts = options.collection;
        //  this won't be a modal, isntead it will render where the list of parts is
        var addPartView = new PartAddExistingView({
            model: part,
            // later this will come from the app
            collectible: options.model
        });
        addPartView.on('search:collectible', function() {
            var searchView = new CollectibleSearchView({
                // although we might want to keep this cached?
                collection: new PaginatedCollection([], {
                    mode: 'server'
                })
            });
            searchView.on('part:selected', _.partial(renderAddExistingPart, layout, options));
            searchView.on('cancel', _.partial(renderPartsView, layout, options));
            layout.parts.show(searchView);
        });
        addPartView.on('cancel', _.partial(renderPartsView, layout, options));
        addPartView.on('part:added', function(model) {
            // if it is an edit, there won't be a model
            // passed in right now
            if (model) {
                options.collection.add(model);
            }
            renderPartsView(layout, options);
        });
        layout.parts.show(addPartView);
    }

    function renderAddPart(layout, options) {
        var parts = options.collection,
            model = new options.collection.model();
        var addPartView = new PartEditView({
            model: model,
            // later this will come from the app
            collectible: options.model,
            manufacturers: options.manufacturers,
            artists: options.artists,
            scales: options.scales
        });
        layout.modal.show(addPartView);
        model.once('sync', function(model, response, options) {
            // if it isn't an edit, then add it to the collection
            // otherwise, do nothing with it
            // if (!response.isEdit) {
            parts.add(model);
            // }
            layout.modal.hideModal();
        });
    }

    function renderRemovePart(model, layout, options) {
        var removePartView = new PartRemoveView({
            model: model,
            // later this will come from the app
            collectible: options.model,
        });
        layout.modal.show(removePartView);
        model.once('sync', function(model, response, options) {
            if (_.isArray(response)) {
                // App.comments.add(response);
            }
            layout.modal.hideModal();
        });
    }

    function renderRemovePartDuplicate(layout, options, part, replacement) {
        var parts = options.collection;
        //  this won't be a modal, isntead it will render where the list of parts is
        var addPartView = new PartRemoveDuplicateView({
            model: part,
            replacement: replacement,
            // later this will come from the app
            collectible: options.model
        });
        addPartView.on('search:collectible', function() {
            var searchView = new CollectibleSearchView({
                // although we might want to keep this cached?
                collection: new PaginatedCollection([], {
                    mode: 'server'
                })
            });
            searchView.on('part:selected', _.partial(renderRemovePartDuplicate, layout, options, part));
            searchView.on('cancel', _.partial(renderPartsView, layout, options));
            layout.parts.show(searchView);
        });
        addPartView.on('cancel', _.partial(renderPartsView, layout, options));
        addPartView.on('part:added', function(model) {
            renderPartsView(layout, options);
        });
        layout.parts.show(addPartView);
    };
    // mock app until we fully convert to marionette
    return {
        start: function() {
            // Setup the current model
            var collectibleModel = new CollectibleModel(rawCollectible.Collectible);
            var collectibleTypeModel = new CollectibleTypeModel(rawCollectible.Collectibletype);
            // Setup the manufacturer list, this will contain all data for each manufacturer
            var manufacturerList = new ManufacturerList(rawManufacturers);
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
            manufacturerList.each(function(manufacturer) {
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
                events: {
                    'click #add-existing-item-link': 'addExisting',
                    'click #add-new-item-link': 'addNew'
                },
                regions: {
                    actions: '._actions',
                    parts: '._parts',
                    // main: '._main',
                    modal: ModalRegion
                },
                addNew: function() {
                    this.trigger('add:part');
                },
                addExisting: function() {
                    this.trigger('add:existing:part');
                }
            });
            var partsLayout = new PartsLayout();
            $('#parts-container').html(partsLayout.render().el);
            renderPartsView(partsLayout, {
                collection: parts,
                status: status,
                artists: new Backbone.Collection(rawArtists),
                manufacturers: manufacturerList,
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