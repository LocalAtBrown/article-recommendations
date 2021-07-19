/**
 * Checks that the number of expected total events are correct
 *
 * ```
 *    this.demoTest = function (browser) {
 *      browser.assert.noOfTotalEvents(2);
 *    };
 * ```
 *
 * @method NoOfTotalEvents
 * @param {number} [expected_value] Number of total events expected to be sent to micro
 * @param {string} [msg] Optional log message to display in the output. If missing, one is displayed by default.
 * @api assertions
 */
function NoOfTotalEvents(expected_value, msg) {
	this.message = msg || `Testing that the total number of events is: ${expected_value}`;

	this.expected = () => expected_value;

	this.pass = value => value === this.expected();

	this.value = json => parseInt(json.total);

	this.command = callback => {
		const request = require('request');

		request(
			{
				url: 'http://localhost:9090/micro/all',
				json: true,
			},
			(err, res, body) => {
				if (err) {
					console.log(err);
					return false;
				}
				callback(body);
			}
		);
	};
}

module.exports.assertion = NoOfTotalEvents;
