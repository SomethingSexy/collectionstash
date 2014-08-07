define(['marionette', 'text!templates/app/collectible/edit/tag.add.mustache', 'mustache', 'underscore', 'marionette.mustache', 'select2'], function(Marionette, template, mustache, _) {
    var AddTagView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click .add-tag': 'addTag',
            'keypress #inputTag': 'inputChange'
        },
        onRender: function() {
            var self = this;
            $('.tags-typeahead', this.el).select2({
                placeholder: 'Search for a person',
                minimumInputLength: 1,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "/tags/tags",
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
                    return item.tag;
                },
                formatSelection: function(item) {
                    return item.tag;
                },
                dropdownCssClass: "bigdrop"
            });
            return this;
        },
        inputChange: function() {
            $('.inline-error', this.el).text('');
            $('.input-group', this.el).removeClass('has-error');
        },
        addTag: function() {
            var self = this,
                valObj = $('.tags-typeahead', this.el).select2('data');
            var tag = valObj && valObj.tag ? valObj.tag : '';
            tag = $.trim(tag);
            $('.inline-error', self.el).text('');
            $('.input-group', self.el).removeClass('has-error');
            if (tag !== '') {
                this.collection.create({
                    'collectible_id': collectibleId,
                    Tag: {
                        tag: tag
                    }
                }, {
                    wait: true,
                    success: function(model, response) {
                        var message = "The tag has been successfully added!";
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
                            $('.input-group', self.el).addClass('has-error');
                            $('.inline-error', self.el).text(responseObj.response.errors[0].message[0]);
                        }
                    }
                });
                $('#inputTag', self.el).val('');
            }
        }
    });

    return AddTagView;
});