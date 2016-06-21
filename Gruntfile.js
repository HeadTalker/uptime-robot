module.exports = function(grunt){

  // Load The Plugins
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-csscomb');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // required for sass/scss
  require('load-grunt-tasks')(grunt);

  // Configure main project settings
  grunt.initConfig({

    // basic settings and info about our plugins
    pkg: grunt.file.readJSON('package.json'),

    // copy some files from bower components to dist folders
    copy: {
      main: {
        files: [
          {
            cwd: 'bower_components/bootstrap/dist/css/',
            src: 'bootstrap.min.css',
            dest: 'main/css/src/',
            expand: true
          },
          {
            cwd: 'bower_components/jquery/dist/',
            src: 'jquery.js',
            dest: 'main/js/src/',
            expand: true
          },
          {
            cwd: 'bower_components/bootstrap/dist/js/',
            src: 'bootstrap.js',
            dest: 'main/js/src/',
            expand: true
          },
          {
            cwd: 'bower_components/Chart.js/dist/',
            src: 'Chart.min.js',
            dest: 'main/js/src/',
            expand: true
          },
          {
            cwd: 'bower_components/tether/dist/js/',
            src: 'tether.min.js',
            dest: 'main/js/src/',
            expand: true
          },
          {
            cwd: 'bower_components/datatables.net/js/',
            src: 'jquery.dataTables.min.js',
            dest: 'main/js/src/',
            expand: true
          },
          {
            cwd: 'bower_components/datatables.net-dt/css/',
            src: 'jquery.dataTables.min.css',
            dest: 'main/css/src/',
            expand: true
          },
          {
            cwd: 'bower_components/datatables.net-dt/images/',
            src: '**/*',
            dest: 'main/img/',
            expand: true
          },
        ]
      }
    },

    // CSS with superpowers
    sass: {
      dist: {
        files: {
          'main/css/src/main.css': 'main/sass/main.scss'
        }
      }
    },

    // make our css beautiful
    csscomb: {
      dist: {
        options: {
          config: 'csscomb.json'
        },
        files: {
          'main/css/src/main.css': ['main/css/src/main.css']
        }
      }
    },

    // minify and combine our css files
    cssmin: {
      combine: {
        files: {
          'main/css/dist/all.styles.min.css':
           [
            'main/css/src/bootstrap.min.css',
            'main/css/src/jquery.dataTables.min.css',
            'main/css/src/main.css'
          ]
        }
      }
    },

    // minify and combine our javascript
    uglify: {
      combine: {
        files: {
          'main/js/dist/all.scripts.min.js':
           [
            'main/js/src/jquery.js',
            'main/js/src/tether.min.js',
            'main/js/src/bootstrap.js',
            'main/js/src/Chart.min.js',
            'main/js/src/jquery.dataTables.min.js',
            'main/js/src/main.js'
          ]
        }
      }
    },

    // watch files for changes and run tasks when they change
    watch: {
      scripts: {
        files: ['main/sass/*.scss', 'main/css/src/*.css', 'main/js/src/*.js', 'Gruntfile.js'],
        tasks: ['copy', 'sass', 'csscomb', 'cssmin', 'uglify'],
        options: {
          spawn: false,
          event:['all']
        }
      }
    }

  });

  // Do the task
  grunt.registerTask('default', ['copy', 'sass', 'csscomb', 'cssmin', 'uglify', 'watch']);

};
