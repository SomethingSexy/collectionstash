define(['backbone', 'text!templates/collectibles/collectible.delete.dust', 'dust'], function(Backbone, collectibleDeleteTemplate, dust) {
    dust.loadSource(dust.compile(collectibleDeleteTemplate, 'collectible.delete'));
    var CollectibleDeleteView = Backbone.View.extend({
        template: 'collectible.delete',
        className: "col-md-12",
        events: {
            'click .save': 'remove'
        },
        initialize: function(options) {
            this.variants = options.variants;
            this.on('ok', this.remove, this);
            this.alertView = null;
            this.errors = [];
        },
        render: function() {
            var self = this;

            var data = {
                wishListCount: this.model.get('collectibles_wish_list_count'),
                stashCount: this.model.get('collectibles_user_count'),
                variantCount: this.variants.size()
            };

            dust.render(this.template, data, function(error, output) {
                $(self.el).html(output);
            });

            if (this.errors.length > 0) {
                this.alertView = new AlertView({
                    dismiss: false,
                    messages: this.errors,
                    error: true
                });

                $('.well', this.el).before(this.alertView.render().el);
            }

            return this;
        },
        remove: function() {
            var self = this;
            var url = this.model.url();
            this.errors = [];

            if ($('#inputReplaceId', this.el).val() !== '') {
                url = url + '/' + $('#inputReplaceId', this.el).val();
            }

            this.model.destroy({
                url: url,
                wait: true,
                error: function(model, response) {
                    var responseObj = $.parseJSON(response.responseText);

                    self.errors = new Backbone.Collection(responseObj.response.errors);
                    self.render();

                    // pageEvents.trigger('status:change:error', responseObj.response.errors);
                }
            });
        }
    });

    return CollectibleDeleteView;
});