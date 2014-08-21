define(['marionette', 'text!templates/app/collectible/edit/persons.mustache', 'views/app/collectible/edit/view.person', 'views/app/collectible/edit/view.person.add', 'mustache', 'underscore', 'marionette.mustache'], function(Marionette, template, PersonView, AddPersonView, mustache, _) {

    return Marionette.CompositeView.extend({
        template: template,
        className: "row",
        itemViewContainer: ".artists",
        // emptyView: NoItemsView,
        itemView: PersonView,
        events: {
            'click .save': 'save',
        },
        initialize: function(options) {
            this.collectibleType = options.collectibleType;
            // this.collection.on('add', this.render, this);
            // this.collection.on('remove', this.render, this);
        },
        serializeData: function() {
            return {
                total: this.collection.length,
                collectibleType: this.collectibleType.toJSON()
            };
        },
        onRender: function() {
            var self = this;
            if (this.addArtistView) {
                this.addArtistView.remove();
            }
            this.addArtistView = new AddPersonView({
                collection: this.collection
            });
            $('.add-container', self.el).html(this.addArtistView.render().el);
            return this;
        },
        save: function() {
            this.collection.sync();
        }
    });
});