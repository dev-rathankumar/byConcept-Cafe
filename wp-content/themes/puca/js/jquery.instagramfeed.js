'use strict';

/*!
 * jquery.instagramFeed
 *
 * @version 1.1.3
 *
 * @author Javier Sanahuja Liebana <bannss1@gmail.com>
 * @contributor csanahuja <csanahuja@gmail.com>
 *
 * https://github.com/jsanahuja/jquery.instagramFeed
 *
 */
(function ($) {
  var defaults = {
    'host': "https://www.instagram.com/",
    'username': '',
    'container': '',
    'private': puca_settings.instagram_private,
    'display_gallery': true,
    'get_raw_json': false,
    'callback': null,
    'items': 8,
    'items_per_row': 4,
    'image_size': 'original',
    'time_ago': true,
    'like': true,
    'comment': true
  };

  $.instagramFeed = function (options) {
    options = $.fn.extend({}, defaults, options);

    if (options.username == "") {
      return;
    }

    if (!options.get_raw_json && options.container == "") {
      return;
    }

    if (options.get_raw_json && options.callback == null) {
      return;
    }

    $.get(options.host + options.username, function (data) {
      data = data.split("window._sharedData = ");
      data = data[1].split("<\/script>");
      data = data[0];
      data = data.substr(0, data.length - 1);
      data = JSON.parse(data);
      data = data.entry_data.ProfilePage[0].graphql.user;

      if (options.get_raw_json) {
        options.callback(JSON.stringify({
          id: data.id,
          username: data.username,
          full_name: data.full_name,
          is_private: data.is_private,
          is_verified: data.is_verified,
          biography: data.biography,
          followed_by: data.edge_followed_by.count,
          following: data.edge_follow.count,
          images: data.edge_owner_to_timeline_media.edges,
          igtv: data.edge_felix_video_timeline.edges
        }));
        return;
      }

      var styles = {
        'profile_container': "",
        'profile_image': "",
        'profile_name': "",
        'profile_biography': "",
        'gallery_image': ""
      };
      var html = "";
      var image_index = 4;

      switch (options.image_size) {
        case 'thumbnail':
          image_index = 0;
          break;

        case 'small':
          image_index = 2;
          break;

        case 'large':
          image_index = 3;
          break;

        default:
          image_index = 4;
          break;
      }

      if (options.display_gallery) {
        if (data.is_private) {
          html += "<p class='instagram_private'><strong>" + options.private + "</strong></p>";
        } else {
          var imgs = data.edge_owner_to_timeline_media.edges,
              max = imgs.length > options.items ? options.items : imgs.length;

          for (var i = 0; i < max; i++) {
            let url = "https://www.instagram.com/p/" + imgs[i].node.shortcode,
                image = imgs[i].node.thumbnail_resources[image_index].src,
                liked = imgs[i].node.edge_liked_by.count,
                comment = imgs[i].node.edge_media_to_comment.count,
                type_resource = "image",
                time = imgs[i].node.taken_at_timestamp,
                time_ago = $.timeago(new Date(time * 1000));

            switch (imgs[i].node.__typename) {
              case "GraphSidecar":
                type_resource = "sidecar";
                break;

              case "GraphVideo":
                type_resource = "video";
                image = imgs[i].node.thumbnail_src;
                break;

              default:
                type_resource = "image";
            }

            html += "<div class='item'><div class='instagram-item-inner'><a href='" + url + "' class='instagram-" + type_resource + "' rel='noopener' target='_blank'>";

            if (options.like || options.comment) {
              html += "<span class='group-items'>";

              if (options.like) {
                html += "<span class='likes'><i class='icon-heart'></i>" + liked + "</span>";
              }

              if (options.comment) {
                html += "<span class='comments'><i class='icon-bubbles icons'></i>" + comment + "</span>";
              }

              html += "</span>";
            }

            if (options.time_ago) {
              html += "<span class='time elapsed-time'>" + time_ago + "</span>";
            }

            html += "<img src='" + image + "' alt='" + options.username + " instagram image " + i + "'" + styles.gallery_image + " />";
            html += "</a></div></div>";
          }
        }
      }

      $(options.container).html(html);
      $(document.body).trigger('puca_instagramfeed_slick');
    });
  };
})(jQuery);

class InstagramFeed {
  _initInstagramFeed() {
    var _this = this;

    $(".instagram-feed:visible").each(function () {
      let _this2 = $(this);

      var config = _this._getInstagramConfigOption(this);

      $.instagramFeed({
        'username': config.username,
        'container': config.container,
        'display_gallery': true,
        'get_raw_json': false,
        'callback': null,
        'items': config.items,
        'items_per_row': 4,
        'image_size': config.image_size,
        'time_ago': config.time_ago,
        'like': config.like,
        'comment': config.comment
      });
    });
  }

  _getInstagramConfigOption($el) {
    var _config = {};
    _config.username = $($el).data('username');
    _config.items = $($el).data('number');
    _config.image_size = $($el).data('image_size');
    _config.container = $($el).data('id');
    _config.time_ago = $($el).data('time_ago');
    _config.like = $($el).data('like');
    _config.comment = $($el).data('comment');
    return _config;
  }

}

jQuery(window).on('load', function ($) {
  var instagramfeed = new InstagramFeed();

  instagramfeed._initInstagramFeed();
});

var CustomInstagramfeed = function ($scope, $) {
  var instagramfeed = new InstagramFeed();

  instagramfeed._initInstagramFeed();
};

jQuery(window).on('elementor/frontend/init', function () {
  if (jQuery.isArray(puca_settings.elements_ready.instagram)) {
    $.each(puca_settings.elements_ready.instagram, function (index, value) {
      elementorFrontend.hooks.addAction('frontend/element_ready/tbay-' + value + '.default', CustomInstagramfeed);
    });
  }
});
