module.exports = {
	beforeEach: function (browser) {
		browser.resetMicro();
		browser.assert.noBadEvents();
		browser.assert.noOfGoodEvents(0);
		browser.assert.noOfTotalEvents(0);
	},
	tags: ['staging'],
	'Step 1: Number of good events after user SEES the widget is equal to ONE': async function (
		browser
	) {
		const experiment = '#amp-x-canary=1';
		const recommendedPostId = '#recommendations_1';
		const recommendationsListId = '#recommendations';

		await browser.url(
			`https://localnewslab.newspackstaging.com/2020/12/07/rutrum-vel-euismod-proin-vulputate-nulla-aliquam/${experiment}`
		);

		// scroll to recommendation widget
		await browser.waitForElementVisible(recommendationsListId);
		await browser.moveToElement(recommendedPostId, 0, 0);

		await browser.assert.noBadEvents();
		await browser.assert.noOfGoodEvents(1);
		await browser.assert.noOfTotalEvents(1);

		// let articleIds = [];
		// const num = ['one', 'two', 'three', 'four', 'five'];

		// Retrieve the article ids from attributes
		// for (let i = 0; i < num.length; i++) {
		// 	const { value } = await browser.getAttribute('#recommendations', `data-vars-${num[i]}`);
		// 	articleIds.push(parseInt(value));
		// }
		// console.log('The generated article recommendations are: ', articleIds);

		await browser.assert.successfulEvent(
			{
				eventType: 'unstruct',
				schema: 'iglu:com.washingtoncitypaper/recommendation_flow/jsonschema/1-0-0',
				values: {
					step_name: 'widget_seen',
					// article_ids: articleIds,
				},
				contexts: [
					{
						schema: 'iglu:dev.amp.snowplow/amp_id/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:io.localnewslab/model/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
						data: {
							// id: articleIds[0],
						},
					},
					{
						schema: 'iglu:io.localnewslab/experiment/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
						data: {
							// id: articleIds[1],
						},
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
						data: {
							// id: articleIds[2],
						},
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
						data: {
							// id: articleIds[3],
						},
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
						data: {
							// id: articleIds[4],
						},
					},
					{
						schema: 'iglu:dev.amp.snowplow/amp_web_page/jsonschema/1-0-0',
					},
				],
			},
			1,
			'Testing micro received the expected "widget_seen" event with articles, model, and experiment context'
		);
	},
	'Step 2: Number of good events after user CLICKS a RECOMMENDED ARTICLE is equal to TWO':
		async function (browser) {
			const experiment = '#amp-x-canary=1';
			const recommendedPostId = '#recommendations_1';
			const recommendationsListId = '#recommendations';

			await browser.url(
				`https://localnewslab.newspackstaging.com/2020/12/07/rutrum-vel-euismod-proin-vulputate-nulla-aliquam/${experiment}`
			);

			// scroll to recommendation widget
			await browser.waitForElementVisible(recommendationsListId);
			await browser.moveToElement(recommendationsListId, 0, 0);

			// const articleId = await browser.getAttribute(recommendationsListId, 'data-vars-one');

			// Click article
			await browser.click(recommendedPostId, async function (result) {
				await this.assert.equal(true, result.status == 0, 'Article clicked successfully');
			});

			await browser.assert.noBadEvents();
			await browser.verify.noOfGoodEvents(2);
			await browser.assert.noOfTotalEvents(2);

			await browser.assert.successfulEvent(
				{
					eventType: 'unstruct',
					schema: 'iglu:com.washingtoncitypaper/recommendation_flow/jsonschema/1-0-0',
					values: {
						step_name: 'widget_click',
					},
					contexts: [
						{
							schema: 'iglu:dev.amp.snowplow/amp_id/jsonschema/1-0-0',
						},
						{
							schema: 'iglu:io.localnewslab/model/jsonschema/1-0-0',
						},
						{
							schema: 'iglu:io.localnewslab/experiment/jsonschema/1-0-0',
						},
						{
							schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
							// data: {
							// 	id: articleId,
							// },
						},
						{
							schema: 'iglu:io.localnewslab/article_recommendation/jsonschema/1-0-0',
						},
						{
							schema: 'iglu:dev.amp.snowplow/amp_web_page/jsonschema/1-0-0',
						},
					],
				},
				1,
				'Testing micro received the expected "widget_click" event with article_recommendation, article, model and experiment context'
			);

			await browser.assert.noBadEvents();

			// browser.click("newspack-popup button").dismissAlert();
			// .waitForElementNotPresent(".popup-actionform", 2000, false);
			//TODO: fix: navigate past newspack-popups' campaign popups for every test
			//TODO: test: click each article individually using the index property
			// https://nightwatchjs.org/guide/using-nightwatch/finding-and-interacting-with-elements.html
			//TODO: feat: add localtunnel to proxy all micro requests through HTTPS
			//TODO: feat: add custom user-agent to request headers so that localtunnel url verification is automated
			//TODO: step one: navigate to localnewslab.newspackstaging
			//TODO: step two:
		},
};
