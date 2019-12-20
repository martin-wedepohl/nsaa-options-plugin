// Load Gulp...of course
var gulp = require('gulp');

// CSS related plugins
var sass         = require( 'gulp-sass' );
var autoprefixer = require( 'gulp-autoprefixer' );

// JS related plugins
var uglify       = require( 'gulp-uglify' );
var babelify     = require( 'babelify' );
var browserify   = require( 'browserify' );
var source       = require( 'vinyl-source-stream' );
var buffer       = require( 'vinyl-buffer' );
var stripDebug   = require( 'gulp-strip-debug' );

// Utility plugins
var rename       = require( 'gulp-rename' );
var sourcemaps   = require( 'gulp-sourcemaps' );
var notify       = require( 'gulp-notify' );
var options      = require( 'gulp-options' );
var gulpif       = require( 'gulp-if' );
var image        = require( 'gulp-image' );

var devDir  = './';
var baseDir = '../../wp-content/plugins/nsaa-options-plugin/';

// Style Sheets
var styleSRC     = devDir + 'src/scss/style.scss';
var styleSRC_FE  = devDir + 'src/scss/style-fe.scss';
var styleINST    = devDir + 'src/scss/instructions.scss'
var styleFiles   = [styleSRC, styleSRC_FE, styleINST];
var styleDEST    = baseDir + 'dist/css/';

// Javascript
var jsSRC        = devDir + 'src/js/';
var jsINST       = 'instructions.js'
var jsAdmin      = 'scripts.js';
var jsFE         = 'scripts-fe.js';
var jsFiles      = [jsAdmin, jsINST, jsFE];
var jsDEST       = baseDir + 'dist/js/';

// Images
//var imageSRC     = devDir + '/src/images/*';
//var imageURL     = baseDir + 'dist/images/';

// Index files watch
var srcIndexWatch     = devDir + 'src/index.php';
var srcJSIndexWatch   = devDir + 'src/js/index.php';
var distJS            = baseDir + 'dist/js';
var srcScssIndexWatch = devDir + 'src/scss/index.php';
var distCSS           = baseDir + 'dist/css'
var vendorDir         = baseDir + 'vendor/';
var assetsDir         = baseDir + 'assets/';

// Watches
var styleWatch    = devDir + 'src/scss/**/*.scss';
var jsWatch       = devDir + 'src/js/**/*.js';
var includesWatch = devDir + 'includes/**/*.php';
var templatesWatch = devDir + 'templates/**/*.php';
var distIncludes  = baseDir + 'includes';
var distTemplates  = baseDir + 'templates';
var vendorWatch   = devDir + 'vendor/**/*.*';
var assetsWatch   = devDir + 'assets/**/*.*';
var rootFiles      = ['./LICENSE', './README.md', 'composer.json', './gulpfile.js', './package.json','./readme.txt'];
var rootPhp       = './*.php';
//var imageWatch   = devDir + 'src/images/**/*';

function style(done) {
    gulp
        .src(styleFiles)
        .pipe(sourcemaps.init())   // Initialize sourcemaps before compilation starts
        .pipe(sass({
            errLogToConsole: true,
            outputStyle: 'compressed'
        }))
        .on("error", sass.logError)
        .pipe( autoprefixer({ overrideBrowserslist: [ 'last 2 versions', '> 5%', 'Firefox ESR', 'not dead' ] }) )        
        .pipe( rename( { suffix: '.min' } ) )
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(styleDEST));
    done();
}

function script(done) {
	jsFiles.map(function (entry) {
		return browserify({
			entries: [jsSRC + entry]
		})
		.transform( babelify, { presets: [ '@babel/preset-env' ] } )
		.bundle()
		.pipe( source( entry ) )
		.pipe( buffer() )
		.pipe( gulpif( options.has( 'production' ), stripDebug() ) )
		.pipe( sourcemaps.init({ loadMaps: true }) )
		.pipe( uglify() )
        .pipe( rename( { suffix: '.min' } ) )
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest( jsDEST ) )
	});
    done();
}

//function compress_images( done ) {
//    src( imageSRC )
//        .pipe(image())
//        .pipe( dest( imageURL ) );
//    done();
//}

function copyIndex(done) {
    gulp.src(srcIndexWatch, {allowEmpty: true})
            .pipe(gulp.dest(baseDir + './dist/'));
    done();
}

function copyRootFiles(done) {
    gulp.src(rootFiles, {allowEmpty: true})
        .pipe(gulp.dest(baseDir));
    done();
}

function copyRootPhp(done) {
    gulp.src(rootPhp)
        .pipe(gulp.dest(baseDir));
    done();
}

function copyJSIndex(done) {
    gulp.src(srcJSIndexWatch, {allowEmpty: true})
        .pipe(gulp.dest(distJS));
    done();
}

function copyCSSIndex(done) {
    gulp.src(srcScssIndexWatch, {allowEmpty: true})
        .pipe(gulp.dest(distCSS));
    done();
}

function copyInclude(done) {
    gulp.src(includesWatch, {allowEmpty: true})
        .pipe(gulp.dest(distIncludes));
    done();
}

function copyTemplates(done) {
    gulp.src(templatesWatch, {allowEmpty: true})
        .pipe(gulp.dest(distTemplates));
    done();
}

function copyVendor(done) {
    gulp.src(vendorWatch, {allowEmpty: true})
        .pipe(gulp.dest(vendorDir));
    done();
}

function copyAssets(done) {
    gulp.src(assetsWatch, {allowEmpty: true})
        .pipe(gulp.dest(assetsDir));
    done();
}

function watchFiles() {
    gulp.watch(styleWatch, style);
    gulp.watch(jsWatch, script);
    gulp.watch(srcIndexWatch, copyIndex);
    gulp.watch(rootFiles, copyRootFiles);
    gulp.watch(rootPhp, copyRootPhp);
    gulp.watch(srcJSIndexWatch, copyJSIndex);
    gulp.watch(srcScssIndexWatch, copyCSSIndex);
    gulp.watch(includesWatch, copyInclude);
    gulp.watch(templatesWatch, copyTemplates);
    gulp.watch(vendorWatch, copyVendor);
    gulp.watch(assetsWatch, copyAssets);
    gulp.src(styleSRC).pipe(notify({message: 'Gulp is Watching, Happy Coding!'}));
}

var watch = gulp.parallel(style, script, copyIndex, copyRootFiles, copyRootPhp, copyJSIndex, copyCSSIndex, copyInclude, copyTemplates, copyVendor, copyAssets, watchFiles);

exports.style = style;
exports.script = script;
exports.watch = watch;
exports.default = watch;
