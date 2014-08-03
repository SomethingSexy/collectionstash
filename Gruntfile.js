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
                    appDir: "app/webroot/",
                    baseUrl: "js/",
                    dir: "app/webroot/dist",
                    skipDirOptimize: true,
                    optimize: "none",
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
                        name: 'app/app.user.settings',
                        exclude: ['common']
                    }, {
                        name: 'controllers/app.home.controller',
                        exclude: ['common']
                    }, {
                        name: 'controllers/app.user.profile.controller',
                        exclude: ['common']
                    }, {
                        name: 'controllers/app.user.settings.controller',
                        exclude: ['common']
                    }]
                }
            }
        },
        copy: {
            clean: {
                files: [
                    // includes files within path and its sub-directories
                    {
                        expand: true,
                        cwd: 'app/webroot/dist',
                        src: ['**'],
                        dest: 'app/webroot'
                    }
                ]
            }
        }
    });
    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-copy');

    // Default task(s).
    grunt.registerTask('default', ['bower-install-simple']);
    grunt.registerTask('install', ['bower-install-simple', 'requirejs']);
    grunt.registerTask('install:production', ['bower-install-simple', 'requirejs', 'copy:clean']);
};