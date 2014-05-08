define(['require', 'marionette', 'text!templates/app/user/profile/stash.mustache', 'views/app/user/profile/view.stash.collectible', 'mustache', 'imagesloaded', 'wookmark',
    'marionette.mustache'
], function(require, Marionette, template, CollectibleView, mustache, Masonry) {

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CollectibleView,
        itemViewContainer: "._tiles",
        onRender: function() {
            var handler = $('._tiles .tile', this.el);
            $('._tiles', this.el).imagesLoaded(function() {
                // Call the layout function.
                handler.wookmark({
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: $('._tiles', this.el)
                });

                // Capture clicks on grid items.
                handler.click(function() {
                    // Randomize the height of the clicked item.
                    var newHeight = $('img', this).height() + Math.round(Math.random() * 300 + 30);
                    $(this).css('height', newHeight + 'px');

                    // Update the layout.
                    handler.wookmark();
                });
            });
        }
    });
});