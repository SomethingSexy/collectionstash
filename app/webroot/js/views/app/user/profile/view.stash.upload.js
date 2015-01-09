define(function(require) {
    var _ = require('underscore'),
        Backbone = require('backbone'),
        Marionnette = require('marionette'),
        template = require('text!templates/app/user/profile/stash.upload.mustache'),
        ErrorMixin = require('views/common/mixin.error'),
        PhotosCollection = require('collections/collection.photos'),
        PhotoView = require('views/app/user/profile/view.photo');
    require('mustache');
    require('marionette.mustache');

    var StashUploadView = Marionnette.CompositeView.extend({
        template: template,
        itemView: PhotoView,
        itemViewContainer: "._photos",
        events: {
            'click .save': 'save',
            'click .photo .image a': 'selectPhoto',
            'click ._more': 'next',
            'click ._clear': 'clearPhoto'
        },
        itemViewOptions: function(model, index) {
            return {
                permissions: this.permissions,
                model: model,
                gallery: false
            };
        },
        _initialEvents: function() {
            this.listenTo(this.collection, "remove", this.removeItemView);
            this.listenTo(this.collection, "reset", this.renderMore);
        },
        initialize: function(options) {
            var self = this;
            this.model.startTracking();
            this.profile = options.profile;
            this.permissions = options.permissions;
            this.isLoaded = false;

            // create and load this here, should make the view more portable, can always change later to
            // move it to the controller
            this.collection = new PhotosCollection([], {
                username: this.profile.get('username')
            });
            this.collection.getFirstPage({
                silent: true
            }).done(function() {
                self.isLoaded = true;
                self.render();
            });
        },
        onBeforeRender: function() {

        },
        onRender: function() {
            var self = this;
            this.errors = [];
        },
        serializeData: function() {
            var data = this.model.toJSON();
            data.isLoaded = this.isLoaded;
            data.showMore = this.collection.hasNextPage();
            if (this.model.userUpload) {
                data.selectedUserUpload = this.model.userUpload.toJSON();
            }
            return data;
        },
        onClose: function() {
            var self = this;
            _.defer(function() {
                self.model.resetAttributes();
                self.model.stopTracking()
            });
        },
        selectPhoto: function(event) {
            event.preventDefault();
            var $photo = $(event.target).closest('.tile.photo'),
                id = $photo.data('id');
            $('.tile.photo', this.el).css('border', '1px solid #E5E5E5').css('width', '200px');
            $photo.css('border', '5px solid Chartreuse').css('width', '210px');

            this.model.set('user_upload_id', id);
        },
        next: function(event) {
            $('._more', this.el).button('loading');
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

            // once the images are done loading, reset the button
            $('._tiles', this.el).imagesLoaded(function() {
                $('._more', self.el).button('reset');
            });

            if (!this.collection.hasNextPage() || this.collection.state.currentPage >= this.collection.state.lastPage) {
                $('._more', this.el).hide();
            } else {
                $('._more', this.el).show();
            }
        },
        clearPhoto: function(event) {
            var self = this;
            event.preventDefault();
            $('.btn-primary', this.el).button('loading');
            $('._clear', this.el).button('loading');

            this.model.save({
                user_upload_id: null
            }, {
                wait: true,
                success: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                    $('._clear', self.el).button('reset');
                    var userUpload = self.collection.get(model.get('user_upload_id'));
                    if (model.userUpload) {
                        delete model.userUpload;
                    }

                    model.trigger('change:userUpload');
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        },
        // TODO: update this to do what we did for the profile
        // only set the fields when we do the save...taht way if they
        // cancel we won't have to worry about remove values
        save: function(event) {
            var self = this;
            event.preventDefault();
            // pull values from the forum fields
            // call save on the model, this should validate

            $('.btn-primary', this.el).button('loading');

            this.model.save({}, {
                wait: true,
                success: function(model, response, options) {
                    $('.btn-primary', self.el).button('reset');
                    var userUpload = self.collection.get(model.get('user_upload_id'));
                    if (model.userUpload) {
                        model.userUpload.clear({
                            silent: true
                        });
                        model.userUpload.set(userUpload.toJSON());

                    } else {
                        model.userUpload = new Backbone.Model(userUpload.toJSON());
                    }

                    model.trigger('change:userUpload');
                },
                error: function(model, response, options) {
                    self.onModelError(model, response, options);
                }
            });
        }
    });

    _.extend(StashUploadView.prototype, ErrorMixin);

    return StashUploadView;
});