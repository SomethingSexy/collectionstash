(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['backbone', 'dust', 'text!templates/collectibles/status.dust', 'dust-helpers'], factory);
    } else {
        // Browser globals
        root.StatusView = factory(root.Backbone, root.dust);
    }
}(this, function(backbone, dust, template) {
    // only do this for the AMD version
    if (template && dust) {
        dust.loadSource(dust.compile(template, 'status.edit'));
    }

    var StatusView = Backbone.View.extend({
        template: 'status.edit',
        className: "col-md-12",
        events: {
            'click .submit': 'changeStatus',
            'click .delete': 'remove'
        },
        initialize: function(options) {
            options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
            this.collectible = options.collectible ? options.collectible : {};
            this.allowDelete = (options.allowDelete && options.allowDelete === true) ? true : false;
            //this.model.on("change", this.render, this);
        },
        render: function() {
            var self = this;

            var model = this.model.toJSON();
            model.allowEdit = this.allowEdit;
            model.allowDelete = this.allowDelete;
            if (this.collectible) {
                model.collectible = this.collectible.toJSON();
            }
            dust.render(this.template, model, function(error, output) {
                $(self.el).html(output);
            });
            return this;
        },
        changeStatus: function(event) {
            $(event.currentTarget).button('loading');
            this.model.save({}, {
                error: function(model, response) {
                    $(event.currentTarget).button('reset');

                    if (response.status === 500) {
                        pageEvents.trigger('status:change:error:severe');
                    } else {

                        var responseObj = $.parseJSON(response.responseText);
                        if (responseObj.response.data.hasOwnProperty('dupList')) {
                            pageEvents.trigger('status:change:dupList', responseObj.response.data.dupList);
                        } else {
                            pageEvents.trigger('status:change:error', responseObj.response.errors);
                        }
                    }
                }
            });
        },
        remove: function(event) {
            if ($(event.currentTarget).attr('data-prompt')) {
                this.trigger('delete:collectible:prompt');
            } else {
                this.collectible.destroy({
                    wait: true,
                    error: function(model, response) {

                        var responseObj = $.parseJSON(response.responseText);

                        pageEvents.trigger('status:change:error', responseObj.response.errors);

                    }
                });
            }
        }
    });

    return StatusView;
}));