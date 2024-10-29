<?php
/*
Plugin Name: AM Action Quiz
Description: Simple quiz, one question and three answers when the website visitor chooses the right answer action button will show-up to redirect him/her to another page to colect free gift, discount or anything else you offer them.
Author: Ayoub Media
Author URI: http://www.ayoubmedia.com
Version: 1.0
License: GPLv2 or later
*/

//handle ajax calls
add_action( 'wp_ajax_am_aqz_ajax', 'am_action_quiz_do_ajax' );
add_action( 'wp_ajax_nopriv_am_aqz_ajax', 'am_action_quiz_do_ajax' );
if (!function_exists('am_action_quiz_do_ajax')){
	
	function am_action_quiz_do_ajax() {

				include dirname(__FILE__)."/am-action-quiz.ajax.php";
	
				exit();
		
		wp_die();
	}
}


/*
work-round when the admin user logged-in and has no permission for ajax
and wp_ajax_nopriv not working.
*/
if ( !empty( $_REQUEST['action'] ) && $_REQUEST['action'] == "am_aqz_ajax")
am_action_quiz_do_ajax();

if (!is_admin()){
	//load js
	add_action('wp_enqueue_scripts', 'am_action_quiz_js');
	
	function am_action_quiz_js() {
		wp_enqueue_script('jquery');
		wp_localize_script( 'jquery', 'am_aqz_ajax', array(
        'ajaxurl'       => admin_url( 'admin-ajax.php' ))
    );
		wp_enqueue_script('am_action_quiz_script',plugins_url('',__FILE__).'/am-action-quiz.js');
	}
}

class am_action_quiz_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'am_action_quiz', // Base ID
			__('AM Action Quiz', 'am_action_quiz'), // Name
			array( 'description' => __( 'Simple quiz, one question and three answers following by action button.', 'am_action_quiz' ), ) // Args
		);
	}

	function form( $instance ) {
		if ( $instance ) {
			$title = esc_html( $instance['title'] );
			$question = esc_html( $instance['question'] );
			$answer_1 = esc_html( $instance['answer_1'] );
			$answer_2 = esc_html( $instance['answer_2'] );
			$answer_3 = esc_html( $instance['answer_3'] );
			$right_answer = esc_html( $instance['right_answer'] );
			$right_answer_message = esc_textarea( $instance['right_answer_message'] );
			$wrong_answer_message = esc_textarea( $instance['wrong_answer_message'] );	
			$guess_button_text = esc_html( $instance['guess_button_text'] );	
			$action_button_text = esc_html( $instance['action_button_text'] );	
			$action_page_url = esc_url( $instance['action_page_url'] );
			$error_message = esc_textarea( $instance['error_message'] );	
			$cookies_duration = esc_html( $instance['cookies_duration'] );
		} else {
		
			$title = '';
			$question = '';
			$answer_1 = '';
			$answer_2 = '';
			$answer_3 = '';
			$right_answer = 'answer_1';
			$right_answer_message = '';
			$wrong_answer_message = '';	
			$guess_button_text = '';	
			$action_button_text = '';	
			$action_page_url = '';		
			$error_message = '';
			$cookies_duration = 0;	//hours
		
		}
?>
	
    
    	<p>
        <input type="hidden" name="<?php echo $this->get_field_name( '_am_action_quiz_' ); ?>" value="<?php echo wp_create_nonce('am_action_quiz_nonce');?>"/>
        
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><b><?php _e( 'Title:' ); ?></b></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title;?>"/> 
        
        <label for="<?php echo $this->get_field_id( 'question' ); ?>"><b><?php _e( 'Question:' ); ?></b></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'question' ); ?>" name="<?php echo $this->get_field_name( 'question' ); ?>"><?php echo $question;?></textarea> 
        
        <label for="<?php echo $this->get_field_id( 'answer_1' ); ?>"><b><?php _e( 'Answer 1:' ); ?></b></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'answer_1' ); ?>" name="<?php echo $this->get_field_name( 'answer_1' ); ?>" value="<?php echo $answer_1;?>" /> 
        
        <label for="<?php echo $this->get_field_id( 'answer_2' ); ?>"><b><?php _e( 'Answer 2:' ); ?></b></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'answer_2' ); ?>" name="<?php echo $this->get_field_name( 'answer_2' ); ?>" value="<?php echo $answer_2;?>" /> 
        
        <label for="<?php echo $this->get_field_id( 'answer_3' ); ?>"><b><?php _e( 'Answer 3:' ); ?></b></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'answer_3' ); ?>" name="<?php echo $this->get_field_name( 'answer_3' ); ?>" value="<?php echo $answer_3;?>" /> 
        
        
        <label for="<?php echo $this->get_field_id( 'right_answer' ); ?>"><b><?php _e( 'What is the right answer:' ); ?></b></label>
        <select class="widefat" id="<?php echo $this->get_field_id( 'right_answer' ); ?>" name="<?php echo $this->get_field_name( 'right_answer' ); ?>">
		<option value="answer_1">Answer 1</option>
        <option value="answer_2" <?php echo ($right_answer=="answer_2")?"selected":"";?>>Answer 2</option>
        <option value="answer_3" <?php echo ($right_answer=="answer_3")?"selected":"";?>>Answer 3</option>
        </select>
        
        
        <label for="<?php echo $this->get_field_id( 'right_answer_message' ); ?>"><b><?php _e( 'Right answer message:' ); ?></b><br/><?php _e('e.g. Congratulations! You got the right answer.');?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'right_answer_message' ); ?>" name="<?php echo $this->get_field_name( 'right_answer_message' ); ?>" ><?php echo $right_answer_message;?></textarea> 

        <label for="<?php echo $this->get_field_id( 'wrong_answer_message' ); ?>"><b><?php _e( 'Wrong Answer Message:' ); ?></b><br/><?php _e('e.g. Sorry your answer is wrong, good luck next time.');?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'wrong_answer_message' ); ?>" name="<?php echo $this->get_field_name( 'wrong_answer_message' ); ?>" ><?php echo $wrong_answer_message;?></textarea> 

        <label for="<?php echo $this->get_field_id( 'guess_button_text' ); ?>"><b><?php _e( 'Guess button text:' ); ?></b></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'guess_button_text' ); ?>" name="<?php echo $this->get_field_name( 'guess_button_text' ); ?>" value="<?php echo $guess_button_text;?>"/> 
        
        <label for="<?php echo $this->get_field_id( 'action_button_text' ); ?>"><b><?php _e( 'Action button text:' ); ?></b> <?php _e('e.g. Click Here to Claim Your FREE Gift');?><br/><?php _e('This button will show-up when the visitor chooses the right answer.');?></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'action_button_text' ); ?>" name="<?php echo $this->get_field_name( 'action_button_text' ); ?>" value="<?php echo $action_button_text;?>"/>
        
        <label for="<?php echo $this->get_field_id( 'action_page_url' ); ?>"><b><?php _e( 'Action page link:' ); ?></b> <?php _e('The visitors will be redirected to this link when he/she clicks on the action button.');?></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'action_page_url' ); ?>" name="<?php echo $this->get_field_name( 'action_page_url' ); ?>" value="<?php echo $action_page_url;?>"/>  
        
                <label for="<?php echo $this->get_field_id( 'error_message' ); ?>"><b><?php _e( 'Popup error message:' ); ?></b> <?php _e('e.g. Please select an answer.');?></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'error_message' ); ?>" name="<?php echo $this->get_field_name( 'error_message' ); ?>" value="<?php echo $error_message;?>"/>   
        
        <label for="<?php echo $this->get_field_id( 'cookies_duration' ); ?>"><b><?php _e( 'Cookies duration in hours:' ); ?></b> <?php _e('To prevent the visitor from trying again');?></label> 
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'cookies_duration' ); ?>" name="<?php echo $this->get_field_name( 'cookies_duration' ); ?>" value="<?php echo $cookies_duration;?>"/>           
        </p>

<?php 
	}

	function update( $new_instance, $old_instance ) {
		
		if (!wp_verify_nonce( $new_instance['_am_action_quiz_'], 'am_action_quiz_nonce')){return;};
		
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['question'] = sanitize_text_field( $new_instance['question'] );
		$instance['answer_1'] = sanitize_text_field( $new_instance['answer_1'] );
		$instance['answer_2'] = sanitize_text_field( $new_instance['answer_2'] );
		$instance['answer_3'] = sanitize_text_field( $new_instance['answer_3'] );
		$instance['right_answer'] = sanitize_text_field( $new_instance['right_answer'] );
		$instance['right_answer_message'] = sanitize_text_field( $new_instance['right_answer_message'] );
		$instance['wrong_answer_message'] = sanitize_text_field( $new_instance['wrong_answer_message'] );	
		$instance['guess_button_text'] = sanitize_text_field( $new_instance['guess_button_text'] );	
		$instance['action_button_text'] = sanitize_text_field( $new_instance['action_button_text'] );	
		$instance['action_page_url'] = sanitize_text_field( $new_instance['action_page_url'] );	
		$instance['error_message'] = sanitize_text_field( $new_instance['error_message'] );		
		
		//check if isset cookie duration and its an integer
		if (isset($new_instance['cookies_duration']) and trim($new_instance['cookies_duration']) != ""){
			
			$am_action_quiz_cookies_duration = intval($new_instance['cookies_duration']);
			
			if (!$am_action_quiz_cookies_duration)
				$am_action_quiz_cookies_duration = '0';
	
			$instance['cookies_duration'] = $am_action_quiz_cookies_duration;
		}
			
		return $instance;
	}

	function widget( $args, $instance ) {
		?>
        <div class="am_action_quiz">
        <?php
		if ( ! empty( $instance['title'] ) ) {
			$title = esc_html( $instance['title'] );
			echo "<h4>$title</h4>";
		}
		?>
        
        <form onsubmit="return am_action_quiz_get_answer(this)">
        <input type="hidden" name="action" value="am_aqz_ajax"/>
        <input type="hidden" name="_am_action_quiz_" value="<?php echo wp_create_nonce('am_action_quiz_nonce');?>"/>
        <input type="hidden" name="field_id" value="<?php echo str_replace(array('widget-am_action_quiz-','-'),'',$this->get_field_id( '' )); ?>" />
        
		<?php
		if ( ! empty( $instance['error_message'] ) ) {
			$error_message = esc_html( $instance['error_message'] );
			?>
        	<input type="hidden" name="error_message" value="<?php echo $error_message;?>"/>    
            <?php
		}

		if ( ! empty( $instance['question'] ) ) {
			$question = esc_textarea( $instance['question'] );		
			echo "<p>$question</p>";	
		}
		?>
        <p>
        <ul style="margin:0px; padding:0px; list-style:none">
        <?php
		if ( ! empty( $instance['answer_1'] ) ) {
			$answer_1 = esc_html( $instance['answer_1'] );	
			?>
			<li><input type="radio" name="am_action_quiz_answer" id="am_action_quiz_answer_1" value="answer_1" /> <label for="am_action_quiz_answer_1"><?php echo $answer_1;?></label></li>
            <?php
		}

		if ( ! empty( $instance['answer_2'] ) ) {
			$answer_2 = esc_html( $instance['answer_2'] );	
			?>
			<li><input type="radio" name="am_action_quiz_answer" id="am_action_quiz_answer_2" value="answer_2" /> <label for="am_action_quiz_answer_2"><?php echo $answer_2;?></label></li>
            <?php		
		}		
		
		if ( ! empty( $instance['answer_3'] ) ) {
			$answer_3 = esc_html( $instance['answer_3'] );	
			?>
			<li><input type="radio" name="am_action_quiz_answer" id="am_action_quiz_answer_3" value="answer_3" /> <label for="am_action_quiz_answer_3"><?php echo $answer_3;?></label></li>
            <?php		
		}	
		?>
        </ul>
        </p>
        <?php
		if ( ! empty( $instance['guess_button_text'] ) and (!isset($_COOKIE['am_action_quiz']) or $instance['cookies_duration']=='0' or $instance['cookies_duration']=='')) {
			$guess_button_text = esc_html( $instance['guess_button_text'] );	
			?>
			<input type="submit" value="<?php echo esc_html($guess_button_text);?>" class="button"/>
            <?php				
		}	
		
?>
</form>
</div>

<?php
}
}

function am_register_am_action_quiz_Widget() {
	register_widget( 'am_action_quiz_Widget' );
}
add_action( 'widgets_init', 'am_register_am_action_quiz_Widget' );