    // we need set baseUrl here because this page can have other routes
   require.config({
       baseUrl: '/js'
   });
   require(['require', '../js/common'], function(require, common) {
       require(['require', 'bootstrap', 'jquery.flot', 'jquery.flot.time'], function(require, MyApp) {
           require(['views/app/collectible/detail/view.transactions'], function(TransactionsView) {
               // Get all of the data here
               $.when($.get('/templates/collectibles/status.dust'), $.get('/templates/collectibles/message.dust')).done(function(statusTemplate, messageTemplate) {
                   dust.loadSource(dust.compile(messageTemplate[0], 'message'));


                   // grab the template-stash-add
                   var collectibleModel = new Backbone.Model(collectible);

                   // global variable that comes from the page, status is only for new collectibles
                   if (showStatus) {

                       // since I am only loading one, don't need to index
                       // TODO: This page should use a different view template for different text
                       dust.loadSource(dust.compile(statusTemplate[0], 'status.edit'));

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
                       moviePath: "/assets/flash/ZeroClipboard.swf"
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

               });

           });
       });
   });