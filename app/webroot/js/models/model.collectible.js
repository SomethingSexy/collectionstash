if (!adminMode) {
    var adminMode = false;
}

(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['backbone', 'collections/collection.collectible.parts'], factory);
    } else {
        // Browser globals
        root.CollectibleModel = factory(root.Backbone, root.PartsCollection);
    }
}(this, function(backbone, PartsCollection) {

    return Backbone.Model.extend({
        urlRoot: function() {
            return '/collectibles/collectible';
        },
        parse: function(response) {
            if (!this.parts) {
                this.parts = new PartsCollection(response.AttributesCollectible, {
                    parse: true
                });
            } else {
                this.parts.reset(response.AttributesCollectible);
            }

            delete response.AttributesCollectible;

            return response;
        },
        validation: {
            msrp: [{
                    pattern: 'number',
                    msg: 'Must be a valid amount.'
                }, {
                    required: false
                }
                // , {
                //     pattern: /^[a-z0-9- &$%#@!*()+_#:.,'"\\/]+$/i,
                //     msg: 'Invalid characters'
                // }
            ],
            description: [{
                rangeLength: [0, 1000],
                msg: 'Invalid length.'
            }, {
                required: false
            }, {
                pattern: /^[a-z0-9\s\r\n ?&$%#@!*()+_\\\\#:.',"\/-]+$/i,
                msg: 'Invalid characters'
            }],
            'edition_size': [{
                pattern: 'digits',
                msg: 'Must be numeric.'
            }, {
                required: false
            }],
            upc: [{
                required: false
            }, {
                pattern: 'digits',
                msg: 'Must be numeric.'
            }, {
                maxLength: 12,
                msg: 'Must be a valid length.'
            }],
            'product_length': [{
                pattern: 'number',
                msg: 'Must be a valid length.'
            }, {
                required: false
            }],
            'product_width': [{
                pattern: 'number',
                msg: 'Must be a valid width.'
            }, {
                required: false
            }],
            'product_depth': [{
                pattern: 'number',
                msg: 'Must be a valid depth.'
            }, {
                required: false
            }],
            url: [{
                pattern: 'url',
                msg: 'Must be a valid url.'
            }, {
                required: false
            }],
            pieces: [{
                pattern: 'digits',
                msg: 'Must be numeric.'
            }, {
                required: false
            }],
            retailer: [{
                rangeLength: [4, 150],
                msg: 'Invalid length.  Must be between 4 and 150 characters.'
            }, {
                required: false
            }],
        }
    });
}));