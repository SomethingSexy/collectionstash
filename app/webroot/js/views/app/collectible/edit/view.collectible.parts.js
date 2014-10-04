define(['marionette', 'text!templates/app/collectible/edit/collectible.parts.mustache', 'views/app/collectible/edit/view.collectible.part', 'mustache', 'underscore', 'marionette.mustache', 'bootstrap'], function(Marionette, template, PartView, mustache, _) {
    return Marionette.CompositeView.extend({
        template: template,
        className: "",
        itemViewContainer: "._parts-list",
        // emptyView: NoItemsView,
        itemView: PartView,
        itemEvents: {
            'edit:collectible:part': function(event, view, model) {
                this.trigger('edit:collectible:part', model);
            },
            'edit:part': function(event, view, model) {
                this.trigger('edit:part', model);
            },
            'edit:part:photo': function(event, view, model) {
                this.trigger('edit:part:photo', model);
            },
            'remove:part': function(event, view, model) {
                this.trigger('remove:part', model);
            },
            'remove:part:duplicate': function(event, view, model) {
                this.trigger('remove:part:duplicate', model);
            }
        },
        itemViewOptions: function(model, index) {
            return {
                status: this.status,
                artists: this.artists,
                manufacturers: this.manufacturers,
                categories: this.categories,
                collectible: this.model,
                scales: this.scales
            };
        },
        initialize: function(options) {
            this.status = options.status;
            this.artists = options.artists;
            this.manufacturers = options.manufacturers;
            this.categories = options.categories;
            this.collectible = options.collectible;
            this.scales = options.scales;
        },
        onRender: function() {}
    });
});