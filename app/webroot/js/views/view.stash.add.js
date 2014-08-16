var StashAddView = Backbone.View.extend({
    template: 'stash.add',
    events: {
        "change input[name]": "fieldChanged",
        "change select[name]": "selectionChanged",
        'change textarea[name]': 'fieldChanged',
    },
    initialize: function(options) {
        this.collectible = options.collectible;
        // this.merchantHound = new Bloodhound({
        //     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        //     queryTokenizer: Bloodhound.tokenizers.whitespace,
        //     remote: {
        //         url: '/merchants/getMerchantList?query=%QUERY',
        //         filter: function(list) {
        //             return $.map(list, function(country) {
        //                 return {
        //                     value: country
        //                 };
        //             });
        //         }
        //     }
        // });
        // this.merchantHound.initialize();
        this.lastResults = [];
    },
    render: function() {
        var self = this;
        var data = this.collectible.toJSON();
        data.model = this.model.toJSON();
        dust.render(this.template, data, function(error, output) {
            $(self.el).html(output);
        });
        $("#CollectiblesUserPurchaseDate", this.el).datepicker().on('changeDate', function(e) {
            var data = {
                purchase_date: (e.date.getMonth() + 1) + '/' + e.date.getDay() + '/' + e.date.getFullYear()
            };
            self.model.set(data, {
                forceUpdate: true
            });
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
                    self.lastResults = data;
                    return {
                        results: data
                    };
                }
            },
            formatResult: function(item) {
                return item.name;
            },
            formatSelection: function(item) {
                return item.name;
            },
            createSearchChoice: function(term, data) {
                if (self.lastResults.some(function(r) {
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
            var data = $('.merchants-typeahead', self.el).select2('data');
            if (!data || !data.name) {
                self.model.unset('merchant', {
                    forceUpdate: true
                });
                return;
            }
            if (data.created) {
                self.model.set({
                    merchant: data.name,
                }, {
                    forceUpdate: true
                });
            } else {
                self.model.set({
                    merchant: data.name,
                }, {
                    forceUpdate: true
                });
            }
        });
        // $('.merchants .typeahead', this.el).typeahead({
        //     hint: true,
        //     highlight: true,
        //     minLength: 1
        // }, {
        //     name: 'merchants',
        //     displayKey: 'value',
        //     source: this.merchantHound.ttAdapter()
        // });
        return this;
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
    removeErrors: function() {
        $('input[data-error=true]', this.el).removeClass('invalid').closest('.form-group').removeClass('has-error').children('._error').empty();
        $('._globalError', this.el).empty();
    },
    onError: function() {
        $('.btn-primary', this.el).button('reset');
        this.removeErrors();
        var self = this;
        _.each(this.errors, function(error, attr) {
            $('[name="' + attr + '"]', self.el).addClass('invalid').attr('data-error', true);
            $('[name="' + attr + '"]', self.el).closest('.form-group').addClass('has-error');
            $('[name="' + attr + '"]', self.el).parent().find('._error').remove();
            var errorHtml = '';
            if (_.isArray(error)) {
                if (error.length === 1) {
                    errorHtml = error[0];
                } else {
                    _.each(error, function(message) {
                        errorHtml += '<p>' + message + '</p>';
                    });
                }
            } else {
                errorHtml = error;
            }

            $('[name="' + attr + '"]', self.el).after('<span class="help-block _error">' + errorHtml + '</span>');
        });
    }
});