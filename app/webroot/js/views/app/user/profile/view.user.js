define(['require', 'marionette', 'underscore', 'text!templates/app/user/profile/user.mustache', 'mustache', 'marionette.mustache'], function(require, Marionette, _, template, mustache) {

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

        }
    });
});