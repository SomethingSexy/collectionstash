define(['marionette', 'text!templates/app/common/activities.mustache', 'views/common/view.activity', 'text!templates/app/common/activities.empty.mustache', 'mustache', 'marionette.mustache'], function(Marionette, template, ActivityView, emptyTemplate) {

    var NoItemsView = Backbone.Marionette.ItemView.extend({
        template: emptyTemplate
    });

    return Marionette.CompositeView.extend({
        template: template,
        itemView: ActivityView,
        itemViewContainer: "._activities",
        emptyView: NoItemsView,
        _initialEvents: function() {
            if (this.collection) {
                this.listenTo(this.collection, "sync", this.renderMore)
            }
        },
        events: {
            'click ._next': 'next'
        },
        initialize: function(options) {
            this.showMore = (typeof options.showMore === 'undefined') ? false : options.showMore;
        },
        itemEvents: {
            "filter:selected": function(event, view, type, values) {
                this.trigger('filter:selected', type, values);
            }
        },
        serializeData: function() {
            return {
                showMore: this.showMore
            };
        },
        next: function() {
            this.collection.getNextPage();
        },
        renderMore: function() {
            var self = this;
            var ItemView;
            if (this.collection.state.currentPage === 1) {
                $(this.itemViewContainer, this.el).empty();
            }
            this.startBuffering();
            this.collection.each(function(item, index) {
                ItemView = this.getItemView(item);
                this.addItemView(item, ItemView, index);
            }, this);
            this.endBuffering();
            if (!this.collection.hasNextPage()) {
                $('._next', this.el).hide();
            } else {
                $('._next', this.el).show();
            }
        }

    });
});