define(['backbone', 'select2'], function(Backbone) {
    var AddPersonView = Backbone.View.extend({
        template: 'artist.add',
        events: {
            'click .add-artist': 'addArtist',
            'keypress #inputArtist': 'inputChange'
        },
        initialize: function(options) {
            // this.artistsHound = new Bloodhound({
            //     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            //     queryTokenizer: Bloodhound.tokenizers.whitespace,
            //     remote: {
            //         url: '/artists/getArtistList?query=%QUERY',
            //         filter: function(list) {
            //             return $.map(list, function(artist) {
            //                 return {
            //                     value: artist
            //                 };
            //             });
            //         }
            //     }
            // });
            // this.artistsHound.initialize();
        },
        render: function() {
            var self = this;
            dust.render(this.template, {}, function(error, output) {
                $(self.el).html(output);
            });
            // $('.artists-typeahead .typeahead', this.el).typeahead({
            //     hint: true,
            //     highlight: true,
            //     minLength: 1
            // }, {
            //     name: 'artists',
            //     displayKey: 'value',
            //     source: this.artistsHound.ttAdapter()
            // });
            return this;
        },
        inputChange: function() {
            $('.inline-error', this.el).text('');
            $('.input-group', this.el).removeClass('has-error');
        },
        addArtist: function() {
            var self = this;
            var name = $('#inputArtist', self.el).val();
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