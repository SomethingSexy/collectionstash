define(['backbone', 'dust', 'text!templates/collectibles/manufacturer.series.add.dust'], function(Backbone, dust, template) {

    dust.loadSource(dust.compile(template, 'manufacturer.series.add'));

    var ManufacturerSeriesView = Backbone.View.extend({
        template: 'manufacturer.series.add',
        modal: 'modal',
        events: {
            'click .add-series': 'showAdd',
            'click .add.submit': 'addSeries'
        },
        initialize: function(options) {
            var self = this;
            Backbone.Validation.bind(this, {
                valid: function(view, attr, selector) {
                    view.$('[' + selector + '~="' + attr + '"]').removeClass('invalid').removeAttr('data-error');
                    view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                    view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').removeClass('has-error');
                    // do something
                },
                invalid: function(view, attr, error, selector) {
                    view.$('[' + selector + '~="' + attr + '"]').addClass('invalid').attr('data-error', error);
                    view.$('[' + selector + '~="' + attr + '"]').closest('.form-group').addClass('has-error');
                    view.$('[' + selector + '~="' + attr + '"]').parent().find('._error').remove();
                    view.$('[' + selector + '~="' + attr + '"]').after('<span class="help-block _error">' + error + '</span>');
                    // do something
                }
            });
            this.manufacturer = options.manufacturer;
        },
        remove: function() {
            //this.model.off('change');
            Backbone.View.prototype.remove.call(this);
        },
        renderBody: function() {
            var self = this;
            var data = {
                manufacturer: this.manufacturer.toJSON()
            };
            dust.render(this.template, data, function(error, output) {
                $('.modal-body', self.el).html(output);
            });
            $('.modal-body', self.el).append(this.model.toJSON().response.data);
        },
        render: function() {
            var self = this;
            dust.render(this.modal, {
                modalId: 'manufacturerSeriesModal',
                modalTitle: 'Manufacturer Categories'
            }, function(error, output) {
                $(self.el).html(output);
            });
            $(self.el).find('.btn-primary.save').remove();
            this.renderBody();
            return this;
        },
        showAdd: function(event) {
            this.hideMessage();
            var $target = $(event.currentTarget);
            var $inputWrapper = $('<div></div>').addClass('item').addClass('input');
            var $input = $('<input />').attr('type', 'input').attr('maxlength', '100');
            var $submit = $('<button></button>').text('Submit').addClass('add').addClass('submit');
            var $cancel = $('<button></button>').text('Cancel').addClass('add').addClass('cancel');
            $inputWrapper.append($input);
            $inputWrapper.append($submit);
            $inputWrapper.append($cancel);
            $target.parent('span.actions').after($inputWrapper);
        },
        closeAdd: function(event) {
            var $target = $(event.currentTarget);
            $target.parent('div.input').remove();
        },
        addSeries: function(event) {
            var self = this;
            var seriesId = $(event.currentTarget).parent('div.input').parent('li').children('span.name').attr('data-id');
            var name = $(event.currentTarget).parent('div.input').children('input').val();
            $.ajax({
                url: '/series/add.json',
                dataType: 'json',
                data: 'data[Series][parent_id]=' + seriesId + '&data[Series][name]=' + name,
                type: 'post',
                beforeSend: function(xhr) {},
                error: function(jqXHR, textStatus, errorThrown) {
                    var $messageContainer = $('.message-container', self.el);
                    $('h4', $messageContainer).text('');
                    $('ul', $messageContainer).empty();
                    if (jqXHR.status === 401) {
                        $('h4', $messageContainer).text('You must be logged in to do that!');
                    } else if (jqXHR.status === 400) {
                        var response = JSON.parse(jqXHR.responseText);
                        $('h4', $messageContainer).text('Oops! Something wasn\'t filled out correctly.');
                        if (response && response.response && response.response.errors) {
                            _.each(response.response.errors, function(error) {
                                _.each(error.message, function(message) {
                                    $('ul', $messageContainer).append($('<li></li>').text(message));
                                });
                            });
                        }
                    } else {
                        $('h4', $messageContainer).text('Something really bad happened.');
                    }
                    $messageContainer.show();
                },
                success: function(data) {
                    self.hideMessage();
                    if (data.response.isSuccess) {
                        //TODO: Once this part is more backboney then we can just add
                        // render
                        // let's try and add it to the current list
                        var $parentLi = $(event.currentTarget).parent('div.input').parent('li');
                        var $ul = $('ul', $parentLi);
                        if ($ul.length === 0) {
                            $parentLi.append($('<ul></ul>'));
                            $ul = $('ul', $parentLi);
                        }
                        var $series = $('<li></li>');
                        $series.append('<span class="item name" data-id=" ' + data.response.data.id + '" data-path="' + data.response.data.name + '">' + data.response.data.name + '</span>');
                        $series.append('<span class="item actions"> <a class="action add-series"> Add</a></span>');
                        $ul.append($series);
                        self.closeAdd(event);
                        // first check to see if
                    } else {
                        //data.errors[0][name];
                    }
                }
            });
        },
        hideMessage: function() {
            $('.message-container', this.el).hide();
        }
    });

    return ManufacturerSeriesView;
});