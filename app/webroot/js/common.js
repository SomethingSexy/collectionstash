requirejs.config({
    baseUrl: '/js',
    map: {
        '*' :{
             'uri' : '../bower_components/uri.js/src/URI'
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
        'select2': '../bower_components/select2/select2'
       
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
        // at some point we can turn stash.tools into an AMD module that will pull in all of these deps
        'stash.tools': {
            deps: ['jquery', 'backbone', 'bootstrap', 'dust', 'dust-helpers', 'views/common/stash/view.stash.sell', 'blockui']
        },
        'select2': {
            deps: ['jquery']
        }
    }
});