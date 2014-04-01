var StashAddView = Backbone.View.extend({
    template: 'stash.add',
    events: {
        "change input": "fieldChanged",
        "change select": "selectionChanged",
        'change textarea': 'fieldChanged',
    },
    initialize: function(options) {
        this.collectible = options.collectible;
        this.merchantHound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/merchants/getMerchantList?query=%QUERY',
                filter: function(list) {
                    return $.map(list, function(country) {
                        return {
                            value: country
                        };
                    });
                }
            }
        });
        this.merchantHound.initialize();
    },
    render: function() {
        var self = this;
        var data = this.collectible.toJSON();
        data.model = this.model.toJSON();
        data.errors = this.errors;
        data.inlineErrors = {};
        _.each(this.errors, function(error) {
            if (error.inline) {
                data.inlineErrors[error.name] = error.message;
            }
        });
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
        $('.merchants .typeahead', this.el).typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'merchants',
            displayKey: 'value',
            source: this.merchantHound.ttAdapter()
        });
        this.errors = [];
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
    }
});