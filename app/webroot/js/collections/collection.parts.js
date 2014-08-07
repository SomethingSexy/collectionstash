define(['backbone', 'backbone.pageable'], function(Backbone, pageable) {
    return Backbone.PageableCollection.extend({
        initialize: function(models, props) {
            this.url = "/attributes/index/";
        },
        mode: "infinite",
        state: {
            pageSize: 25,
            query: {}
        },
        queryParams: {
            query: function() {
                return this.state.query;
            },
            "sortKey": "sort",
            "pageSize" : "limit"
        },
        setQuery: function(query, page_size) {
            var state = this.state;
            if (query != state.query) {
                state = _.clone(this._initState)
                //state.pageSize = page_size;
            }
            state = this.state = this._checkState(_.extend({}, state, {
                query: query,
            }));
        }
    });
});


    // var PaginatedPart = Backbone.Paginator.requestPager.extend({
    //     filters: {},
    //     paginator_core: {
    //         // the type of the request (GET by default)
    //         type: 'GET',
    //         // the type of reply (jsonp by default)
    //         dataType: 'json',
    //         // the URL (or base URL) for the service
    //         url: function() {
    //             var queryString = '';
    //             $.each(this.filters, function(index, filterGroup) {
    //                 if (filterGroup.length > 0) {
    //                     var length = filterGroup.length;
    //                     var filterQuery = index + '=';
    //                     $.each(filterGroup, function(index, filter) {
    //                         filterQuery += filter;
    //                         if (index !== length - 1) {
    //                             filterQuery += ',';
    //                         }
    //                     });
    //                     queryString += '&' + filterQuery;
    //                 }
    //             });
    //             var url = '/attributes/index/page:' + this.currentPage + '?' + queryString;
    //             if (this.selectedSort) {
    //                 url = url + '/sort:' + this.selectedSort + '/direction:' + this.sortDirection;
    //             }
    //             return url;
    //         }
    //     },
    //     paginator_ui: {
    //         // the lowest page index your API allows to be accessed
    //         firstPage: 1,
    //         // which page should the paginator start from
    //         // (also, the actual page the paginator is on)
    //         currentPage: 1,
    //         // how many items per page should be shown
    //         perPage: 25,
    //         // a default number of total pages to query in case the API or
    //         // service you are using does not support providing the total
    //         // number of pages for us.
    //         // 10 as a default in case your service doesn't return the total
    //         //totalPages : totalSubmissionPages,
    //         //total : totalSubmission
    //     },
    //     server_api: {
    //         // how many results the request should skip ahead to
    //         // customize as needed. For the Netflix API, skipping ahead based on
    //         // page * number of results per page was necessary.
    //         'page': function() {
    //             return this.currentPage;
    //         }
    //     },
    //     parse: function(response) {
    //         // Be sure to change this based on how your results
    //         // are structured (e.g d.results is Netflix specific)
    //         var tags = response.results;
    //         //Normally this.totalPages would equal response.d.__count
    //         //but as this particular NetFlix request only returns a
    //         //total count of items for the search, we divide.
    //         this.totalPages = response.metadata.paging.pageCount;
    //         this.paginator_ui.totalPages = response.metadata.paging.pageCount;
    //         this.total = response.metadata.paging.count;
    //         this.pagingHtml = response.metadata.pagingHtml;
    //         return tags;
    //     }
    // });