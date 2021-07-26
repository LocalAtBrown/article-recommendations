/* eslint-disable object-shorthand */
module.exports = {
	beforeEach: function (browser) {
		browser.resetMicro();
		browser.assert.noBadEvents();
		browser.assert.noOfGoodEvents(0);
		browser.assert.noOfTotalEvents(0);
	},
	tags: ['staging'],
	'Step 1: Number of good events after user SEES the widget is equal to ONE': function (browser) {
		browser.url(
			'https://localnewslab.newspackstaging.com/2020/12/07/rutrum-vel-euismod-proin-vulputate-nulla-aliquam/#amp-x-canary=0'
		);
		const recentPostId = '#recentposts_1';
		const recentPostsId = '#recentposts';

		// scroll to recommendation widget
		browser.waitForElementVisible(recentPostsId).moveToElement(recentPostId, 0, 0);

		browser.assert.noBadEvents();
		browser.assert.noOfGoodEvents(1);
		browser.assert.noOfTotalEvents(1);

		browser.assert.successfulEvent(
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
						data: {
							type: 'recent_posts',
						},
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
			'Testing micro received the expected "widget_seen" event with context'
		);
	},
	'Step 2: Number of good events after user CLICKS a RECENT POST is equal to TWO': async function (
		browser
	) {
		browser.url(
			'https://localnewslab.newspackstaging.com/2020/12/07/rutrum-vel-euismod-proin-vulputate-nulla-aliquam/#amp-x-canary=0'
		);

		const recentPostId = '#recentposts_1';
		const recentPostsId = '#recentposts';

		// scroll to recommendation widget
		await browser.waitForElementVisible(recentPostsId);
		await browser.moveToElement(recentPostId, 0, 0);

		await browser.assert.noOfTotalEvents(1);
		await browser.assert.noOfGoodEvents(1);
		await browser.assert.noBadEvents();

		await browser.assert.successfulEvent(
			{
				eventType: 'unstruct',
				schema: 'iglu:com.washingtoncitypaper/recommendation_flow/jsonschema/1-0-0',
				values: {
					step_name: 'widget_seen',
					//article_ids: articleIds,
				},
				contexts: [
					{
						schema: 'iglu:dev.amp.snowplow/amp_id/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:io.localnewslab/model/jsonschema/1-0-0',
						data: {
							type: 'recent_posts',
						},
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
			'Testing micro received the expected "widget_seen" event with recent_post, articles, and experiment context'
		);

		await browser.click(recentPostId, async function (result) {
			await this.assert.equal(true, result.status === 0, 'Article clicked successfully');
		});

		const { value } = await browser.getAttribute(recentPostsId, 'data-vars-one');
		const articleId = parseInt(value);

		await browser.assert.noBadEvents();
		await browser.assert.noOfGoodEvents(2);
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
						data: {
							type: 'recent_posts',
						},
					},
					{
						schema: 'iglu:io.localnewslab/experiment/jsonschema/1-0-0',
					},
					{
						schema: 'iglu:com.washingtoncitypaper/article/jsonschema/1-0-0',
						data: {
							id: articleId,
						},
					},
					{
						schema: 'iglu:io.localnewslab/article_recommendation/jsonschema/1-0-0',
						data: {
							position: 1,
							articleId,
						},
					},
					{
						schema: 'iglu:dev.amp.snowplow/amp_web_page/jsonschema/1-0-0',
					},
				],
			},
			1,
			'Testing micro received the expected "widget_click" event with recent_post, articles, and experiment context'
		);
		// Event with property test - context, event type, and schema properties
		// Race condition test - ensure that event x is always sent to micro before event y
	},
	// "Recent Posts widget should be loaded": function (browser) {},
	// "Check the order of events; widget seen always before widget click": function (browser) {},
	// "Number of good events after SELECTION of post is equal to one": function (browser) {},
	// "Number of good events after BROWSE to landing page is equal to one": function (browser) {},
};
