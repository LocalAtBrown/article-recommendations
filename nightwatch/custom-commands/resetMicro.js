/**
Clears Snowplow Micro's cache - to be used before each test
**/
const EventEmitter = require('events');

class ResetMicro extends EventEmitter {
	async command() {
		const request = require('request');
		// const localtunnel = require('localtunnel');
		// const tunnel = await localtunnel({
		// 	port: 9090,
		// 	subdomain: 'lnlmicro',
		// });

		// const options = {
		// 	url: `${tunnel.url}/micro/reset`,
		// 	headers: {
		// 		'User-Agent': 'request',
		// 		'Bypass-Tunnel-Reminder': 'true',
		// 	},
		// };
		request('http://localhost:9090/micro/reset', (err, res, body) => {
			if (err) {
				console.log(err);
				throw 'Unable to reset micro';
			}
		});
		this.emit('complete');
		return this;
	}
}

module.exports = ResetMicro;
