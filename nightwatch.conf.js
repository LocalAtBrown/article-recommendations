require('@babel/register');

module.exports = {
	// An array of folders (excluding subfolders) where your tests are located;
	// if this is not specified, the test source must be passed as the second argument to the test runner.
	src_folders: ['nightwatch/tests'],

	// See https://nightwatchjs.org/guide/extending-nightwatch/#writing-custom-commands
	custom_commands_path: 'nightwatch/custom-commands',

	// See https://nightwatchjs.org/guide/extending-nightwatch/#writing-custom-assertions
	custom_assertions_path: 'nightwatch/custom-assertions',

	screenshots: {
		enabled: true,
		path: 'nightwatch/tests_output/screenshots',
		on_failure: true,
		on_error: true,
	},

	test_settings: {
		default: {
			launchUrl: 'https://localnewslab.newspackstaging.com/',
			desiredCapabilities: {
				browserName: 'chrome',
				chromeOptions: {
					args: ['--headless'],
					prefs: {
						download: {
							default_directory: require('path').resolve(__dirname + '/download'),
						},
					},
				},
			},
			webdriver: {
				start_process: true,
				port: 4445,
				server_path: './bin/chromedriver',
			},
			globals: {
				controlUrl:
					'https://localnewslab.newspackstaging.com/2020/12/07/rutrum-vel-euismod-proin-vulputate-nulla-aliquam/#amp-x-canary=0',
				recUrl:
					'https://localnewslab.newspackstaging.com/2020/12/07/rutrum-vel-euismod-proin-vulputate-nulla-aliquam/#amp-x-canary=1',
			},
		},

		firefox: {
			desiredCapabilities: {
				browserName: 'firefox',
			},
			webdriver: {
				start_process: true,
				port: 4447,
				server_path: require('geckodriver').path,
			},
		},

		prod: {
			launchUrl:
				'https://washingtoncitypaper.com/article/517630/contract-of-dc-housing-authority-ed-tyrone-garrett-will-not-be-renewed/#amp-x-canary50=0',
			desiredCapabilities: {
				browserName: 'chrome',
				chromeOptions: {
					args: ['--headless'],
					prefs: {
						download: {
							default_directory: require('path').resolve(__dirname + '/download'),
						},
					},
				},
			},
			webdriver: {
				start_process: true,
				port: 4445,
				server_path: './bin/chromedriver',
			},
			globals: {
				controlUrl:
					'https://washingtoncitypaper.com/article/517630/contract-of-dc-housing-authority-ed-tyrone-garrett-will-not-be-renewed/#amp-x-canary50=0',
				recUrl:
					'https://washingtoncitypaper.com/article/517630/contract-of-dc-housing-authority-ed-tyrone-garrett-will-not-be-renewed/#amp-x-canary50=1',
			},
		},
	},
};
