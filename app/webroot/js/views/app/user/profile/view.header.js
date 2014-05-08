define(['require', 'marionette', 'text!templates/app/user/profile/header.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        events: {
            'click ._stash': 'stash',
            'click ._wishlist': 'wishlist',
            'click ._sale': 'sale',
            'click ._comments': 'comments',
            'click ._photos': 'photos',
            'click ._history': 'history'
        },
        onRender: function() {

        },
        stash: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'stash');
        },
        wishlist: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'wishlist');
        },
        sale: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'sale');
        },
        comments: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'comments');
        },
        photos: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'photos');
        },
        history: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'history');
        }
    });
});