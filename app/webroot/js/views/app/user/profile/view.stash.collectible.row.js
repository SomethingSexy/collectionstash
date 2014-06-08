define(['require', 'marionette', 'views/app/user/profile/view.stash.collectible', 'text!templates/app/user/profile/stash.collectible.row.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, CollectibleStashView, template, mustache) {

    return CollectibleStashView.extend({
        className: 'stash-item',
        tagName : 'tr',
        template: template
    });
});