(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['backbone', 'dust', 'text!templates/collectibles/status.dust', 'dust-helpers'], factory);
    } else {
        // Browser globals
        root.StatusView = factory(root.Backbone, root.dust);
    }
}(this, function(backbone, dust, template) {
    // only do this for the AMD version
    if (template && dust) {
        dust.loadSource(dust.compile(template, 'status.edit'));
    }

    var StatusView = Backbone.View.extend({
        template: 'status.edit',
        className: "col-md-12",
        events: {
            'click .submit': 'changeStatus',
            'click .delete': 'remove'
        },
        initialize: function(options) {
            options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
            this.collectible = options.collectible ? options.collectible : {};
            this.allowDelete = (options.allowDelete && options.allowDelete === true) ? true : false;
            //this.model.on("change", this.render, this);
        },
        render: function() {
            var self = this;

            var model = this.model.toJSON();
            model.allowEdit = this.allowEdit;
            model.allowDelete = this.allowDelete;
            if (this.collectible) {
                model.collectible = this.collectible.toJSON();
            }
            dust.render(this.template, model, function(error, output) {
                $(self.el).html(output);
            });

            $('.merchants-typeahead', this.el).select2({
                placeholder: 'Search or add a new merchant.',
                minimumInputLength: 1,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "/merchants/merchants",
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
                            name: $('#CollectiblesUserMerchantValue').val()
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
                            name: name
                        };
                    } else {
                        return {
                            id: term,
                            name: term
                        };
                    }
                },
                allowClear: true,
                dropdownCssClass: "bigdrop"
            }).on('change', function(val, added, removed) {
                var data = $('.merchants-typeahead', self.el).select2('data');
                if (!data || !data.name) {
                    $('#CollectiblesUserMerchantValue').val('');
                    return;
                }
                $('#CollectiblesUserMerchantValue').val(data.name);
            });

            return this;
        },
        changeStatus: function(event) {
            $(event.currentTarget).button('loading');
            this.model.save({}, {
                error: function(model, response) {
                    $(event.currentTarget).button('reset');

                    if (response.status === 500) {
                        pageEvents.trigger('status:change:error:severe');
                    } else {

                        var responseObj = $.parseJSON(response.responseText);
                        if (responseObj.response.data.hasOwnProperty('dupList')) {
                            pageEvents.trigger('status:change:dupList', responseObj.response.data.dupList);
                        } else {
                            pageEvents.trigger('status:change:error', responseObj.response.errors);
                        }
                    }
                }
            });
        },
        remove: function(event) {
            if ($(event.currentTarget).attr('data-prompt')) {
                this.trigger('delete:collectible:prompt');
            } else {
                this.collectible.destroy({
                    wait: true,
                    error: function(model, response) {

                        var responseObj = $.parseJSON(response.responseText);

                        pageEvents.trigger('status:change:error', responseObj.response.errors);

                    }
                });
            }
        }
    });

    return StatusView;
}));