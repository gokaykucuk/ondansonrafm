<?php
    require 'lib/db.php';
    require 'lib/functions.php';
    require 'lib/facebook.php';

    $facebook = new Facebook(array(
        'appId'  => '167459620119553',
        'secret' => '0eacebe5b661e0f1e14bb3cbac0022b9',
    ));

    $user = $facebook->getUser();


    $lastPost = R::findLast('post','fb_id = :uid',array('uid'=>$user));
    if ((time() - $lastPost->timestamp) <= 30){
        die();
    }
    $post = R::dispense('post');

    if ($user) {
        try {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = $facebook->api('/me');
            $post->user_name = $user_profile['first_name'];
            $post->user_id = $user_profile['id'];
            $post->message = $_POST['message'];
            $post->timestamp = time();
            R::store($post);
        } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
        }
    }
?>