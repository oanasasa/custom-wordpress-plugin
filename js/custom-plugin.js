/*
Plugin Name: Custom WP Data and Ajax
File: The JS File
Version: 1.0
Author: Oana Sasaran
*/

jQuery(function () {
  // AJAX call to retrieve WordPress posts
  jQuery.ajax({
    url: customPluginAjax.ajaxurl,
    type: "POST",
    data: {
      action: "custom_plugin_get_posts",
    },
    success: function (response) {
      // Display the list of posts in the frontend
      var postsContainer = jQuery('<div id="custom-plugin-posts"></div>');
      jQuery.each(response, function (index, post) {
        var postItem = jQuery('<article class="custom-plugin-post"></article>');
        postItem.append(jQuery("<h3 class='title'>" + post.title + "</h3>"));
        postItem.append(
          jQuery("<div class='content'>" + post.excerpt + "</div>")
        );
        postsContainer.append(postItem);
      });
      jQuery("body").append(postsContainer);
    },
  });
});
