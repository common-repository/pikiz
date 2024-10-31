<?php

class wp_pikiz_support {

  public static $success = false;

	public static function init()
	{
	    add_action('admin_menu', array(__class__, 'add_pikiz_page'));
      add_action('admin_enqueue_scripts', array(__class__, 'add_support_scripts'));
      add_action( 'wp_ajax_support_form_submit', array(__class__, 'support_form_submit_callback'));
	}

  public static function support_form_submit_callback() {
  	global $wpdb;

  	$name = $_POST['name'];
  	$email = $_POST['email'];
  	$website = $_POST['website'];
  	$message = $_POST['message'];
  	$newsletter = $_POST['newsletter'];

    $to = 'support@getpikiz.com';

    $subject = 'Pikiz WP Plugin Support';
    $headers = "From: ". $name ." <". strip_tags($email) . ">\r\n";
    $headers .= "Reply-To: ". strip_tags($email) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $pMessage = "Website: ".$website;
    $pMessage .= "<br><br>".strip_tags(stripslashes($message));

    require_once( PIKIZ_PLUGIN_DIR . 'includes/Mailchimp.php' );

    if ($newsletter == "true") {

      $mailchimp = new Mailchimp('0ae44061ed9a187049e3763440df008a-us9');

      $result = $mailchimp->call("lists/subscribe", array(
       'id'=>'a49b114d49',
       'email'=>array('email'=>$email),
       'merge_vars'=>array('FLNAME'=>$name),
       'update_existing'=>true,
       'double_optin'=>false,
       'send_welcome'=>false
      ));
    }

    mail($to, $subject, $pMessage, $headers);

  	wp_die(); // this is required to terminate immediately and return a proper response
  }

	public static function add_pikiz_page()
	{
		// This page will be under "Settings"
		add_options_page(
		  __('Pikiz', 'pikiz'),
		  __('Pikiz', 'pikiz'),
		  'manage_options',
		  'pikiz',
		  array( __class__, 'create_admin_page' )
		);
	}

	public static function create_admin_page()
	{
		?>
		<div class="pikiz-admin-page">
		  <h1>Pikiz</h1>
      <div class="notice notice-success is-dismissible hide" data-support-notice>
        <p><?php _e( 'Your message was successfully sent!', 'sample-text-domain' ); ?></p>
      </div>
      <h2>Trouble with Pikiz?</h2>
      <div>
        <p>
          If you have any question, feedback, issue or suggestion regarding
          <a href="https://getpikiz.com" target="_blank">Pikiz</a>, please write to us.
        </p>
        <p>
          Our customer Happiness Team is here to help.
        </p>
        <p>
          Send us an email to <a href="mailto:support@getpikiz.com" target="_blank">support@getpikiz.com</a>
          or use the form below to get in touch with us.
        </p>
      </div>
      <div>
        <form method="POST" action="" class="form-support" data-support-form>
          <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" class="form-control" name="name" id="name" required placeholder="Your name"
              data-support-name>
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" class="form-control" name="email" id="email" required placeholder="you@example.com"
              data-support-email>
          </div>
          <div class="form-group">
            <label for="website">Website *</label>
            <input type="url" class="form-control" name="website" id="website" required data-support-website
              value="<?php echo home_url()?>">
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea name="message" class="form-control" id="message" rows="4" required data-support-message></textarea>
          </div>
          <div class="form-group">
            <input type="checkbox" name="newsletter" id="newsletter" data-support-newsletter>
            <label for="newsletter">Yes, I would like to receive updates from Pikiz.</label>
          </div>
          <div class="form-group text-right">
            <input type="submit" class="button button-primary" value="Send">
          </div>
        </form>
      </div>
		</div>
		<?php
	}

  public static function add_support_scripts()
  {
    wp_register_script(
    'pikiz_support_js',
    PIKIZ_PLUGIN_URL . 'js/pikiz-support.js',
    array('jquery'),
    '1.0',
    true);

    wp_enqueue_script('pikiz_support_js');
    wp_localize_script( 'pikiz_support_js', 'WPPikiz', array(
      'PIKIZ_URL' => PIKIZ_URL
    ));

    wp_enqueue_style(
      'pikiz_support_css',
      PIKIZ_PLUGIN_URL . 'css/main.css'
    );
  }
}
