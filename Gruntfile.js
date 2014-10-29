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
                    appDir: "app/webroot/dist-prep/",
                    baseUrl: "js/",
                    dir: "app/webroot/dist/",
                    skipDirOptimize: true,
                    // optimize: "none",
                    modules: [{
                        name: "common",
                        include: ['jquery', 'bootstrap', 'backbone', 'underscore', 'marionette', 'marionette.mustache', 'mustache', 'backbone.wreqr', 'backbone.babysitter', 'text']
                    }, {
                        // module names are relative to baseUrl/paths config
                        name: 'app/app.collectible.detail',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'app/app.home',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'app/app.user.profile',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'app/app.user.settings',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'app/app.collectible.edit',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'controllers/app.home.controller',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'controllers/app.user.profile.controller',
                        exclude: ['common', 'rickshaw']
                    }, {
                        name: 'controllers/app.user.settings.controller',
                        exclude: ['common', 'rickshaw']
                    }]
                }
            }
        },
        copy: {
            prep: {
                files: [
                    // includes files within path and its sub-directories
                    {
                        expand: true,
                        cwd: 'app/webroot/templates/',
                        src: ['**'],
                        dest: 'app/webroot/dist-prep/templates/'
                    }, {
                        expand: true,
                        cwd: 'app/webroot/js/',
                        src: ['**'],
                        dest: 'app/webroot/dist-prep/js/'
                    }, {
                        expand: true,
                        cwd: 'app/webroot/bower_components/',
                        src: ['**'],
                        dest: 'app/webroot/dist-prep/bower_components/'
                    }
                ]
            },
            clean: {
                files: [
                    // includes files within path and its sub-directories
                    {
                        expand: true,
                        cwd: 'app/webroot/dist/js/',
                        src: ['**'],
                        dest: 'app/webroot/js/'
                    }, {
                        expand: true,
                        cwd: 'app/webroot/dist/templates/',
                        src: ['**'],
                        dest: 'app/webroot/templates/'
                    }, {
                        expand: true,
                        cwd: 'app/webroot/dist/bower_components/',
                        src: ['**'],
                        dest: 'app/webroot/bower_components/'
                    }
                ]
            }
        },
        clean: {
            finish: {
                src: ["app/webroot/dist-prep", "app/webroot/dist"]
            }
        }
    });
    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');

    // Default task(s).
    grunt.registerTask('default', ['bower-install-simple']);
    grunt.registerTask('install', ['bower-install-simple', 'requirejs']);
    grunt.registerTask('install:production', ['bower-install-simple', 'copy:prep', 'requirejs', 'copy:clean', 'clean:finish']);
};