define(['require', 'marionette', 'underscore', 'text!templates/app/user/profile/user.mustache', 'mustache', 'blockies', 'marionette.mustache'], function(require, Marionette, _, template, mustache, blockies) {

    return Marionette.ItemView.extend({
        template: template,
        initialize: function(options) {
            this.facts = options.facts;
        },
        serializeData: function() {
            var data = _.extend(this.model.toJSON(), this.facts.toJSON());
            return data;
        },
        onRender: function() {
            var icon = blockies.create({ // All options are optional
                seed: this.model.get('username'), // seed used to generate icon data, default: random
                // color: '#dfe', // to manually specify the icon color, default: random
                size: 10, // width/height of the icon in blocks, default: 10
                scale: 15 // width/height of each block in pixels, default: 5
            });

            $('.blockie', this.el).html(icon);
        }
    });
});