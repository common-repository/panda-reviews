module.exports = function(grunt) {
    
    var backendJS = [
        'assets/js/reviews_wp_backend.js',
        'assets/vendor/colorpicker/colors.js',
		'assets/vendor/colorpicker/jqColorPicker.js'
    ];
    
    var frontendJS = [
        'assets/js/frontend-init.js'
    ];
    
    var jsFiles = [].concat(backendJS, frontendJS);
    
    // Project configuration.
    grunt.initConfig({
        
        // concatenate all JS files into 1 single file 
        concat: {
            
            dist1:{
                options: { "separator": ";" },
                src: backendJS,
                dest: "assets/grunt/wpbackendConcat.js"
            },    
        
            dist2:{
                options: { "separator": ";" },
                src: frontendJS,
                dest: "assets/grunt/mainConcat.js"
            }
            
        },
        
        
        
        // minify the js scripts
        uglify: {
            js:{
                files:{
                    'assets/wpbackend.min.js':'assets/grunt/wpbackendConcat.js',
                    'assets/main.min.js':'assets/grunt/mainConcat.js'
                }
            }        
        },
        watch: {
            
            scripts: {
                files: [].concat( backendJS, frontendJS ),
                tasks: ['concat', 'uglify'],
                options: {
                    spawn: false
                }
            }
        }
    });

    // Load required grunt/node modules
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    // Task definitions
    grunt.registerTask('default', ['concat', 'uglify'] );
    
};