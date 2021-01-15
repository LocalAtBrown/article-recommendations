
const { createElement } = wp.element
const { registerBlockType } = wp.blocks
import { __ } from '@wordpress/i18n'; 

registerBlockType("wp-js-plugin-starter/hello-world", {
  title: "Hello World",
  description: "Just another Hello World block",
  icon: "admin-site",
  category: "common",

  edit: function() {
    return <p>Hello Editor</p>;
  },

  save: function() {
    return <p>Hello Frontend</p>;
  }
});