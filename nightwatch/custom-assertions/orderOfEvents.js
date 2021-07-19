const eventMatcher = require('../jsm/helpers');

/**
 * Check that events are sent to micro in the correct order
 *
 * ...
 *    this.demoTest = function (order) {
 *      browser.assert.orderOfEvents(events_list);
 *    };
 *
 * @method orderOfEvents
 * @param {Array} [events]  Events in the order we expect to see on micro
 * @param {string} [msg] Optional log message to display in the output. If missing, one is displayed by default.
 * @api assertions
 */
function OrderOfEvents(events, msg) {
	this.message = msg || 'Testing that events arrive to micro in the correct order';

	this.expected = () => true;

	this.pass = flagOrder => flagOrder === this.expected();

	this.value = goodEvents => eventMatcher.inOrder(goodEvents, events);

	this.command = callback => {
		const request = require('request');

		request(
			{
				url: 'http://localhost:9090/micro/good',
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

module.exports.assertion = OrderOfEvents;
