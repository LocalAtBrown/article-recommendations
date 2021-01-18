
const { createElement } = wp.element
const { registerBlockType } = wp.blocks
import { __ } from '@wordpress/i18n'; 

registerBlockType("article-recommendations/hello-world", {
  // built-in attributes
  title: "Article Recommendations",
  description: "Create a list of article recommendations",
  // can be wp icons by dash icons from wp or put svg
  icon: "admin-site", 
  // Category the gutenberg block type falls under within the editor
  category: "widgets", 
  // custom attributes
  attributes: {},
  // custom functions
  // built-in functions
  // uses JSX syntax
  edit: function() {
    return <p>Hello Editor Recommendations</p>;
  },

  save: function() {
    return <p>Hello Frontend</p>;
  }
});