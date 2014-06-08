define(['require', 'marionette', 'views/app/user/profile/view.stash', 'text!templates/app/user/profile/stash.table.mustache', 'views/app/user/profile/view.stash.collectible.row', 'mustache', 'marionette.mustache'], function(require, Marionette, StashView, template, CollectibleView, mustache) {

    return StashView.extend({
        className: 'table-responsive',
        template: template,
        itemViewContainer: "tbody",
        itemView: CollectibleView
    });
});