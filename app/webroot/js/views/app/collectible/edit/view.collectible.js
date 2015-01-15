define(['backbone', 'jquery', 'models/model.series', 'models/model.company', 'views/app/collectible/edit/view.company', 'collections/collection.brands', 'views/app/collectible/edit/view.series', 'views/app/collectible/edit/view.company.series', 'select2'], function(Backbone, $, SeriesModel, CompanyModel, CompanyView, Brands, SeriesView, CompanySeriesView) {
    var lastResults = [];
    var CollectibleView = Backbone.View.extend({
        className: "row",
        events: {
            'change #inputManufacturer': 'changeManufacturer',
            'click #buttonSeries': 'changeSeries',
            'click .save': 'save',
            "change input": "fieldChanged",
            "change select": "selectionChanged",
            'change textarea': 'fieldChanged',
            'click .manufacturer-add': 'addManufacturer',
            'click .manufacturer-edit': 'editManufacturer',
            'click .manufacturer-add-brand': 'editManufacturer',
            'click .manufacturer-add-category': 'editManufacturerSeries'
        },
        initialize: function(options) {
            var self = this;
            this.pageEvents = options.pageEvents;
            this.template = options.template;
            this.manufacturers = options.manufacturers;
            this.currencies = options.currencies;
            // this.retailers = options.retailers;
            this.scales = options.scales;
            this.collectibleType = options.collectibleType;
            this.brands = options.brands;
            this.status = options.status;
            this.customStatuses = options.customStatuses;
            // this is information on the selected manufacturer
            if (options.manufacturer) {
                this.manufacturer = options.manufacturer;
                this.series = new SeriesModel({
                    id: this.manufacturer.get('id')
                });
                this.seriesEdit = new SeriesModel({
                    id: this.manufacturer.get('id'),
                    mode: 'edit'
                });
            } else {
                this.series = new SeriesModel();
                this.seriesEdit = new SeriesModel({
                    mode: 'edit'
                });
            }
            // do other init things
            // create years
            var minOffset = -3,
                maxOffset = 100;
            // Change to whatever you want
            var thisYear = (new Date()).getFullYear();
            this.years = [];
            for (var i = minOffset; i <= maxOffset; i++) {
                var year = thisYear - i;
                this.years.push(year);
            }
            //setup model events
            this.model.on("change:manufacture_id", function() {
                this.model.set({
                    seriesPath: '',
                    'series_id': null,
                    'license_id': null
                }, {
                    silent: true
                });
                this.render();
            }, this);
            this.model.on('change:retailer', function() {
                this.model.set({
                    'retailer_id': null
                }, {
                    silent: true
                });
            }, this);
            this.model.on("change:limited", this.render, this);
            this.model.on("change:edition_size", this.render, this);
            // this.model.on("change:series_id", this.render, this);
            this.manufacturers.on('add', this.render, this);
            this.seriesView = null;
            this.series.on('change', function() {
                if (this.seriesView) {
                    this.seriesView.remove();
                }
                this.seriesView = new SeriesView({
                    model: this.series
                });

                this.seriesView.on('series:select', function(id, name) {
                    // After bootstrap 3 upgrade, setting this to silent and
                    // using the hide event call back to rerender
                    this.model.set({
                        seriesPath: name,
                        'series_id': id
                    }, {
                        silent: true
                    });
                    $('#seriesModal').modal('hide');
                }, this);

                $.unblockUI();
                $('.modal-body', '#seriesModal').html(this.seriesView.render().el);
                $('#seriesModal').modal();
                // After Boostrap 3 upgrade, had to do this to rerender
                // over a change event on the model
                $('#seriesModal').on('hidden.bs.modal', function() {
                    self.render();
                });
            }, this);

            this.seriesEdit.on('change', function() {
                var self = this;
                if (this.manufacturerSeriesView) {
                    this.manufacturerSeriesView.remove();
                }
                this.manufacturerSeriesView = new CompanySeriesView({
                    model: this.seriesEdit,
                    manufacturer: this.manufacturer
                });
                $.unblockUI();
                $('body').append(this.manufacturerSeriesView.render().el);
                $('#manufacturerSeriesModal', 'body').modal({
                    backdrop: 'static'
                });
                $('#manufacturerSeriesModal', 'body').on('hidden.bs.modal', function() {
                    self.manufacturerSeriesView.remove();
                });
            }, this);

            this.model.on('sync', this.onModelSaved, this);

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

            // if the model is ever invalid, because we saved...reset the button
            this.listenTo(this.model, 'validated:invalid', function() {
                $('.save', this.el).button('reset');
            });
        },
        onModelSaved: function(model, response, options) {
            $('.save', this.el).button('reset');
            var message = 'Your changes have been successfully saved!';
            if (response.isEdit) {
                message = 'Your edit has been successfully submitted!';
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
        },
        render: function() {
            var self = this;
            var collectibleType = this.collectibleType.toJSON();
            var collectible = this.model.toJSON();
            var status = this.status.toJSON();
            var data = {
                collectible: collectible,
                manufacturers: this.manufacturers.toJSON(),
                currencies: this.currencies.toJSON(),
                years: this.years,
                scales: this.scales.toJSON(),
                collectibleType: collectibleType,
                brands: this.brands.toJSON()
            };
            // If it is a custom, we are not showing
            // the manufacturer list cause it doesn't make sense
            // but we do want to show the brand list
            if (collectible.custom) {
                data.renderManList = false;
                data.renderBrandList = true;
                data.customStatuses = this.customStatuses.toJSON();
            } else if (this.manufacturer) {
                data.renderManList = true;
                data.manufacturer = this.manufacturer.toJSON();
                // use this to sort but then add back to the manufacturer so we don't
                // have to pass more data around
                var brands = new Brands(data.manufacturer.LicensesManufacture);
                data.manufacturer.LicensesManufacture = brands.toJSON();
                // if there is a manufacturer, don't render the main brand
                // list cause we will use the ones linked to that manufacturer
                data.renderBrandList = false;
            } else {
                // If there is no manufacturer selected, render the man list
                // and the brand list
                data.renderManList = true;
                data.renderBrandList = true;
            }
            // If this collectible is submitted and we are
            // editing it. Do not allow adding new
            // manufacturer
            if (status.status.id === '4') {
                data.allowAddManufacturer = false;
            } else {
                data.allowAddManufacturer = true;
            }
            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });

            $('.retailers-typeahead', self.el).select2({
                placeholder: 'Search or add a new venue/retailer.',
                minimumInputLength: 1,
                ajax: {
                    url: "/retailers/retailers",
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            query: term, // search term
                            page_limit: 100
                        };
                    },
                    results: function(data, page) {
                        lastResults = data;
                        return {
                            results: data
                        };
                    }
                },
                initSelection: function(element, callback) {
                    // the input tag has a value attribute preloaded that points to a preselected movie's id
                    // this function resolves that id attribute to an object that select2 can render
                    // using its formatResult renderer - that way the movie name is shown preselected
                    var id = $(element).val();
                    if (id !== "") {
                        callback({
                            id: id,
                            name: self.model.get('retailer')
                        });
                    }
                },
                formatResult: function(item) {
                    return item.name;
                },
                formatSelection: function(item) {
                    return item.name;
                },
                createSearchChoice: function(term, data) {
                    if (lastResults.some(function(r) {
                        return r.name == term
                    })) {
                        return {
                            id: data.id,
                            name: term,
                            created: false
                        };
                    } else {
                        return {
                            id: term,
                            name: term,
                            created: true
                        };
                    }
                },
                allowClear: true,
                dropdownCssClass: "bigdrop"
            }).on('change', function(val, added, removed) {
                var data = $('.retailers-typeahead', self.el).select2('data');
                if (!data || !data.name) {
                    self.model.unset('retailer', {
                        forceUpdate: true
                    });
                    self.model.unset('retailer_id', {
                        forceUpdate: true
                    });

                    return;
                }

                if (data.created) {
                    self.model.set({
                        retailer: data.name,

                    }, {
                        forceUpdate: true
                    });
                } else {
                    self.model.set({
                        retailer: data.name,
                        retailer_id: data.id
                    }, {
                        forceUpdate: true
                    });
                }
            });

            return this;
        },
        changeManufacturer: function(event) {
            var field = $(event.currentTarget);
            var value = $("option:selected", field).val();
            // we also need to change the update manufacturer
            // will make it easier for the template
            var selectedManufacturer = null;
            _.each(this.manufacturers.models, function(manufacturer) {
                if (manufacturer.get('id') === value) {
                    selectedManufacturer = manufacturer;
                    return;
                }
            });
            this.manufacturer = selectedManufacturer;
            // Change the id on the series
            if (this.manufacturer !== null) {
                this.series.set({
                    id: this.manufacturer.get('id')
                }, {
                    silent: true
                });
            } else {
                this.series.set({
                    id: ''
                }, {
                    silent: true
                });
            }
        },
        changeSeries: function(event) {
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
            // This is a little ghetto, there should
            // be a better way to do this
            // Do a clear to make sure we are always getting new data
            // but then we need to set the id
            // then do a fetch
            this.series.clear({
                silent: true
            });
            this.series.set({
                id: this.manufacturer.get('id')
            }, {
                silent: true
            });
            this.series.fetch();
        },
        save: function(event) {
            var self = this;
            event.preventDefault();
            $(event.currentTarget).button('loading');
            // OK, doing this, and this might be alittle hacky but because we have some old data
            // that is escaped incorrectly in the database, it is failing validation.  The textarea should
            // be properly escaped, so always pull the description and reset it.
            var description = $('textarea[name=description]', this.el).val();
            if (description) {
                // this fixes all of those douchy curly quotes that aren't standard.
                this.model.set('description', description.replace(/[\u2018\u2019]/g, "'")
                    .replace(/[\u201C\u201D]/g, '"'));
            }

            this.model.save({}, {
                wait: true,
                error: function(model, response) {
                    $(event.currentTarget).button('reset');
                    if (response.status === 401) {
                        var errors = [];
                        errors.push({
                            message: ['You do not have access.']
                        });
                        self.pageEvents.trigger('status:change:error', errors);
                    }
                }
            });
        },
        selectionChanged: function(e) {
            var field = $(e.currentTarget);
            var value = $("option:selected", field).val();
            var data = {};
            data[field.attr('name')] = value;
            this.model.set(data, {
                forceUpdate: true
            });
        },
        fieldChanged: function(e) {
            var field = $(e.currentTarget);
            var data = {};
            if (field.attr('type') === 'checkbox') {
                if (field.is(':checked')) {
                    data[field.attr('name')] = true;
                } else {
                    data[field.attr('name')] = false;
                }
            } else {
                data[field.attr('name')] = field.val();
            }
            this.model.set(data, {
                forceUpdate: true
            });
        },
        addManufacturer: function() {
            var self = this;
            if (this.manufacturerView) {
                this.manufacturerView.remove();
            }
            var manufacturer = new CompanyModel();
            manufacturer.on('sync', function() {
                $.blockUI({
                    message: '<button class="close" data-dismiss="alert" type="button">×</button>Your manufacturer has been added!',
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
                // unbind all
                manufacturer.off();
                this.manufacturers.add(manufacturer);
                $('#manufacturerModal', 'body').modal('hide');
            }, this);
            this.manufacturerView = new CompanyView({
                model: manufacturer,
                brands: this.brands,
                mode: 'add'
            });
            $('body').append(this.manufacturerView.render().el);
            $('#manufacturerModal', 'body').modal({
                backdrop: 'static'
            });
            $('#manufacturerModal', 'body').on('hidden.bs.modal', function() {
                self.manufacturerView.remove();
            });
        },
        editManufacturer: function() {
            var self = this;
            if (this.manufacturerView) {
                this.manufacturerView.remove();
            }
            this.manufacturer.on('sync', function() {
                $.blockUI({
                    message: '<button class="close" data-dismiss="alert" type="button">×</button>The manufacturer has been updated!',
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
                this.render();
                // unbind all
                this.manufacturer.off();
                $('#manufacturerModal', 'body').modal('hide');
            }, this);
            this.manufacturerView = new CompanyView({
                model: this.manufacturer,
                brands: this.brands,
                mode: 'edit'
            });
            $('body').append(this.manufacturerView.render().el);
            $('#manufacturerModal', 'body').modal({
                backdrop: 'static'
            });
            $('#manufacturerModal', 'body').on('hidden.bs.modal', function() {
                self.manufacturerView.remove();
                // on close I need to make sure we do not have
                // any brands attached to this manufacturer that are
                // not officially saved
                if (self.manufacturer.get('LicensesManufacture')) {
                    var brands = self.manufacturer.get('LicensesManufacture');
                    var len = brands.length;
                    while (len--) {
                        brand = brands[len];
                        if (!brand.hasOwnProperty('id')) {
                            brands.splice(len, 1);
                        }
                    }
                    self.manufacturer.set({
                        LicensesManufacture: brands
                    }, {
                        silent: true
                    });
                }
            });
        },
        editManufacturerSeries: function() {
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
            // This is a little ghetto, there should
            // be a better way to do this
            // Do a clear to make sure we are always getting new data
            // but then we need to set the id
            // then do a fetch
            this.seriesEdit.clear({
                silent: true
            });
            this.seriesEdit.set({
                id: this.manufacturer.get('id'),
                mode: 'edit'
            }, {
                silent: true
            });
            this.seriesEdit.fetch();
        }
    });

    return CollectibleView;
});