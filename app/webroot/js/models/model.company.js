define(function(require) {

    var Backbone = require('backbone'),
        _ = require('underscore');
    require('backbone.validation');

    var ManufacturerModel = Backbone.Model.extend({
        urlRoot: '/manufactures/manufacturer',
        validation: {
            title: [{
                pattern: /^[a-z0-9\s\r\n &$%#@!*()+_\\\\#:.,'"\/-]+$/i,
                msg: 'Invalid characters'
            }, {
                required: true
            }],
            url: [{
                pattern: 'url',
                msg: 'Must be a valid url.'
            }, {
                required: false
            }],
            bio: [{
                rangeLength: [0, 5000],
                msg: 'Invalid length.'
            }, {
                required: false
            }, {
                pattern: /^[a-z0-9\s\r\n &$%#@!*()+_\\\\#:.,'"\/-]+$/i,
                msg: 'Invalid characters'
            }]
        },
        parse: function(response) {
            if (response.Upload) {
                if (!this.photo) {
                    this.photo = new Backbone.Model(response.Upload);
                } else {
                    this.photo.clear({
                        silent: true
                    });
                    this.photo.set(response.Upload);
                }
                delete response.Upload;
            } else {
                delete this.photo;
            }

            return response;
        }
    });

    _.extend(ManufacturerModel.prototype, Backbone.Validation.mixin);

    return ManufacturerModel;
});