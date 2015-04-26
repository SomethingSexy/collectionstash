define(function(require) {
    var Backbone = require('backbone'),
        Marionette = require('marionette'),
        $ = require('jquery'),
        Mustache = require('mustache'),
        template = require('text!templates/app/collectible/edit/collectibletype.mustache'),
        ErrorMixin = require('views/common/mixin.error'),
        growl = require('views/common/growl');
    require('marionette.mustache');

    var CollectibletypeView = Marionette.ItemView.extend({
        template: template,
        events: {
            'click .item.name': 'selectType'
        },
        initialize: function(options) {
            var self = this;
            this.collectiblTypeHtml = options.collectiblTypeHtml;
        },
        onRender: function() {
            this.$('.modal-body').html(this.collectiblTypeHtml);
        },
        selectType: function(event) {
            var $item = $(event.currentTarget),
                id = $item.data('id'),
                label = $item.text().trim();
            this.trigger('select:type', id, label);
        }
    });

    _.extend(CollectibletypeView.prototype, ErrorMixin);

    return CollectibletypeView;
});