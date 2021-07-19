/**
 * Checks that the number of expected good events are sent
 *
 * ```
 *    this.demoTest = function (browser) {
 *      browser.assert.noOfGoodEvents(1);
 *    };
 * ```
 *
 * @method NoOfGoodEvents
 * @param {number} [expected_value] Number of good events expected to be sent to micro
 * @param {string} [msg] Optional log message to display in the output. If missing, one is displayed by default.
 * @api assertions
 */
function NoOfGoodEvents(expected_value, msg) {
	this.message = msg || 'Testing number of good events is: ' + expected_value;

	this.expected = () => {
		return expected_value;
	};

	this.pass = value => {
		return value === this.expected();
	};

	this.value = json => {
		return parseInt(json.good);
	};

	this.command = callback => {
		const request = require('request');

		request(
			{
				url: 'http://localhost:9090/micro/all',
				json: true,
			},
			(err, res, body) => {
				if (err) {
					// eslint-disable-next-line no-console
					console.log(err);
					return false;
				}
				callback(body);
			}
		);
	};
}

export const assertion = NoOfGoodEvents;
