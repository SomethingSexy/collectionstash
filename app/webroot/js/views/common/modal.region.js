define(['require', 'marionette', 'bootstrap'], function(Marionette) {
    return Backbone.Marionette.Region.extend({
        el: "#modal",

        constructor: function() {
            var self = this;
            _.bindAll(this, "getEl", "showModal", "hideModal");
            Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
            this.on("show", this.showModal, this);
            $(this.el).on("hide.bs.modal", '.modal', function() {
                self.close();
            });
        },
        getEl: function(selector) {
            var $el = $(selector);
            return $('.modal-dialog', $el);
        },
        showModal: function(view) {
            //view.on("close", this.hideModal, this);
            $(this.el).find('.modal').modal();
        },
        hideModal: function() {
            $(this.el).find('.modal').modal('hide');
        }
    });
});