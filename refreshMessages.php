<?php
    require 'lib/db.php';
    session_start();

    if(!isset($_GET["last"])){
        $lastPostId = 1;
    }else{
        $lastPostId = $_GET["last"];
    }
    //$lastPostId = 1;
    $wallPosts = R::findAll('post' ,'id > :tempid ORDER BY id DESC LIMIT 50',array( ':tempid'=>$lastPostId));
    $lastPost = array_shift(array_values($wallPosts));
    if(isset($lastPost)) {
        echo array_shift(array_values($wallPosts))->id;
    }else{
        echo $lastPostId.'|||';
        die();
    }

?>
|||
<?php foreach ($wallPosts as $wallpost) { ?>
    <?php $user =  R::findOne('user','fb_id = :fb_id',array(':fb_id'=>$wallpost['user_id'])); ?>
    <div class="wallItem" style='border-color: rgba(<?php echo $user->red; ?>,<?php echo $user->green; ?>,<?php echo $user->blue; ?>,1);'>
        <h6 class="wallItemHeader" ><img src='<?php echo ("http://graph.facebook.com/".$wallpost->user_id."/picture?width=25&height=25"); ?>' /><?php echo($wallpost->user_name);?> : </h6>
        <p class="wallItemContent"><?php echo($wallpost->message);?></p>
    </div>
<?php }?>