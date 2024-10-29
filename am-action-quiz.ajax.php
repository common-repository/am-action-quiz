<?php
// If this file is called directly, exit.
if (!defined( 'ABSPATH' )) { exit(); }

/**
 * Check the referrer for the AJAX call.
 */
if(!function_exists('wp_verify_nonce'))
require_once(ABSPATH .'wp-includes/pluggable.php');

if (!wp_verify_nonce( $_REQUEST['_am_action_quiz_'],'am_action_quiz_nonce')){ exit(); };


//get quiz data from saved widget option
$am_action_quiz_data = get_option('widget_am_action_quiz');

//validate field ID
$am_action_quiz_field_id = intval($_POST['field_id']);
	if (! $am_action_quiz_field_id){ exit(); }

//fetch data from saved array using field ID
$am_action_quiz_data = $am_action_quiz_data[$am_action_quiz_field_id];
	if (! $am_action_quiz_data){ exit(); }

//checking if cookie duration is set
if (isset($am_action_quiz_data['cookies_duration']) 
	and trim($am_action_quiz_data['cookies_duration'])!=""
	and trim($am_action_quiz_data['cookies_duration'])!="0"){
		
	$am_action_quiz_cookies_duration = intval($am_action_quiz_data['cookies_duration']);
	
	if ($am_action_quiz_cookies_duration){
		setcookie('am_action_quiz','am_action_quiz',time()+( $am_action_quiz_cookies_duration * 3600 ),'/');
	}
}

//if the answer is correct returns action button, with message
if (isset($_POST['am_action_quiz_answer']) and $am_action_quiz_data['right_answer'] === $_POST['am_action_quiz_answer']){
?>
<p><?php echo esc_textarea($am_action_quiz_data['right_answer_message']);?></p>
<input type="button" onclick = "document.location = '<?php echo esc_url($am_action_quiz_data['action_page_url']);?>'" value="<?php echo esc_html($am_action_quiz_data['action_button_text']);?>" />
<?php
} else {
	//if the answer is wrong
	$am_action_quiz_right_answer = esc_html($am_action_quiz_data['right_answer']);
?>
<p>
<?php echo esc_textarea($am_action_quiz_data['wrong_answer_message']);?> <br/>
<?php echo _e('The right answer is: ');?>
<b><?php echo esc_textarea($am_action_quiz_data[$am_action_quiz_right_answer]);?></b>
</p>
<?php };
exit();