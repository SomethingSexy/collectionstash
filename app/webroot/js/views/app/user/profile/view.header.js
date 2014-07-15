define(['require', 'marionette', 'text!templates/app/user/profile/header.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, template, mustache) {

    return Marionette.ItemView.extend({
        template: template,
        events: {
            'click ._profile': 'profile',
            'click ._stash': 'stash',
            'click ._wishlist': 'wishlist',
            'click ._sale': 'sale',
            'click ._photos': 'photos',
            'click ._history': 'history',
            'click ._activity': 'activity'
        },
        initialize: function(options) {
            this.selectedMenu = options.selectedMenu || 'profile';
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.selectedMenu = this.selectedMenu;
            return data;
        },
        onRender: function() {
            if(this.selectedMenu){
                $('a._' + this.selectedMenu, this.el).parent().addClass('active');
            }
        },
        profile: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'profile');
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
        photos: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'photos');
        },
        history: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'history');
        },
        activity: function(event) {
            event.preventDefault();
            this.trigger('navigate:menu', 'activity');
        }
    });
});