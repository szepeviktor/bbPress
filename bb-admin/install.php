<?php
// Modify error reporting levels
error_reporting(E_ALL ^ E_NOTICE);

// Let everyone know we are installing
define('BB_INSTALLING', true);

// Load bbPress
require_once('../bb-load.php');

// Instantiate the install class
require_once(BB_PATH . 'bb-admin/class-install.php');
$bb_install = new BB_Install(__FILE__);

// Include some neccesary functions if not already there
if ($bb_install->load_includes) {
	require_once(BACKPRESS_PATH . 'functions.plugin-api.php');
	require_once(BB_PATH . BB_INC . 'wp-functions.php');
	require_once(BB_PATH . BB_INC . 'functions.php');
	require_once(BB_PATH . BB_INC . 'kses.php');
	require_once(BB_PATH . BB_INC . 'l10n.php');
}

$bb_install->get_languages();
$bb_install->set_language();

if ($bb_install->language) {
	$locale = $bb_install->language;
	unset($l10n['default']);
	if ($bb_install->load_includes) {
		require_once( BACKPRESS_PATH . 'class.gettext-reader.php' );
		require_once( BACKPRESS_PATH . 'class.streamreader.php' );
	}
}

if ($bb_install->load_includes) {
	require_once( BB_PATH . BB_INC . 'template-functions.php');
}

// Load the default text localization domain.
load_default_textdomain();

// Pull in locale data after loading text domain.
require_once(BB_PATH . BB_INC . 'locale.php');
$bb_locale = new BB_Locale();

$bb_install->prepare_strings();
$bb_install->check_prerequisites();
$bb_install->check_configs();

if ($bb_install->step > 0) {
	$bb_install->set_step();
	$bb_install->prepare_data();
	$bb_install->process_form();
}

$bb_install->header();
?>
		<script type="text/javascript" charset="utf-8">
			function toggleBlock(toggleObj, target) {
				var targetObj = document.getElementById(target);
				if (toggleObj.checked) {
					targetObj.style.display = 'block';
				} else {
					targetObj.style.display = 'none';
				}
			}
			function toggleValue(toggleObj, target, offValue, onValue) {
				var targetObj = document.getElementById(target);
				if (toggleObj.checked) {
					targetObj.value = onValue;
				} else {
					targetObj.value = offValue;
				}
			}
		</script>
<?php
switch ($bb_install->step) {
	case -1:
	case 0:
		$bb_install->messages();
		$bb_install->intro();
		break;
	
	default:
		$bb_install->sanitize_form_data();
		
		$bb_install->step_header(1);
		
		if ($bb_install->step === 1) {
			
			switch($bb_install->step_status[1]) {
				case 'incomplete':
?>
				<form action="install.php?step=1" method="post">
					<fieldset>
<?php
					$bb_install->input_text('bbdb_name');
					$bb_install->input_text('bbdb_user');
					$bb_install->input_text('bbdb_password', 'password');
					$bb_install->select_language();
					$bb_install->input_toggle('toggle_1');
?>
						<div class="toggle" id="toggle_1_target" style="display:<?php echo $bb_install->data[$bb_install->step]['form']['toggle_1']['display']; ?>;">
<?php
					$bb_install->input_text('bbdb_host');
					$bb_install->input_text('bbdb_charset');
					$bb_install->input_text('bbdb_collate');
					$bb_install->input_text('bb_secret_key');
					$bb_install->input_text('bb_table_prefix', 'ltr');
?>
						</div>
					</fieldset>
<?php
					$bb_install->input_buttons('forward_1_0');
?>
				</form>
<?php
					break;
				
				case 'manual':
?>
				<form action="install.php?step=1" method="post">
<?php
					$bb_install->hidden_step_inputs();
?>
					<fieldset>
<?php
					$bb_install->textarea('config', 'ltr');
?>
					</fieldset>
<?php
					$bb_install->input_buttons('forward_1_1', 'back_1_1');
?>
				</form>
<?php
					break;
				
				case 'complete':
?>
				<form action="install.php?step=2" method="post">
<?php
					$bb_install->input_buttons('forward_1_2');
?>
				</form>
<?php
					break;
			}
		}
		
		$bb_install->step_footer();
		
		$bb_install->step_header(2);
		
		if ($bb_install->step === 2) {
			
			switch ($bb_install->step_status[2]) {
				case 'incomplete':
?>
				<form action="install.php?step=2" method="post">
					<fieldset>
<?php
					bb_nonce_field('bbpress-installer');
					$bb_install->input_toggle('toggle_2_0');
?>
					</fieldset>
					<div class="toggle" id="toggle_2_0_target" style="display:<?php echo $bb_install->data[$bb_install->step]['form']['toggle_2_0']['display']; ?>;">
						<fieldset>
<?php
					$bb_install->input_toggle('toggle_2_1');
?>
						</fieldset>
						<div class="toggle" id="toggle_2_1_target" style="display:<?php echo $bb_install->data[$bb_install->step]['form']['toggle_2_1']['display']; ?>;">
							<fieldset>
								<legend><?php _e('Cookies'); ?></legend>
								<p><?php _e('Integrating cookies allows you and your users to login to either your bbPress or your WordPress site and be automatically logged into both.'); ?></p>
								<p><?php _e('You may need to make changes to your WordPress configuration once installation is complete. See the "WordPress Integration" section of the bbPress administration area when you are done.'); ?></p>
<?php
					$bb_install->input_text('wp_siteurl', 'ltr');
					$bb_install->input_text('wp_home', 'ltr');
					$bb_install->input_text('wp_secret_key');
					$bb_install->input_text('wp_secret');
?>
							</fieldset>
						</div>
						<fieldset>
<?php
					$bb_install->input_toggle('toggle_2_2');
?>
						</fieldset>
						<div class="toggle" id="toggle_2_2_target" style="display:<?php echo $bb_install->data[$bb_install->step]['form']['toggle_2_2']['display']; ?>;">
							<fieldset>
								<legend><?php _e('User database'); ?></legend>
								<p><?php _e('Integrating your WordPress database user tables allows you to store user data in one location, instead of having separate user data for both bbPress and WordPress.'); ?></p>
<?php
					$bb_install->input_text('wp_table_prefix', 'ltr');
					$bb_install->input_toggle('toggle_2_3');
?>
							</fieldset>
							<div class="toggle" id="toggle_2_3_target" style="display:<?php echo $bb_install->data[$bb_install->step]['form']['toggle_2_3']['display']; ?>;">
								<fieldset>
									<legend><?php _e('Separate user database settings'); ?></legend>
									<p><?php _e('Most of the time these settings are <em>not</em> required. Look before you leap!'); ?></p>
									<p><?php _e('All settings except for the character set must be specified.'); ?></p>
<?php
					$bb_install->input_text('user_bbdb_name');
					$bb_install->input_text('user_bbdb_user');
					$bb_install->input_text('user_bbdb_password', 'password');
					$bb_install->input_text('user_bbdb_host');
					$bb_install->input_text('user_bbdb_charset');
?>
								</fieldset>
								<fieldset>
									<legend><?php _e('Custom user tables'); ?></legend>
									<p><?php _e('Only set these options if your integrated user tables do not fit the usual mould of <em>wp_user</em> and <em>wp_usermeta</em>.'); ?></p>
<?php
					$bb_install->input_text('custom_user_table');
					$bb_install->input_text('custom_user_meta_table');
?>
								</fieldset>
							</div>
						</div>
					</div>
<?php
					$bb_install->input_buttons('forward_2_0');
?>
				</form>
				<script type="text/javascript" charset="utf-8">
					function updateWordPressOptionURL () {
						var siteURLInputValue = document.getElementById('wp_siteurl').value;
						var outputAnchor = document.getElementById('getSecretOption');
						if (siteURLInputValue) {
							if (siteURLInputValue.substr(-1,1) != '/') {
								siteURLInputValue += '/';
							}
							outputAnchor.href = siteURLInputValue + 'wp-admin/options.php';
						} else {
							outputAnchor.href = '';
						}
					}
					var siteURLInput = document.getElementById('wp_siteurl');
					if (siteURLInput.value) {
						updateWordPressOptionURL();
					}
					siteURLInput.onkeyup = updateWordPressOptionURL;
					siteURLInput.onblur = updateWordPressOptionURL;
					siteURLInput.onclick = updateWordPressOptionURL;
					siteURLInput.onchange = updateWordPressOptionURL;
				</script>
<?php
					break;
				
				case 'complete':
?>
				<form action="install.php?step=3" method="post">
					<fieldset>
<?php
					bb_nonce_field('bbpress-installer');
?>
					</fieldset>
<?php
					$bb_install->hidden_step_inputs();
					$bb_install->input_buttons('forward_2_1', 'back_2_1');
?>
				</form>
<?php
					break;
			}
		}
		
		$bb_install->step_footer();
		
		$bb_install->step_header(3);
		
		if ($bb_install->step === 3) {
			
			switch($bb_install->step_status[3]) {
				case 'incomplete':
?>
				<form action="install.php?step=3" method="post">
					<fieldset>
<?php
					bb_nonce_field('bbpress-installer');
?>
					</fieldset>
<?php
					$bb_install->hidden_step_inputs(2);
?>
					<fieldset>
<?php
					$bb_install->input_text('name');
					$bb_install->input_text('uri', 'ltr');
?>
					</fieldset>
					<fieldset>
						<legend><?php _e('"Key master" account'); ?></legend>
<?php
					if ($bb_install->step_status[2] == 'complete' && $bb_install->populate_keymaster_user_login_from_user_tables()) {
						echo $bb_install->strings[3]['scripts']['changeKeymasterEmail'];
						$bb_install->select('keymaster_user_login');
						$bb_install->input_hidden('keymaster_user_email');
					} else {
						$bb_install->input_text('keymaster_user_login');
						$bb_install->input_text('keymaster_user_email', 'ltr');
					}
					$bb_install->input_hidden('keymaster_user_type');
?>
					</fieldset>
<?php
					if (!$bb_install->database_tables_are_installed()) {
?>
					<fieldset>
						<legend><?php _e('First forum'); ?></legend>
<?php
						$bb_install->input_text('forum_name');
?>
					</fieldset>
<?php
					}
					
					$bb_install->input_buttons('forward_3_0');
?>
				</form>
<?php
					break;
				
				case 'complete':
?>
				<form action="install.php?step=4" method="post">
					<fieldset>
<?php
					bb_nonce_field('bbpress-installer');
?>
					</fieldset>
<?php
					$bb_install->hidden_step_inputs(2);
					$bb_install->hidden_step_inputs(); // The current step (3) is assumed here
					$bb_install->input_buttons('forward_3_1', 'back_3_1');
?>
				</form>
<?php
					break;
			}
		}
		
		$bb_install->step_footer();
		
		if ($bb_install->step === 4) {
		
			$bb_install->step_header(4);
			
			if ($bb_install->step_status[4] == 'complete') {
?>
				<p><?php _e('You can now log in with the following details:'); ?></p>
				<dl>
					<dt><?php _e('Username:'); ?></dt>
					<dd><code><?php echo $bb_install->data[3]['form']['keymaster_user_login']['value']; ?></code></dd>
					<dt><?php _e('Password:'); ?></dt>
					<dd><code><?php echo $bb_install->data[4]['form']['keymaster_user_password']['value']; ?></code></dd>
					<dt><?php _e('Site address:'); ?></dt>
					<dd dir="ltr"><a href="<?php bb_option( 'uri' ); ?>"><?php bb_option( 'uri' ); ?></a></dd>
				</dl>
<?php
				if ($bb_install->data[3]['form']['keymaster_user_type']['value'] == 'bbPress') {
?>
				<p><?php _e('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you. If you lose it, you will have to delete the tables from the database yourself, and re-install bbPress.'); ?></p>
<?php
				}
			}
?>
				<form action="<?php bb_option( 'uri' ); ?>">
					<fieldset>
<?php
			$bb_install->input_toggle('toggle_4');
?>
						<div class="toggle" id="toggle_4_target" style="display:none;">
<?php
			if ($bb_install->data[4]['form']['error_log']['value']) {
				$bb_install->textarea('error_log');
			}
			$bb_install->textarea('installation_log');
?>
						</div>
					</fieldset>
				</form>
<?php
			$bb_install->step_footer();
			
		} else {
?>
			<div id="step4" class="closed"></div>
<?php
		}
		
		break;
}
$bb_install->footer();
?>