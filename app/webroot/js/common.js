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
        'backbone.trackit': '../bower_components/backbone.trackit/backbone.trackit',
        'simplePagination': '../bower_components/simplePagination/jquery.simplePagination',
        'jquery.flot': '../bower_components/Flot/jquery.flot',
        'jquery.flot.time': '../bower_components/Flot/jquery.flot.time',
        'zeroclipboard': '../bower_components/zeroclipboard/dist/ZeroClipboard',
        'blueimp-gallery': '../bower_components/blueimp-gallery/js/blueimp-gallery',
        'jquery.blueimp-gallery': '../bower_components/blueimp-gallery/js/jquery.blueimp-gallery',
        'blueimp-helper': '../bower_components/blueimp-gallery/js/blueimp-helper',
        'jquery.blockui': '../bower_components/blockui/jquery.blockUI',
        'd3': '../bower_components/d3/d3.min',
        'rickshaw': '../bower_components/rickshaw/rickshaw.min',
        'jquery-ui': '../bower_components/jquery-ui/ui',
        'blockies': '../bower_components/blockies/blockies',
        'jquery.form': '../bower_components/jquery-form/jquery.form',
        "jquery.treeview": "../bower_components/jquery.treeview/jquery.treeview",
        "backbone.bootstrap-modal": "../bower_components/backbone.bootstrap-modal/src/backbone.bootstrap-modal",
        "tmpl": "../bower_components/blueimp-tmpl/js/tmpl",
        "load-image": "../bower_components/blueimp-load-image/js/load-image",
        "load-image-ios": "../bower_components/blueimp-load-image/js/load-image-ios",
        "load-image-exif": "../bower_components/blueimp-load-image/js/load-image-exif",
        "load-image-meta": "../bower_components/blueimp-load-image/js/load-image-meta",
        "canvas-to-blob": "../bower_components/blueimp-canvas-to-blob/js/canvas-to-blob",
        "jquery.ui.widget": "../bower_components/jquery-ui/ui/widget",
        'jquery.fileupload': '../bower_components/blueimp-file-upload/js/jquery.fileupload',

        'jquery.fileupload-audio': '../bower_components/blueimp-file-upload/js/jquery.fileupload-audio',
        'jquery.fileupload-video': '../bower_components/blueimp-file-upload/js/jquery.fileupload-video',

        'jquery.iframe-transport': '../bower_components/blueimp-file-upload/js/jquery.iframe-transport',
        'jquery.postmessage-transport': '../bower_components/blueimp-file-upload/js/cors/jquery.postmessage-transport',

        'jquery.fileupload-process': '../bower_components/blueimp-file-upload/js/jquery.fileupload-process',
        'jquery.fileupload-image': '../bower_components/blueimp-file-upload/js/jquery.fileupload-image',
        'jquery.fileupload-validate': '../bower_components/blueimp-file-upload/js/jquery.fileupload-validate',
        'jquery.fileupload-ui': '../bower_components/blueimp-file-upload/js/jquery.fileupload-ui'
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
        // 'backbone.trackit': {
        //     deps: ['backbone']
        // },
        'simplePagination': {
            deps: ['jquery', 'bootstrap']
        },
        'jquery.flot': {
            deps: ['jquery']
        },
        'jquery.flot.time': {
            deps: ['jquery.flot']
        },
        'blockies': {
            exports: 'blockies'
        },
        'jquery.treeview': {
            deps: ['jquery']
        },
        'cs.core.tree': {
            deps: ['jquery']
        },
        // 'jquery.getimagedata': {
        //     deps: ['jquery']
        // },
        'cs.attribute': {
            deps: ['jquery', 'bootstrap']
        }
    }
});