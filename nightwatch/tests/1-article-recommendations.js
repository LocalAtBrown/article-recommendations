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
		const recommendedPostId = '#recommendations_1';
		const recommendationsListId = '#recommendations';

		await browser.url(`${browser.globals.recUrl}`);

		// scroll to recommendation widget
		await browser.waitForElementVisible(recommendationsListId);
		await browser.moveToElement(recommendedPostId, 0, 0);

		await browser.assert.noBadEvents();
		await browser.assert.noOfGoodEvents(1);
		await browser.assert.noOfTotalEvents(1);

		await browser.assert.successfulEvent(
			{
				eventType: 'unstruct',
				schema: 'iglu:com.washingtoncitypaper/recommendation_flow/jsonschema/1-0-0',
				values: {
					step_name: 'widget_seen',
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
					},
					{
						schema: 'iglu:io.localnewslab/experiment/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
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
			await browser.url(`${browser.globals.recUrl}`);

			const recommendedPostId = '#recommendations_1';
			const recommendationsListId = '#recommendations';

			// scroll to recommendation widget
			await browser.waitForElementVisible(recommendationsListId);
			await browser.moveToElement(recommendationsListId, 0, 0);

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
		},
};
