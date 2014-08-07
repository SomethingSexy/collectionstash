define(['marionette', 'text!templates/app/collectible/edit/person.add.mustache', 'mustache', 'underscore', 'marionette.mustache', 'select2'], function(Marionette, template, mustache, _) {
    var AddPersonView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click .add-artist': 'addArtist',
            'keypress #inputArtist': 'inputChange'
        },
        initialize: function(options) {

        },
        onRender: function() {
            var self = this;
            $('.artists-typeahead', this.el).select2({
                placeholder: 'Search for a person',
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
                    results: function(data, page) { // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to alter remote JSON data
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
                dropdownCssClass: "bigdrop"
            });

            return this;
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
                        var message = "The artist has been successfully added!";
                        if (response.response.data) {
                            if (response.response.data.hasOwnProperty('isEdit')) {
                                if (response.response.data.isEdit) {
                                    message = "Your edit has been successfully submitted!";
                                }
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