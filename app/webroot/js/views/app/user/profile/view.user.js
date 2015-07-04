define(function(require) {
    var Marionette = require('marionette'),
        Backbone = require('backbone'),
        _ = require('underscore'),
        template = require('text!templates/app/user/profile/user.mustache'),
        blockies = require('blockies');
    require('mustache');
    require('marionette.mustache');
    return Marionette.ItemView.extend({
        template: template,
        events: {
            'click ._favorite': 'favorite'
        },
        initialize: function(options) {
            this.facts = options.facts;
            this.permissions = options.permissions;
        },
        serializeData: function() {
            var data = _.extend(this.model.toJSON(), this.facts.toJSON(), {
                permissions: this.permissions.toJSON()
            });
            return data;
        },
        onRender: function() {
            var icon = blockies.create({ // All options are optional
                seed: this.model.get('username'), // seed used to generate icon data, default: random
                // color: '#dfe', // to manually specify the icon color, default: random
                size: 10, // width/height of the icon in blocks, default: 10
                scale: 15 // width/height of each block in pixels, default: 5
            });
            this.$('.blockie').html(icon);
        },
        favorite: function(event) {
            event.preventDefault();
            Backbone.ajax({
                type: "post",
                data: {
                    'data[Favorite][type_id]': this.model.get('id'),
                    'data[Favorite][type]': 'user',
                    'data[Favorite][subscribed]': this.model.get('favorited'),
                },
                dataType: 'json',
                url: '/favorites/favorite',
                beforeSend: function(jqXHR, settings) {},
                success: function(data, textStatus, XMLHttpRequest) {
                    // eh don't do anything
                }
            });
        }
    });
});