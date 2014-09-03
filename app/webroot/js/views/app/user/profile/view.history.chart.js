define(['require', 'marionette', 'underscore', 'text!templates/app/user/profile/history.chart.mustache', 'mustache', 'rickshaw', 'marionette.mustache'], function(require, Marionette, _, template, mustache, Rickshaw) {

    return Marionette.ItemView.extend({
        className: 'chart',
        template: template,
        initialize: function(options) {

        },
        onRender: function() {
            var model = this.model.toJSON(),
                soldData = [],
                boughtData = [];
            // we need to convert this into a format the chart can understand
            // _.keys(model['sold']);
            // model['sold'].foreach(function(count, time) {
            //     soldData.push({
            //         x: time,
            //         y: count
            //     })
            // });

            for (var prop in model['sold']) {
                if (model['sold'].hasOwnProperty(prop)) {
                    soldData.push({
                        x: parseInt(prop),
                        y: model['sold'][prop]
                    });
                }
            }

            for (var prop in model['bought']) {
                if (model['bought'].hasOwnProperty(prop)) {
                    boughtData.push({
                        x: parseInt(prop),
                        y: model['bought'][prop]
                    });
                }
            }

            var series = [];
            if (soldData.length > 0) {
                soldData = _.sortBy(soldData, 'x');

                series.push({
                    data: soldData,
                    color: '#30c020',
                    name: 'Sold'
                });
            }

            if (boughtData.length > 0) {
                boughtData = _.sortBy(boughtData, 'x');
                series.push({
                    data: boughtData,
                    color: 'steelblue',
                    name: 'Bought'
                });
            }

            if (series.length > 0) {
                Rickshaw.Series.zeroFill(series);
                var graph = new Rickshaw.Graph({
                    element: $('.chart', this.el).get(0),
                    renderer: 'bar',
                    width: 900,
                    height: 250,
                    series: series
                });

                var axes = new Rickshaw.Graph.Axis.Time({
                    graph: graph
                });

                var y_axis = new Rickshaw.Graph.Axis.Y({
                    graph: graph,
                    orientation: 'left',
                    tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
                    element: $('.y_axis', this.el).get(0)
                });

                graph.render();

                var preview = new Rickshaw.Graph.RangeSlider.Preview({
                    graph: graph,
                    element: $('.preview', this.el).get(0),
                });
                var hoverDetail = new Rickshaw.Graph.HoverDetail({
                    graph: graph,
                    formatter: function(series, x, y, formattedX, formattedY, selected) {
                        var date = '<span class="date">' + new Date(x * 1000).toUTCString() + '</span>';
                        var swatch = '<span class="detail_swatch" style="background-color: ' + series.color + '"></span>';
                        var content = swatch + series.name + ": " + parseInt(y) + '<br>' + date;
                        return content;
                    }
                });

            }


        }
    });
});