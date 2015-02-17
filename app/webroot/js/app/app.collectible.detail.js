define(['require', 'views/app/collectible/detail/view.transactions', 'zeroclipboard', 'views/common/stash/view.stash.add', 'backbone', 'marionette', 'views/common/modal.region', 'models/model.collectible.user', 'models/model.collectible.wishlist', 'views/common/growl', 'views/common/view.comments', 'views/common/view.comment.add', 'collections/collection.comments', 'views/common/cs.subscribe', 'models/model.status', 'views/view.status', 'jquery.blueimp-gallery', 'bootstrap', 'jquery.flot', 'jquery.flot.time'], function(require, TransactionsView, ZeroClipboard, StashAddView, Backbone, Marionette, ModalRegion, CollectibleUser, CollectibleWishlist, growl, CommentsView, CommentAddView, CommentsCollection, subscribe, Status, StatusView) {

    //bootstrap this until it is switched over to a Marionette App
    return {
        start: function() {
            // Get all of the data here

            // grab the template-stash-add
            var collectibleModel = new Backbone.Model(collectible);

            var comments = new CommentsCollection(rawComments || {});
            var permissions = new Backbone.Model(rawPermissions);


            // global variable that comes from the page, status is only for new collectibles
            if (showStatus) {

                // since I am only loading one, don't need to index
                // TODO: This page should use a different view template for different text


                var status = new Status();
                status.set(collectibleStatus, {
                    silent: true
                });

                $('#status-container').html(new StatusView({
                    model: status,
                    allowEdit: allowStatusEdit,
                    collectible: collectibleModel,
                    // can't global delete from view anyway
                    allowDelete: false
                }).render().el);

                // If the status has changed and I am on the view
                //page and they change the status and it is a draft
                // go to the edit page
                status.on('sync', function() {
                    if (this.toJSON().status.id === '1') {
                        window.location.href = '/collectibles/edit/' + this.id;
                    }
                }, status);

            } else {
                $('#status-container').remove();
            }

            $('span.popup', '.attributes-list').popover({
                placement: 'bottom',
                html: 'true',
                template: '<div class="popover" onmouseover="$(this).mouseleave(function() {$(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
            }).click(function(e) {
                e.preventDefault();
            }).mouseenter(function(e) {
                $(this).popover('show');
            });

            //This is for admin mode
            $('#approval-button').click(function() {
                $('#approve-input').val('true');
                $('#approval-form').submit();
            });
            $('#deny-button').click(function() {
                $('#approve-input').val('false');
                $('#approval-form').submit();
            });

            // run the subscribe setup code
            subscribe.run();

            var DetailLayout = Backbone.Marionette.Layout.extend({
                el: '#collectible-container',
                regions: {
                    modal: ModalRegion,
                    comments: '#comments'
                }
            });

            var detailLayout = new DetailLayout();

            $('.add-full-to-stash').on('click', function() {
                $anchor = $(this);
                var model = new CollectibleUser({
                    'collectible_id': collectibleModel.get('id')
                });
                model.collectible = collectibleModel;

                model.once('sync', function() {
                    detailLayout.modal.hideModal();
                    growl.onSuccess('You have successfully added the collectible to your stash!');
                });

                detailLayout.modal.show(new StashAddView({
                    model: model,
                    stashCount: $anchor.data('stash-count'),
                    wishlistCount: $anchor.data('wishlist-count')
                }));
            });

            $('.add-to-wishlist').on('click', function() {
                var model = new CollectibleWishlist({
                    'collectible_id': collectibleModel.get('id')
                });

                model.save({}, {
                    // apparently I am adding this to the URL? dumb TODO
                    url: model.url() + '/' + collectibleModel.get('id'),
                    success: function() {
                        growl.onSuccess('The collectible has been added to your Wish List!');
                    },
                    error: function(model, response, options) {
                        var errorMessage = 'Oops! Something went terribly wrong!';
                        if (response.status === 400) {
                            $.each(response.responseJSON.response.errors, function(index, value) {
                                errorMessage = value.message;
                            });
                        }

                        growl.onError(errorMessage)
                    }
                })
            });


            var commentsView = new CommentsView({
                collection: comments,
                permissions: permissions
            });

            commentsView.on('comment:add', function(id) {
                var model = new comments.model();
                model.set('entity_type_id', collectibleModel.get('entity_type_id'));

                // set the last comment created so that this will return any comments
                // created in the mean time
                if (!comments.isEmpty()) {
                    model.set('last_comment_created', comments.last().get('created'));
                }

                detailLayout.modal.show(new CommentAddView({
                    model: model
                }));

                model.once('sync', function(model, response, options) {
                    if (_.isArray(response)) {
                        comments.add(response);
                    }

                    detailLayout.modal.hideModal();
                });
            });

            commentsView.on('comment:edit', function(id) {
                var model = comments.get(id);

                detailLayout.modal.show(new CommentAddView({
                    model: model
                }));

                model.once('sync', function(model, response, options) {
                    // this gets called before tracking is finished updating 
                    detailLayout.modal.hideModal();
                });
            });

            detailLayout.comments.show(commentsView);


            $('#carousel-example-generic').carousel({
                wrap: true
            });


            new TransactionsView({
                collectible: collectibleModel,
                allowDeleteListing: allowDeleteListing,
                allowAddListing: allowAddListing
            });

            // lol this should probably get moved to the view file
            function showTooltip(x, y, contents) {

                $("<div id='tooltip'>" + contents + "</div>").css({
                    position: "absolute",
                    display: "none",
                    top: y + 5,
                    left: x + 5,
                    border: "1px solid #fdd",
                    padding: "2px",
                    "background-color": "#fee",
                    opacity: 0.80
                }).appendTo("body").fadeIn(200);
            }

            if (!_.isEmpty(transactionsGraphData)) {
                $.plot("#holder", [transactionsGraphData], {
                    xaxis: {
                        mode: "time",
                        timeformat: "%m/%d/%y",
                    },
                    yaxes: [{
                        min: 0
                    }],
                    series: {
                        points: {
                            show: true
                        },
                        lines: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                    },
                });
                var previousPoint = null;
                $("#holder").bind("plothover", function(event, pos, item) {

                    if (item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;
                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2);
                            var date = new Date(parseFloat(x));
                            showTooltip(item.pageX, item.pageY, 'Sold on ' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + " for $" + y);
                        }
                    } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }

                });
            }

            $('.selectable').on('click', function() {
                $(this).select();
            });

            $('.btn-copy').tooltip({
                trigger: 'manual'
            });

            var clip = new ZeroClipboard([document.getElementById("copy-to-clipboard-direct"), document.getElementById("copy-to-clipboard-bbcode"), document.getElementById("copy-to-clipboard-bbcodeimage")], {
                swfPath: "/assets/flash/ZeroClipboard.swf"
            });

            clip.on("load", function(client) {

                client.on("complete", function(client, args) {
                    var $button = $(this);
                    $button.tooltip('show');
                    setTimeout(function() {
                        $button.tooltip('hide');
                    }, 500);
                });
            });

        }
    };
});