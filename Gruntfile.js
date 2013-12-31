var javascriptFiles = [
    "<%= bowerRoot %>/flat-ui-official/js/jquery-2.0.3.min.js",
    "<%= bowerRoot %>/flat-ui-official/js/jquery-ui-1.10.3.min.js",
    "<%= bowerRoot %>/flat-ui-official/js/jquery.placeholder.js",
    "<%= bowerRoot %>/flat-ui-official/js/jquery.stackable.js",
    "<%= bowerRoot %>/flat-ui-official/js/jquery.tagsinput.js",
    "<%= bowerRoot %>/flat-ui-official/js/bootstrap.min.js",
    "<%= bowerRoot %>/flat-ui-official/js/bootstrap-select.js",
    "<%= bowerRoot %>/flat-ui-official/js/bootstrap-switch.js",
    "<%= bowerRoot %>/flat-ui-official/js/bootstrap-typeahead.js",
    "<%= bowerRoot %>/flat-ui-official/js/icon-font-ie7.js",
    "<%= bowerRoot %>/flat-ui-official/js/flatui-checkbox.js",
    "<%= bowerRoot %>/flat-ui-official/js/flatui-radio.js",
    "<%= bowerRoot %>/flat-ui-official/js/application.js",
    "<%= bowerRoot %>/angular/angular.js",
    "<%= bowerRoot %>/angular-resource/angular-resource.js",
    "<%= bowerRoot %>/angular-route/angular-route.js",
    "<%= bowerRoot %>/highcharts/hightcharts.js",
    "<%= compiledRoot %>/app.js"
];

var stylesheetFiles = [
    "<%= bowerRoot %>/flat-ui-official/css/flat-ui.css",
    "<%= compiledRoot %>/app.css"
];

module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        root: "public/assets",
        bowerRoot: "assets/vendor",

        coffeeRoot: "assets/coffee",
        sassRoot: "assets/sass",
        compiledRoot: "assets/compiled",

        jsRoot: "<%= root %>/js",
        cssRoot: "<%= root %>/css",
        imgRoot: "<%= root %>/images",
        fontRoot: "<%= root %>/fonts",

        concat: {
            javascripts: {
                src: javascriptFiles,
                dest: '<%= jsRoot %>/app.js'
            },
            stylesheets: {
                src: stylesheetFiles,
                dest: '<%= cssRoot %>/app.css'
            }
        },

        coffee: {
            compile: {
                options: {
                    join: true,
                    bare: true
                },
                files: {
                    '<%= compiledRoot %>/app.js': [
                        '<%= coffeeRoot %>/app.coffee',
                        '<%= coffeeRoot %>/statistics.coffee',
                        '<%= coffeeRoot %>/charts/*.coffee',
                    ]
                }
            }
        },

        sass: {
            dist: {
                options: {
                    style: 'expanded'
                },
                files: {
                    '<%= compiledRoot %>/app.css': '<%= sassRoot %>/app.sass'
                }
            }
        },

        watch: {
            coffee: {
                files: ['<%= coffeeRoot %>/**/*'],
                tasks: ['coffee', 'concat:javascripts'],
                options: {
                    spawn: false,
                }
            },
            sass: {
                files: ['<%= sassRoot %>/**/*'],
                tasks: ['sass', 'concat:stylesheets'],
                options: {
                    spawn: false,
                }
            }
        },

        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        cwd: '<%= bowerRoot %>/flat-ui-official/images',
                        src: '**',
                        dest: '<%= imgRoot %>',
                        flatten: false
                    },
                    {
                        expand: true,
                        cwd: '<%= bowerRoot %>/flat-ui-official/fonts',
                        src: '**',
                        dest: '<%= fontRoot %>',
                        flatten: false
                    },
                    {
                        src: '<%= bowerRoot %>/flat-ui-official/js/html5shiv.js',
                        dest: '<%= jsRoot %>/html5shiv.js'
                    }
                ]
            }
        },

        clean: {
            build: ['<%= root %>/*']
        }
    });

    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.registerTask('default', ['watch']);

    grunt.registerTask('build', ['clean', 'copy', 'coffee', 'sass', 'concat']);
};

