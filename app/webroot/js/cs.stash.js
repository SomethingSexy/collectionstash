// TODO: Lot's of duplicate code in here

function csStashSuccessMessage(message) {
    $.blockUI({
        message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
        showOverlay: false,
        css: {
            top: '100px',
            'background-color': '#DDFADE',
            border: '1px solid #93C49F',
            'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
            'border-radius': '4px 4px 4px 4px',
            color: '#333333',
            'margin-bottom': '20px',
            padding: '8px 35px 8px 14px',
            'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
            'z-index': 999999
        },
        timeout: 2000
    });

}

! function($) {
    "use strict"; // jshint ;_;

    /* PUBLIC CLASS DEFINITION
     *
     * Add in here later whether or not we quick add or not - TODO
     *
     * This requires, Backbone, dust, stash model, stash view
     ** ============================== */

    var StashFullAdd = function() {};

    StashFullAdd.prototype.initialize = function() {
        dust.loadSource(dust.compile($('#template-stash-add').html(), 'stash.add'));
        var self = this;
        this.stashAddView = null;
        this.collectibleUser = null;
        this.removeFromWishList = false;
        this.$tiles = null;
        this.$stashItem = null;
        this.collectibleUserId = null;

        $('#stash-add-dialog', 'body').on('hidden.bs.modal', function() {
            self.stashAddView.remove();
        });

        $('#stash-add-dialog').on('click', '.save', function() {
            var $button = $(this);
            $button.button('loading');
            self.collectibleUser.save({}, {
                success: function(model, response, options) {
                    $button.button('reset');

                    $('#stash-add-dialog').modal('hide');

                    csStashSuccessMessage('You have successfully added the collectible to your stash!');

                },
                error: function(model, response, options) {
                    $button.button('reset');
                    if (response.status === 400) {
                        self.stashAddView.errors = response.responseJSON;
                        self.stashAddView.onError();
                    }
                }
            });

        });

    };

    StashFullAdd.prototype.add = function(collectibleModel, removeFromWishList, options) {
        if (options.tiles) {
            this.$tiles = $('.tiles');
        }

        this.$stashItem = options.$stashItem;

        this.collectibleUser = new CollectibleUserModel({
            'collectible_id': collectibleModel.get('id')
        });
        this.removeFromWishList = removeFromWishList;
        this.collectibleUserId = options.collectibleUserId;

        if (this.stashAddView) {
            this.stashAddView.remove();
            delete this.stashAddView;
        }

        this.stashAddView = new StashAddView({
            collectible: collectibleModel,
            model: this.collectibleUser
        });

        $('.modal-body', '#stash-add-dialog').html(this.stashAddView.render().el);

        $('#stash-add-dialog').modal();
    };

    /* BUTTON PLUGIN DEFINITION
     * ======================== */

    $.fn.stashfulladd = function(model, removeFromWishList, options) {
        return this.each(function() {
            var $this = $(this);

            stashFullAdd.add(model, removeFromWishList, options);

        });
    };

    $.fn.stashfulladd.defaults = {

    };

    // only want one created really
    var stashFullAdd = new StashFullAdd();

    //$.fn.stashfulladd.Constructor = StashFullAdd

    /* DATA-API
     * =============== */

    $(function() {
        var tile = false;
        if ($('.stashable').hasClass('tiles')) {
            tile = true;
        }

        stashFullAdd.initialize();
        $('.stashable').on('click', '.add-full-to-stash', function(e) {
            var $anchor = $(e.currentTarget);

            var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));

            var removeFromWishList = false;
            var collectibleUserId = null;
            // if this is a wishlist, then we want to add then
            // remove
            if ($anchor.attr('data-type') === 'wishlist') {
                removeFromWishList = true;
                collectibleUserId = $anchor.attr('data-collectible-user-id');
            }

            var $stashItem = $anchor.closest('.stash-item');

            $anchor.stashfulladd(collectibleModel, removeFromWishList, {
                tiles: tile,
                $stashItem: $stashItem,
                collectibleUserId: collectibleUserId
            });
            e.preventDefault();
        });
    });
}(window.jQuery);
! function($) {
    "use strict"; // jshint ;_;

    /* PUBLIC CLASS DEFINITION
     *
     * Add in here later whether or not we quick add or not - TODO
     ** ============================== */

    var StashAdd = function(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, $.fn.stashadd.defaults, options);
        this.collectibleId = this.$element.attr('data-collectible-id');
    };

    StashAdd.prototype.add = function() {
        var self = this;
        $.ajax({
            dataType: 'json',
            type: 'post',
            data: {
                '_method': 'POST'
            },
            url: '/collectibles_users/quickAdd/' + this.collectibleId,
            beforeSend: function(formData, jqForm, options) {

            },
            // success identifies the function to invoke when the server response
            // has been received
            success: function(data, textStatus, jqXHR) {
                if (data.response.isSuccess) {
                    csStashSuccessMessage('The collectible has been added to your Stash!');
                } else {
                    if (data.response.errors) {
                        $.each(data.response.errors, function(index, value) {
                            if (value.inline) {
                                $(':input[name="data[' + value.model + '][' + value.name + ']"]', '#AttributeRemoveForm').after('<div class="error-message">' + value.message + '</div>');
                            } else {
                                $.blockUI({
                                    message: '<button class="close" data-dismiss="alert" type="button">×</button>' + value.message,
                                    showOverlay: false,
                                    css: {
                                        top: '100px',
                                        'background-color': '#DDFADE',
                                        border: '1px solid #93C49F',
                                        'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                                        'border-radius': '4px 4px 4px 4px',
                                        color: '#333333',
                                        'margin-bottom': '20px',
                                        padding: '8px 35px 8px 14px',
                                        'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                                        'z-index': 999999
                                    },
                                    timeout: 2000
                                });
                            }

                        });
                    }
                }
            }
        });
    };
    /* BUTTON PLUGIN DEFINITION
     * ======================== */

    $.fn.stashadd = function(option) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('stashadd'),
                options = typeof option == 'object' && option;
            if (!data) {
                $this.data('stashadd', (data = new StashAdd(this, options)));
            }

            if (option == 'add') {
                data.add();
            }
        });
    };

    $.fn.stashadd.defaults = {

    };

    $.fn.stashadd.Constructor = StashAdd;

    /* DATA-API
     * =============== */

    $(function() {
        $('.stashable').on('click', '.add-to-stash', function(e) {
            var $anchor = $(e.currentTarget);
            $anchor.stashadd('add');
            e.preventDefault();
        });
    });
}(window.jQuery);

// Add to Wishlist
! function($) {
    "use strict"; // jshint ;_;
    /* PUBLIC CLASS DEFINITION
     *
     * Add in here later whether or not we quick add or not - TODO
     ** ============================== */

    var WishListAdd = function(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, $.fn.wishlistadd.defaults, options);
        this.collectibleId = this.$element.attr('data-collectible-id');
    };

    WishListAdd.prototype.add = function() {
        var self = this;
        $.ajax({
            dataType: 'json',
            type: 'post',
            data: {
                '_method': 'POST'
            },
            url: '/collectibles_wish_lists/collectible/' + this.collectibleId,
            beforeSend: function(formData, jqForm, options) {

            },
            // success identifies the function to invoke when the server response
            // has been received
            success: function(data, textStatus, jqXHR) {
                csStashSuccessMessage('The collectible has been added to your Wish List!');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var errorMessage = 'Oops! Something went terribly wrong!';
                if (jqXHR.status === 400) {
                    $.each(jqXHR.responseJSON.response.errors, function(index, value) {
                        errorMessage = value.message;
                    });
                }

                $.blockUI({
                    message: '<button class="close" data-dismiss="alert" type="button">×</button>' + errorMessage,
                    showOverlay: false,
                    css: {
                        top: '100px',
                        'background-color': '#DDFADE',
                        border: '1px solid #93C49F',
                        'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                        'border-radius': '4px 4px 4px 4px',
                        color: '#333333',
                        'margin-bottom': '20px',
                        padding: '8px 35px 8px 14px',
                        'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                        'z-index': 999999
                    },
                    timeout: 2000
                });

            }
        });
    };
    /* BUTTON PLUGIN DEFINITION
     * ======================== */

    $.fn.wishlistadd = function(option) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('wishlistadd'),
                options = typeof option == 'object' && option;
            if (!data) {
                $this.data('wishlistadd', (data = new WishListAdd(this, options)));
            }

            if (option == 'add') {
                data.add();
            }
        });
    };

    $.fn.wishlistadd.defaults = {

    };

    $.fn.wishlistadd.Constructor = WishListAdd;

    /* DATA-API
     * =============== */

    $(function() {
        $('.stashable').on('click', '.add-to-wishlist', function(e) {
            var $anchor = $(e.currentTarget);
            $anchor.wishlistadd('add');
            e.preventDefault();
        });
    });
}(window.jQuery);