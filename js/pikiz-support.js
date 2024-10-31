;
jQuery(function ($) {
  'use strict';

  var $supportForm = $('[data-support-form]');
  var $supportName = $('[data-support-name]');
  var $supportEmail = $('[data-support-email]');
  var $supportWebsite = $('[data-support-website]');
  var $supportMessage = $('[data-support-message]');
  var $supportNewsletter = $('[data-support-newsletter]');
  var $supportNotice = $('[data-support-notice]');

  $supportForm.on('submit', function (e) {
    e.preventDefault();
    $supportForm.find(":input").prop("disabled", true);
    var data = {
			'action': 'support_form_submit',
			'name': $supportName.val(),
			'email': $supportEmail.val(),
			'website': $supportWebsite.val(),
			'message': $supportMessage.val(),
			'newsletter': $supportNewsletter.is(':checked')
		};

		jQuery.post(ajaxurl, data, function(response) {
      $supportNotice.removeClass('hide');
      $supportForm.find(":input").prop("disabled", false);
      $supportForm[0].reset();
		});
  });

});
