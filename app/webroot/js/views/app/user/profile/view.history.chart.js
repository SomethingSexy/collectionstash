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

            var graph = new Rickshaw.Graph({
                element: $('.chart', this.el).get(0),
                renderer: 'bar',
                width: 900,
                height: 250,
                series: [{
                    data: soldData,
                    color: 'steelblue'
                }]
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
            var ticksTreatment = 'glow';
            // var previewXAxis = new Rickshaw.Graph.Axis.Time({
            //     graph: preview.graph,
            //     timeFixture: new Rickshaw.Fixtures.Time.Local(),
            //     ticksTreatment: ticksTreatment
            // });

            // previewXAxis.render();

        }
    });
});