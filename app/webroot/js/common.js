requirejs.config({
    baseUrl: '/js',
    paths: {
        templates: '../templates',
        backbone: '../bower_components/backbone/backbone',
        underscore: '../bower_components/underscore/underscore',
        jquery : '../bower_components/jquery/dist/jquery',
        marionette : '../bower_components/backbone.marionette/lib/core/amd/backbone.marionette',
        'marionette-dust': '../bower_components/backbone.marionette.dust/src/amd/backbone.marionette.dust',
        'backbone.wreqr' : '../bower_components/backbone.wreqr/lib/backbone.wreqr',
        'backbone.babysitter' : '../bower_components/backbone.babysitter/lib/amd/backbone.babysitter',
        'backbone.marionette.dust': '../bower_components/backbone.marionette.dust/src/amd/backbone.marionette.dust',
        dust: '../bower_components/dustjs-linkedin/dist/dust-full',
        'dust-helpers': '../bower_components/dustjs-helpers/dist/dust-helpers',
        text: '../bower_components/requirejs-text/text',
        bootstrap: '../bower_components/bootstrap/dist/js/bootstrap'
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