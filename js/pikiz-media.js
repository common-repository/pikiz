;
jQuery(function ($) {
  'use strict';

  (function (w, d) {
    if (!w.Pikiz || (w.Pikiz && typeof w.Pikiz.init !== "function") ) {
      var _s = d.createElement("script");
     _s.src = WPPikiz.PIKIZ_URL + "/scripts/embed/pikiz.js";
     _s.addEventListener("load", function () {
       w.Pikiz.init("", {"appUrl": WPPikiz.PIKIZ_URL, "auto": false});
     });
     d.body.appendChild(_s);
    }
  })(window, document);

  var isFeaturedImage = false;

  var media_window = wp.media({
    title: 'Choose an image',
    library: {type: 'image'},
    multiple: false,
    button: {text: 'Choose'}
  });

  media_window.on('select', function(){
    var files = media_window.state().get('selection').toArray();
    var first = files[0].toJSON();

    if (window.Pikiz) {
      var url = WPPikiz.PIKIZ_URL +
        '/images/create?origin=wordpress_plugin' +
        '&img=' + first.url +
        '&apikey=' +
        '&referrer=' + window.location.href;

      window.Pikiz.Dialog.open(url);
    }
  });

  var saveInGallery = function (url, callback) {
    if (isFeaturedImage) {
      $('#pikiz-loader-overlay').css('display', 'block');
    }

    $.post(wp.ajax.settings.url, {
        'action': 'add_image_to_gallery',
        'url': url
      },
      function (response) {
        callback(response);
      }
    );
  };

  window.addEventListener('message', function (e) {

    if (e.origin === WPPikiz.PIKIZ_URL && e.data.action === 'close') {

      saveInGallery(e.data.imgUrl, function (id) {
        if (isFeaturedImage) {
          if (id != 'Nan') {
            wp.media.featuredImage.set(parseInt(id));
          }
          $('#pikiz-loader-overlay').css('display', 'none');
          isFeaturedImage = false;
        } else {
          var html = '<a href="' + e.data.pageUrl +'">' +
            '<img src="' + e.data.imgUrl + '" />' +
          '</a>';
          wp.media.editor.insert(html);
        }
      });
    }
  });

  window.onPikizSetFeaturedImageClick = function () {
    isFeaturedImage = true;
    media_window.open();
  };

  $(document).ready(function () {
    $('#pikiz-insert-media').on('click', function (e) {
      e.preventDefault();
      media_window.open();
    });
  });

});
