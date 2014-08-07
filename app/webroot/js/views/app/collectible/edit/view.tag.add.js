define(['backbone', 'select2'], function(Backbone) {
    var AddTagView = Backbone.View.extend({
        template: 'tag.add',
        events: {
            'click .add-tag': 'addTag',
            'keypress #inputTag': 'inputChange'
        },
        initialize: function(options) {
            // this.tagsHound = new Bloodhound({
            //     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            //     queryTokenizer: Bloodhound.tokenizers.whitespace,
            //     remote: {
            //         url: '/tags/getTagList?query=%QUERY',
            //         filter: function(list) {
            //             return $.map(list, function(tag) {
            //                 return {
            //                     value: tag
            //                 };
            //             });
            //         }
            //     }
            // });
            // this.tagsHound.initialize();
        },
        render: function() {
            var self = this;
            dust.render(this.template, {}, function(error, output) {
                $(self.el).html(output);
            });
            // $('.tags-typeahead .typeahead', self.el).typeahead({
            //     hint: true,
            //     highlight: true,
            //     minLength: 1
            // }, {
            //     name: 'tags',
            //     displayKey: 'value',
            //     source: this.tagsHound.ttAdapter()
            // });
            return this;
        },
        inputChange: function() {
            $('.inline-error', this.el).text('');
            $('.input-group', this.el).removeClass('has-error');
        },
        addTag: function() {
            var self = this;
            var tag = $('#inputTag', self.el).val();
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