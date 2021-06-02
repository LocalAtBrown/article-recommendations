/**
 * Checks Snowplow Micro for no bad events
 *
 * ```
 *    this.demoTest = function (browser) {
 *      browser.assert.noBadEvents();
 *    };
 * ```
 *
 * @method noBadEvents
 * @param {string} [msg] Optional log message to display in the output. If missing, one is displayed by default.
 * @api assertions
 */
function NoBadEvents(msg) {
	this.message = msg || 'Testing no bad events have been sent';

	this.expected = () => 0;

	this.pass = value => value === this.expected();

	this.value = json => parseInt(json.bad);

	this.command = callback => {
		const request = require('request');

		request(
			{
				url: 'http://localhost:9090/micro/all',
				json: true,
			},
			(err, res, body) => {
				if (err) {
					console.warn(err);
					return false;
				}
				callback(body);
			}
		);
	};
}

module.exports.assertion = NoBadEvents;
