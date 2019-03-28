let mix = require('laravel-mix')
require('laravel-mix-tailwind')


mix
	.setPublicPath('./web')

	// The App Build
	.js('resources/js/styleguide.js', 'assets/js')
	.sass('resources/scss/app.scss', 'assets/css')
	.sass('resources/scss/styleguide.scss', 'assets/css')
	// .copyDirectory('resources/fonts', 'web/assets/fonts')
	// .copyDirectory('resources/img', 'web/assets/img')
	.sourceMaps()
	.browserSync({
		proxy: 'tacwa.test',
		files: [
			'./templates/**/*.twig',
			'./web/css/**/*.css',
			'./web/js/**/*.js',    ]
		})
	.tailwind()
	.version();