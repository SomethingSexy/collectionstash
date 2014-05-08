requirejs.config({
    baseUrl: '/js',
    paths: {
        templates: '../templates',
        backbone: '../bower_components/backbone/backbone',
        underscore: '../bower_components/underscore/underscore',
        jquery : '../bower_components/jquery/dist/jquery',
        marionette : '../bower_components/backbone.marionette/lib/core/amd/backbone.marionette',
        'marionette.mustache': 'common/backbone.marionette.mustache',
        'mustache': '../bower_components/mustache.js/mustache',
        'backbone.wreqr' : '../bower_components/backbone.wreqr/lib/backbone.wreqr',
        'backbone.babysitter' : '../bower_components/backbone.babysitter/lib/backbone.babysitter',
        text: '../bower_components/requirejs-text/text',
        bootstrap: '../bower_components/bootstrap/dist/js/bootstrap',
        'backbone.validation' : '../bower_components/backbone.validation/dist/backbone-validation-amd',
        'backbone.pageable' : '../bower_components/backbone-pageable/lib/backbone-pageable',
        'masonry': '../bower_components/masonry/dist/masonry.pkgd',
        'imagesloaded' : '../bower_components/imagesloaded/imagesloaded.pkgd',
        'wookmark' : '../bower_components/wookmark/jquery.wookmark'
    },
    shim: {
        'dust': {
            exports: 'dust'
        },
        'dust-helpers': {
            deps : ['dust']
        },
        'bootstrap': {
          deps: ['jquery']
        }
    }
});