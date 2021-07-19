require('babel-register')();
module.exports = {
	src_folders: ['nightwatch/tests'],

	webdriver: {
		start_process: true,
		port: 9516,
		server_path: 'node_modules/.bin/chromedriver',
		cli_args: [
			// very verbose geckodriver logs
			'--verbose',
		],
	},

	test_settings: {
		default: {
			launchUrl: 'https://localnewslab.newspackstaging.com/',
			screenshots: {
				enabled: true,
				on_failure: true,
				on_error: true,
				path: 'tests_output/screenshots',
			},
			custom_assertions_path: 'nightwatch/assertions',
			custom_commands_path: 'nightwatch/commands',
			desiredCapabilities: {
				browserName: 'chrome',
				chromeOptions: {
					args: ['--headless'],
				},
			},
		},
	},
};
