define(['marionette', 'text!templates/app/collectible/edit/tag.add.mustache', 'mustache', 'underscore', 'views/common/growl', 'marionette.mustache', 'select2'], function(Marionette, template, mustache, _, growl) {
    var lastResults = [];
    var AddTagView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click .add-tag': 'addTag',
            'keypress #inputTag': 'inputChange'
        },
        onRender: function() {
            var self = this;
            $('input.tags-typeahead', this.el).select2({
                placeholder: 'Search or add a new tag.',
                minimumInputLength: 1,
                ajax: {
                    url: "/tags/tags",
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
                    return item.tag;
                },
                formatSelection: function(item) {
                    return item.tag;
                },
                createSearchChoice: function(term, data) {
                    if (lastResults.some(function(r) {
                            return r.tag == term
                        })) {
                        return {
                            id: data.id,
                            tag: term
                        };
                    } else {
                        return {
                            id: term,
                            tag: term
                        };
                    }
                },
                allowClear: true,
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
                        growl.onSuccess(message);
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