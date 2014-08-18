$(function() {
    var lastResults = [];
    $('#dialogCost').blur(function() {
        $('#dialogCost').formatCurrency();
    });
    $("#CollectiblesUserPurchaseDate").datepicker();
    $("#CollectiblesUserRemoveDate").datepicker();

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
});