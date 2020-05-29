// I don't feel like writing var everytime
var gulp = require("gulp"),
    sass = require("gulp-sass"),
    postcss = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    cssnano = require("cssnano"),
    sourcemaps = require("gulp-sourcemaps");


var browserSync = require("browser-sync").create();


// Put this after including our dependencies
var paths = {
    styles: {
        // By using styles/**/*.sass we're telling gulp to check all folders for any sass file
        src: "./scss/*.scss",
        // Compiled files will end up in whichever folder it's found in (partials are not compiled)
        dest: "styles"
    }

    // Easily add additional paths
    // ,html: {
    //  src: '...',
    //  dest: '...'
    // }
};

// ...
function style() {
    return (
        gulp
            .src("./scss/*.scss")
            // Initialize sourcemaps before compilation starts
            .pipe(sourcemaps.init())
            .pipe(sass())
            .on("error", sass.logError)
            // Use postcss with autoprefixer and compress the compiled file using cssnano
            .pipe(postcss([autoprefixer(), cssnano()]))
            // Now add/write the sourcemaps
            .pipe(sourcemaps.write())
            .pipe(gulp.dest("../styles"))
            .pipe(browserSync.stream())
    );
}


exports.style = style;


function watch() {
    gulp.watch("scss/*.scss", style);
}

exports.watch = watch;