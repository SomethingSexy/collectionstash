define(['require', 'marionette', 'bootstrap'], function(Marionette) {
    return Backbone.Marionette.Region.extend({
        el: "#modal",

        constructor: function() {
            _.bindAll(this, "getEl", "showModal", "hideModal");
            Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
            this.on("show", this.showModal, this);
        },

        getEl: function(selector) {
            var $el = $(selector);
            $el.on("hidden", this.close);
            return $('.modal-dialog', $el);
        },

        showModal: function(view) {
            view.on("close", this.hideModal, this);
            $(this.el).find('.modal').modal();
        },

        hideModal: function() {
            this.$el.modal('hide');
        }
    });
});