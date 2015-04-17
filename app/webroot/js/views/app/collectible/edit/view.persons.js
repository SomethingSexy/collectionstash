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
        },
        serializeData: function() {
            var data = {
                total: this.collection.length,
                collectibleType: this.collectibleType.toJSON()
            };

            if (this.model.parsedCollectible) {
                data.parsedCollectible = this.model.parsedCollectible.toJSON();
                data.parsedCollectible.missingArtists = _.difference(this.model.parsedCollectible.get('artists'), this.collection.map(function(artist) {
                    return artist.get('Artist').name;
                }));
            }

            return data;
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