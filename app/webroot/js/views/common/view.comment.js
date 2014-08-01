define(['require', 'marionette', 'text!templates/app/common/comment.mustache', 'mustache', 'blockies', 'marionette.mustache'], function(require, Marionette, template, mustache, blockies) {

    return Marionette.ItemView.extend({
        className: '',
        tagName: 'li',
        template: template,
        initialize: function(options) {
            this.permissions = options.permissions;
            this.listenTo(this.model, 'change', this.render);
        },
        events: {
            'click ._edit': 'edit',
            'click ._remove': 'removeComment'
        },
        edit: function(event) {
            event.preventDefault();
            this.trigger('comment:edit', this.model.get('id'));
        },
        removeComment: function(event) {
            event.preventDefault();
            this.model.destroy();
        },
        onRender: function() {
            var icon = blockies.create({ // All options are optional
                seed: this.model.get('User').username, // seed used to generate icon data, default: random
                // color: '#dfe', // to manually specify the icon color, default: random
                size: 10, // width/height of the icon in blocks, default: 10
                scale: 5 // width/height of each block in pixels, default: 5
            });

            $('.blockie', this.el).html(icon);
        }
    });
});