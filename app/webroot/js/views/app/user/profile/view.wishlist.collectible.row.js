define(['require', 'marionette', 'views/app/user/profile/view.wishlist.collectible', 'text!templates/app/user/profile/wishlist.collectible.row.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, CollectibleStashView, template, mustache) {

    return CollectibleStashView.extend({
        className: 'stash-item',
        tagName : 'tr',
        template: template
    });
});