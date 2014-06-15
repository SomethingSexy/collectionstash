requirejs.config({
    baseUrl: '/js',
    map: {
        '*': {
            'uri': '../bower_components/uri.js/src/URI'
        }
    },
    paths: {
        templates: '../templates',
        backbone: '../bower_components/backbone/backbone',
        underscore: '../bower_components/underscore/underscore',
        jquery: '../bower_components/jquery/dist/jquery',
        marionette: '../bower_components/backbone.marionette/lib/core/amd/backbone.marionette',
        'marionette.mustache': 'common/backbone.marionette.mustache',
        'mustache': '../bower_components/mustache.js/mustache',
        'backbone.wreqr': '../bower_components/backbone.wreqr/lib/backbone.wreqr',
        'backbone.babysitter': '../bower_components/backbone.babysitter/lib/backbone.babysitter',
        text: '../bower_components/requirejs-text/text',
        bootstrap: '../bower_components/bootstrap/dist/js/bootstrap',
        'backbone.validation': '../bower_components/backbone.validation/dist/backbone-validation-amd',
        'backbone.pageable': '../bower_components/backbone.paginator/lib/backbone.paginator',
        'masonry': '../bower_components/masonry/dist/masonry.pkgd',
        'imagesloaded': '../bower_components/imagesloaded/imagesloaded.pkgd',
        'wookmark': '../bower_components/wookmark/jquery.wookmark',
        'stash.tools': 'cs.stash',
        // for the old stuff that is brought into the newer stuff until it can be rewritten
        'dust': '../bower_components/dustjs-linkedin/dist/dust-full',
        'dust-helpers': '../bower_components/dustjs-linkedin-helpers/dist/dust-helpers',
        'blockui': '../bower_components/blockui/jquery.blockUI',
        'select2': '../bower_components/select2/select2',
        'bootstrap-datepicker': '../bower_components/bootstrap-datepicker/js/bootstrap-datepicker',
        'backbone.trackit': '../bower_components/backbone.trackit/dist/0.1.0/backbone.trackit',
        'simplePagination': '../bower_components/simplePagination/jquery.simplePagination',
        'jquery.flot': '../bower_components/Flot/jquery.flot',
        'jquery.flot.time': '../bower_components/Flot/jquery.flot.time',
        'zeroclipboard': '../bower_components/zeroclipboard/dist/ZeroClipboard',
        'blueimp-gallery': '../bower_components/blueimp-gallery/js/blueimp-gallery',
        'jquery.blueimp-gallery': '../bower_components/blueimp-gallery/js/jquery.blueimp-gallery',
        'blueimp-helper': '../bower_components/blueimp-gallery/js/blueimp-helper'
    },
    shim: {
        'dust': {
            exports: 'dust'
        },
        'dust-helpers': {
            deps: ['dust']
        },
        'bootstrap': {
            deps: ['jquery']
        },
        'bootstrap-datepicker': {
            deps: ['bootstrap']
        },
        // at some point we can turn stash.tools into an AMD module that will pull in all of these deps
        'stash.tools': {
            deps: ['jquery', 'backbone', 'bootstrap', 'dust', 'dust-helpers', 'views/common/stash/view.stash.sell', 'blockui']
        },
        'select2': {
            deps: ['jquery']
        },
        'backbone.trackit': {
            deps: ['backbone']
        },
        'simplePagination': {
            deps: ['jquery', 'bootstrap']
        },
        'jquery.flot': {
            deps: ['jquery']
        },
        'jquery.flot.time': {
            deps: ['jquery.flot']
        }
    }
});