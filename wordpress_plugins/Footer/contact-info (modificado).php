<?php

class Sydney_Contact_Info extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'sydney_contact_info_widget', 'description' => __( 'Display your contact info', 'sydney') );
        parent::__construct(false, $name = __('Sydney: Contact info', 'sydney'), $widget_ops);
		$this->alt_option_name = 'sydney_contact_info';	
    }
	
	function form($instance) {

		$title    = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$address  = isset( $instance['address'] ) ? esc_html( $instance['address'] ) : '';
		$phone    = isset( $instance['phone'] ) ? esc_html( $instance['phone'] ) : '';
		$email    = isset( $instance['email'] ) ? esc_html( $instance['email'] ) : '';
		$info     = isset( $instance['info'] ) ? esc_html( $instance['info'] ) : '';
	?>

	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'sydney'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

	<p><label for="<?php echo $this->get_field_id( 'address' ); ?>"><?php _e( 'Enter your address', 'sydney' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" type="text" value="<?php echo $address; ?>" size="3" /></p>

	<p><label for="<?php echo $this->get_field_id( 'phone' ); ?>"><?php _e( 'Enter your phone number', 'sydney' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" type="text" value="<?php echo $phone; ?>" size="3" /></p>

	<p><label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e( 'Enter your email address', 'sydney' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php echo $email; ?>" size="3" /></p>	
	
	<p><label for="<?php echo $this->get_field_id( 'info' ); ?>">Información importante</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'info' ); ?>" name="<?php echo $this->get_field_name( 'info' ); ?>" type="text" value="<?php echo $info; ?>" size="3" /></p>	

	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['address'] = strip_tags($new_instance['address']);
		$instance['phone'] = strip_tags($new_instance['phone']);
		$instance['email'] = sanitize_email($new_instance['email']);
		$instance['info'] = strip_tags($new_instance['info']);
	  
		return $instance;
	}
		
	function widget($args, $instance) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		extract($args);

		$title 		= ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$title 		= apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$address   	= isset( $instance['address'] ) ? esc_html( $instance['address'] ) : '';
		$phone   	= isset( $instance['phone'] ) ? esc_html( $instance['phone'] ) : '';
		$email   	= isset( $instance['email'] ) ? antispambot(esc_html( $instance['email'] )) : '';
		$info   	= isset( $instance['info'] ) ? esc_html( $instance['info'] ) : '';

		echo $before_widget;
		
		if ( $title ) echo $before_title . $title . $after_title;
		
		if( ($phone) ) {
			echo '<div class="contact-phone">';
			echo '<span><i class="fa fa-phone"></i></span>' . $phone;
			echo '</div>';
		}
		if( ($email) ) {
			echo '<div class="contact-email">';
			echo '<span><i class="fa fa-envelope"></i></span>' . '<a href="mailto:' . $email . '">' . $email . '</a>';
			echo '</div>';
		}
		if( ($info) ) {
			echo '<div class="contact-info">';
			echo '<span><i class="fa fa-exclamation-triangle"></i></span><b>' . $info . '</b>';
			echo '</div>';
		}		
		if( ($address) ) {
			echo '<div class="contact-address">';
			echo '<span><i class="fa fa-home"></i></span>' . $address;
			echo '</div>';
		}
								
		echo $after_widget;

	}
	
}	