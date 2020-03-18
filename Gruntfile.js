module.exports = function( grunt ) {
	// Project configuration.
	grunt.initConfig( {
		// Package
		pkg: grunt.file.readJSON( 'package.json' ),

		// PHPLint
		phplint: {
			options: {
				phpArgs: {
					'-lf': null
				}
			},
			all: [
				'**/*.php',
				'!deploy/**',
				'!node_modules/**'
			]
		},

		// PHP Code Sniffer
		phpcs: {
			application: {
				src: [
					'admin/**/*.php',
					'salesfeed.php',
					'uninstall.php'
				]
			},
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'phpcs.xml.dist',
				showSniffCodes: true
			}
		},

		// Check WordPress version
		checkwpversion: {
			options: {
				readme: 'readme.txt',
				plugin: 'salesfeed.php',
			},
			check: {
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			check2: {
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '=='
			}
		},

		// Make POT
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: 'languages',
					type: 'wp-plugin',
					updatePoFiles: true,
					exclude: [
						'deploy/.*',
						'node_modules/.*',
						'vendor/.*',
						'wordpress/.*'
					]
				}
			}
		},

		// Check textdomain errors
		checktextdomain: {
			options:{
				text_domain: 'salesfeed',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php',
					'!deploy/**',
					'!node_modules/**',
					'!tests/**',
					'!vendor/**',
					'!wordpress/**',
					'!wp-content/**'
				],
				expand: true
			}
		},

		// Copy
		copy: {
			deploy: {
				src: [
					'**',
					'!Gruntfile.js',
					'!package.json',
					'!phpcs.ruleset.xml',
					'!deploy/**',
					'!node_modules/**'
				],
				dest: 'deploy/latest',
				expand: true
			},
		},

		// Clean
		clean: {
			deploy: {
				src: [ 'deploy/latest' ]
			},
		},

		// Compress
		compress: {
			deploy: {
				options: {
					archive: 'deploy/archives/<%= pkg.name %>.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'deploy/latest',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		},

		// Git checkout
		gitcheckout: {
			tag: {
				options: {
					branch: 'tags/<%= pkg.version %>'
				}
			},
			develop: {
				options: {
					branch: 'develop'
				}
			}
		},

		// S3
		aws_s3: {
			options: {
				region: 'eu-central-1'
			},
			deploy: {
				options: {
					bucket: 'downloads.pronamic.eu',
					differential: true
				},
				files: [
					{
						expand: true,
						cwd: 'deploy/archives/',
						src: '<%= pkg.name %>.<%= pkg.version %>.zip',
						dest: 'plugins/<%= pkg.name %>/'
					}
				]
			}
		},
		
		// WordPress deploy
		rt_wp_deploy: {
			app: {
				options: {
					svnUrl: 'http://plugins.svn.wordpress.org/<%= pkg.name %>/',
					svnDir: 'deploy/wp-svn',
					svnUsername: 'pronamic',
					deployDir: 'deploy/latest',
					version: '<%= pkg.version %>',
				}
			}
		}
	} );

	// Load tasks
	grunt.loadNpmTasks( 'grunt-phplint' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-checkwpversion' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-git' );
	grunt.loadNpmTasks( 'grunt-aws-s3' );
	grunt.loadNpmTasks( 'grunt-rt-wp-deploy' );

	// Default task(s).
	grunt.registerTask( 'default', [ 'phplint', 'phpcs' ] );
	grunt.registerTask( 'pot', [ 'checktextdomain', 'makepot' ] );

	grunt.registerTask( 'deploy', [
		'default',
		'clean:deploy',
		'copy:deploy',
		'compress:deploy'
	] );

	grunt.registerTask( 'wp-deploy', [
		'gitcheckout:tag',
		'deploy',
		'rt_wp_deploy',
		'gitcheckout:develop'
	] );
	
	grunt.registerTask( 's3-deploy', [
		'gitcheckout:tag',
		'deploy',
		'aws_s3:deploy',
		'gitcheckout:develop'
	] );
};
