define(['marionette', 'text!templates/app/collectible/edit/person.add.mustache', 'mustache', 'underscore', 'views/common/growl', 'marionette.mustache', 'select2'], function(Marionette, template, mustache, _, growl) {
    var lastResults = [];
    var AddPersonView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click .add-artist': 'addArtist',
            'keypress #inputArtist': 'inputChange'
        },
        initialize: function(options) {},
        onRender: function() {
            var self = this;
            $('.artists-typeahead', this.el).select2({
                placeholder: 'Search or add a new person.',
                minimumInputLength: 1,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "/artists/persons",
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
            });
        },
        inputChange: function() {
            $('.inline-error', this.el).text('');
            $('.input-group', this.el).removeClass('has-error');
        },
        addArtist: function() {
            var self = this,
                valObj = $('.artists-typeahead', this.el).select2('data');
            var name = valObj && valObj.name ? valObj.name : '';
            name = $.trim(name);
            $('.inline-error', self.el).text('');
            $('.input-group', self.el).removeClass('has-error');
            if (name !== '') {
                this.collection.create({
                    'collectible_id': collectibleId,
                    Artist: {
                        name: name
                    }
                }, {
                    wait: true,
                    success: function(model, response) {
                        var message = "The person has been successfully added!";
                        if (response.response.data) {
                            if (response.response.data.hasOwnProperty('isEdit')) {
                                if (response.response.data.isEdit) {
                                    message = "Your edit has been successfully submitted!";
                                }
                            }
                        }
                        growl.onSuccess(message);
                    },
                    error: function(model, response) {
                        var responseObj = $.parseJSON(response.responseText);
                        if (responseObj.response && responseObj.response.errors) {
                            $('.input-grou', self.el).addClass('has-error');
                            $('.inline-error', self.el).text(responseObj.response.errors[0].message[0]);
                        }
                    }
                });
                $('#inputArtist', self.el).val('');
            }
        }
    });
    return AddPersonView;
});