var NotificationModel = Backbone.Model.extend({
    urlRoot: '/notifications/notification',
    parse: function(attrs) {
        if (attrs && attrs.Notification) {
            return attrs.Notification;
        }
    }
});

var PaginatedNotifications = Backbone.PageableCollection.extend({
    model: NotificationModel,
    mode: "server",
    url: function() {
        var url = '/notifications/notifications';
        return url;
    },
    state: {
        pageSize: 25,
        sortKey: "updated",
        order: 1,
        query: {},
        totalRecords: totalNotifications
    },
    // paginator_ui: {
    //     // the lowest page index your API allows to be accessed
    //     firstPage: 1,

    //     // which page should the paginator start from
    //     // (also, the actual page the paginator is on)
    //     currentPage: 1,

    //     // how many items per page should be shown
    //     perPage: 10,

    //     // a default number of total pages to query in case the API or
    //     // service you are using does not support providing the total
    //     // number of pages for us.
    //     // 10 as a default in case your service doesn't return the total
    //     totalPages: totalNotificationPages,
    //     total: totalNotifications
    // },
    server_api: {

        // how many results the request should skip ahead to
        // customize as needed. For the Netflix API, skipping ahead based on
        // page * number of results per page was necessary.
        'page': function() {
            return this.currentPage;
        }
    },
    parse: function(response) {
        if (response.results && response.metadata) {
            // Be sure to change this based on how your results
            // are structured (e.g d.results is Netflix specific)
            var tags = response.results;
            //Normally this.totalPages would equal response.d.__count
            //but as this particular NetFlix request only returns a
            //total count of items for the search, we divide.
            this.totalPages = response.metadata.paging.pageCount;
            return tags;
        }

        return response;
    }
});

var NotificationsView = Backbone.View.extend({
    template: 'notifications',
    initialize: function() {
        //this.collection.on('change', this.render, this);
        this.collection.on('remove', function() {
            this.collection.paginator_ui.total = this.collection.paginator_ui.total - 1;
        }, this);
        this.collection.on('remove', this.render, this);

        this.collection.on('reset', this.render, this);
    },
    render: function() {
        var self = this;
        var data = {};

        dust.render(this.template, data, function(error, output) {
            $(self.el).html(output);
        });

        this.renderNotifications();

        return this;
    },
    renderNotifications: function() {
        var self = this;
        if (this.collection.size() > 0) {
            this.collection.each(function(model) {
                $('.messages', self.el).append(new NotificationView({
                    model: model
                }).render().el);
            });
        } else {
            $('.messages', this.el).html('<p>There are no new notifications.</p>');
        }
    }
});

var NotificationView = Backbone.View.extend({
    className: 'message',
    template: 'notification',
    events: {
        'click .unread-message': 'markAsRead',
        'click .remove': 'remove',
        'click .title': 'toggleMessage'
    },
    initialize: function() {
        this.listenTo(this.model, 'change', this.render);
    },
    render: function() {
        var self = this;
        var data = this.model.toJSON();
        var open = $('.body', this.el).is(':visible');
        if (typeof data['notification_json_data'] !== 'undefined' && data['notification_json_data'] != null && data['notification_json_data'] !== '') {
            data['notification_json_data'] = JSON.parse(data['notification_json_data']);
        }

        dust.render(this.template, data, function(error, output) {
            $(self.el).html(output);
        });

        if (open) {
            $('.body', this.el).show();
        }

        if (!data.read) {
            $(self.el).addClass('unread');
        }

        return this;
    },
    markAsRead: function() {
        this.model.set('read', true);
        this.model.save();
    },
    remove: function() {
        this.model.destroy();
    },
    toggleMessage: function(event) {
        var self = this;
        event.preventDefault();
        $('.body', this.el).toggle();
        if (!self.model.get('read')) {
            self.markAsRead();
        }
    }
});

$(function() {

    $.when($.get('/templates/notifications/notifications.dust'), $.get('/templates/notifications/notification.dust'), $.get('/templates/common/paging.dust'), $.get('/templates/notifications/types/stash_add.dust'), $.get('/templates/notifications/types/comment_add.dust')).done(function(notificationsTemplate, notificationTemplate, pagingTemplate, stashAddTemplate, commentAddTemplate) {
        dust.loadSource(dust.compile(notificationsTemplate[0], 'notifications'));
        dust.loadSource(dust.compile(notificationTemplate[0], 'notification'));
        dust.loadSource(dust.compile(pagingTemplate[0], 'paging'));
        dust.loadSource(dust.compile(stashAddTemplate[0], 'stash.add'));
        dust.loadSource(dust.compile(commentAddTemplate[0], 'comment.add'));

        $('.panel-body').append(new NotificationsView({
            collection: notifications
        }).render().el);

        $('._pagination').pagination({
            items: notifications.state.totalRecords,
            itemsOnPage: notifications.state.pageSize,
            cssStyle: 'pagination',
            currentPage: notifications.state.currentPage,
            onPageClick: function(pageNumber, event) {
                event.preventDefault();
                notifications.getPage(pageNumber);
            }
            // hrefTextPrefix: 'page/'
        });

        // $('.panel-footer').append(new PagingView({
        //     collection: notifications
        // }).render().el);
    });

});