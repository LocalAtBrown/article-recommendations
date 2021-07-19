const eventMatcher = require('../jsm/helpers');

/**
 * Checks that micro receives the expected event with given parameters
 *
 * ```
 *    this.demoTest = function (browser) {
 *       browser.assert.successfulEvent({
                    "eventType": "unstruct",
                    "schema": "iglu:test.example.iglu/cart_action_event/jsonschema/1-0-0",
                    "values": {
                        "type": "add"
                    },
                    "contexts": [{
                        "schema": "iglu:test.example.iglu/product_entity/jsonschema/1-0-0",
                        "data": {
                           "sku": "hh123",
                            "name": "One-size summer hat",
                            "price": 15.5,
                            "quantity": 1
                        }
                       }
                    ]
                });
 *    };
 * ```
 *
 * @method successfulEvent
 * @param {Object} [expected_event] Expected event with given parameters to be sent to micro
 * @param {number} noOfEvents - number of events. If missing, default to 1.
 * @param {string} [msg] Optional log message to display in the output. If missing, one is displayed by default.
 * @api assertions
 */
function SuccessfulEvent(expected_event, noOfEvents = 1, msg) {
	this.message =
		msg || `Testing micro received the expected ${noOfEvents} events of type ${expected_event}`;

	this.expected = () => noOfEvents;

	this.pass = value => value === this.expected();

	this.value = eventsOnMicro => {
		const matchingEvents = eventMatcher.matchEvents(eventsOnMicro, expected_event);
		return matchingEvents.length;
	};

	this.command = callback => {
		const request = require('request');

		request(
			{
				url: 'http://localhost:9090/micro/good',
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

module.exports.assertion = SuccessfulEvent;
