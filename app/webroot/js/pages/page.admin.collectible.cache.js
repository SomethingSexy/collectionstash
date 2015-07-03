var CacheView = Backbone.View.extend({
    template: 'admin.collectible.cache',
    events: {
        'click .clearOne': 'clearOne',
        'click .clearAll': 'clearAll'
    },
    initialize: function(options) {
        var self = this;
        this.errors = [];
        this.listenTo(this.model, 'sync', function() {
            self.errors = [];
            self.render();
        });
    },
    render: function() {
        var self = this;
        var data = {};
        data.errors = this.errors;
        data.inlineErrors = {};
        _.each(this.errors, function(error) {
            if (error.inline) {
                data.inlineErrors[error.name] = error.message;
            }
        });
        dust.render(this.template, data, function(error, output) {
            $(self.el).html(output);
        });
        return this;
    },
    clearOne: function(event) {
        event.preventDefault();
        if ($('#collectibleId', this.el).val() === '') {
            this.errors.push({
                inline: true,
                name: 'collectible_id',
                message: 'Collectible Id is required'
            });
            this.render();
            return;
        }
        this.model.set('clearAll', false);
        this.model.set('collectible_id', $('#collectibleId', this.el).val());
        this.model.save({});
    },
    clearAll: function(event) {
        event.preventDefault();
        this.model.set('clearAll', true);
        this.model.save({});
    }
});
var CacheModel = Backbone.Model.extend({
    url: '/collectibles/cache'
});
$(function() {

    // Get all of the data here
    $.when($.get('/templates/collectibles/admin.cache.dust'), $.get('/templates/common/alert.dust')).done(function(adminCacheTemplate, alertTemplate) {
        dust.loadSource(dust.compile(adminCacheTemplate[0], 'admin.collectible.cache'));
        dust.loadSource(dust.compile(alertTemplate[0], 'alert'));
        var cacheModel = new CacheModel();
        var cacheView = new CacheView({
            model: cacheModel
        });
        cacheModel.on('error', function(model, response, options) {
            $('#message-container').html(new AlertView({
                error: true,
                status: response.status,
                responseText: response.responseText
            }).render().el);
        });
        cacheModel.on('sync', function(model, response, options) {
            $('#message-container').html(new AlertView({
                error: false,
                status: response.status,
                responseText: response.responseText
            }).render().el);
        });
        $('#admin-container').html(cacheView.render().el);
    });
});