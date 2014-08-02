module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        "bower-install-simple": {
            options: {
                production: true,
            }
        },
        requirejs: {
            compile: {
                options: {
                    mainConfigFile: "app/webroot/js/common.js",
                    // appDir: "target/exemplar/build-prep/",
                    // baseUrl: "scripts/",
                    // dir: "target/exemplar/build/",
                    // skipDirOptimize: true,
                    modules: [{
                        name: "common",
                        include: ['jquery', 'bootstrap', 'backbone', 'underscore', 'marionette', 'marionette.mustache', 'mustache', 'backbone.wreqr', 'backbone.babysitter', 'text']
                    }, {
                        // module names are relative to baseUrl/paths config
                        name: 'app/app.collectible.detail',
                        exclude: ['common']
                    }, {
                        name: 'app/app.home',
                        exclude: ['common']
                    }, {
                        name: 'app/app.user.profile',
                        exclude: ['common']
                    }, {
                        name: 'app/app.user.setting',
                        exclude: ['common']
                    }]
                }
            }
        }
    });

    grunt.loadNpmTasks("grunt-bower-install-simple");

    // Default task(s).
    grunt.registerTask('default', ['bower-install-simple']);
    grunt.registerTask('install', ['bower-install-simple', 'requirejs']);
};