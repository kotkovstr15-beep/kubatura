let gulp = require('gulp'),
  gutil = require('gulp-util'), //Вывод уведомления в консоль, так как в gulp нет встроенного лога
  sass = require('gulp-sass'), //Подключаем Sass пакет
  browserSync = require('browser-sync'), // Подключаем автообновление через Browser Sync
  concat = require('gulp-concat'), // Подключаем gulp-concat (для конкатенации файлов)
  uglify = require('gulp-uglify'), // Подключаем gulp-uglifyjs (для сжатия JS)
  cleanCSS = require('gulp-clean-css'), // Минификация CSS
  rename = require('gulp-rename'), // Подключаем библиотеку для переименования файлов
  imagemin = require('gulp-imagemin'), // Подключаем библиотеку для работы с изображениями
  cache = require('gulp-cache'), // Подключаем библиотеку кеширования ///
  autoprefixer = require('gulp-autoprefixer'), // Подключаем библиотеку для автоматического добавления префиксов
  notify = require("gulp-notify"); // Водит ошибки при сборке Gulp в виде системных сообщений

// ==================== Создаём и описываем функции в новом формате
let styles = () => {
  return gulp.src('app/sass/**/*.sass')
    .pipe(sass({
      outputStyle: 'expanded',
      includePaths: require('node-bourbon').includePaths
    }).on("error", notify.onError()))
    .pipe(rename({
      suffix: '.min',
      prefix: ''
    }))
    .pipe(autoprefixer({
			grid: true,
      overrideBrowserslist: ['last 4 versions']
    }))
    .pipe(cleanCSS())
    .pipe(gulp.dest('app/css'))
    .pipe(browserSync.stream())
};

let scripts = () => {
  return gulp.src([
      'app/libs/jquery/jquery-2.2.3.min.js',
      'app/libs/magnific-popup/jquery.magnific-popup.min.js',
      'app/libs/maskedinput/jquery.maskedinput.min.js',
      'app/libs/swiper/swiper.min.js',
      'app/libs/formstyler/jquery.formstyler.min.js',
      'app/libs/range-slider/ion.rangeSlider.min.js',
      'app/libs/jquery.matchHeight-min.js',
    ])
    .pipe(concat('libs.min.js'))
    .pipe(gulp.dest('app/js'))
    .pipe(browserSync.reload({
      stream: true
    }))
};

let html = () => {
  return gulp.src('app/**/*.html')
    .pipe(browserSync.reload({
      stream: true
    }))
};

let imgmin = () => {
  return gulp.src('app/img/**/*')
    .pipe(cache(imagemin()))
    .pipe(gulp.dest('app/img-min/'))
};

// ==================== Отслеживаем любые изменения в файлах
let watch = () => {
  browserSync.init({
    server: {
      baseDir: 'app'
    },
    notify: false,
    // open: false,
    // online: false, // Work Offline Without Internet Connection
    // tunnel: true, tunnel: "projectname", // Demonstration page: http://projectname.localtunnel.me
  });
  gulp.watch('app/sass/**/*.sass', styles);
  gulp.watch(['app/**/*.js', '!app/js/libs.min.js'], scripts);
  gulp.watch('app/**/*.html', html);
};

// ==================== Объявляем и описываем таски
gulp.task('imgmin', imgmin);

gulp.task('default', gulp.series(scripts, styles, watch, browserSync));
