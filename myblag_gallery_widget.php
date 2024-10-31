<?php

include ('XML/RPC.php');

function widget_gallery_init() {

    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return; 

    function widget_gallery($args) {

        extract($args);

        $options = get_option('widget_gallery');
        $title = empty($options['title']) ? 'Fotoalben' : $options['title'];
	$username = empty($options['username']) ? 'dummy' : $options['username'];

        $client = new XML_RPC_Client("/services/xmlrpc", "myblag.de", 80);
	$value = new XML_RPC_Value($username, "string");
	$msg = new XML_RPC_Message("gals.get", array($value));
	$response = $client->send($msg);
	$value = $response->value();

        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo $value->scalarval();
        echo $after_widget;
    }

    function widget_gallery_control() {

        $options = get_option('widget_gallery');

        if ( $_POST['album-submit'] ) {
            $newoptions['title'] = strip_tags(stripslashes($_POST['title']));
	    $newoptions['username'] = strip_tags(stripslashes($_POST['username']));
        

            if ( $options != $newoptions ) {
                $options = $newoptions;
                update_option('widget_gallery', $options);
            }
	}
        $title = $options['title'];
	$username = $options['username'];
?>
        <div>
        <label for="title" style="line-height:35px;display:block;">Title: <input type="text" id="title" name="title" value="<?php echo $title; ?>" /></label>
	<label for="username" style="line-height:35px;display:block;">MyBlag Username: <input type="text" id="username" name="username" value="<?php echo $username; ?>" /></label>
	<input type="hidden" name="album-submit" id="album-submit" value="1" />
        </div>
    <?php
    }

    register_sidebar_widget('MyBlag Gallery', 'widget_gallery');

    register_widget_control('MyBlag Gallery', 'widget_gallery_control');
}

add_action('plugins_loaded', 'widget_gallery_init');
?>
