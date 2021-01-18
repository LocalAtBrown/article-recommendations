
const { createElement } = wp.element
const { registerBlockType } = wp.blocks
import { __ } from '@wordpress/i18n'; 

registerBlockType("article-recommendations/hello-world", {
  title: "Hello World",
  description: "Just another Hello World block",
  icon: "admin-site",
  category: "common",

  edit: function() {
    return <p>Hello Editor Recommendations</p>;
  },

  save: function() {
    return <p>Hello Frontend</p>;
  }
});