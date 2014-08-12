define(['backbone'], function(Backbone) {

    var ManufacturerModel = Backbone.Model.extend({
        urlRoot: '/manufactures/manufacturer',
        validation: {
            title: [{
                pattern: /^[A-Za-z0-9 _]*$/,
                msg: 'Invalid characters'
            }, {
                required: true
            }],
            url: [{
                pattern: 'url',
                msg: 'Must be a valid url.'
            }, {
                required: false
            }]
        }
    });

    return ManufacturerModel;
});