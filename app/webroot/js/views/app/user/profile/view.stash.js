define(['require', 'marionette', 'text!templates/app/user/profile/stash.mustache', 'views/app/user/profile/view.stash.collectible', 'mustache', 'marionette.mustache'], function(require, Marionette, template, CollectibleView, mustache) {

    return Marionette.CompositeView.extend({
        template: template,
        itemView: CollectibleView,
        itemViewContainer: "._tiles",
    });
});