<?php
    require 'lib/db.php';
    require 'lib/functions.php';
    require 'lib/facebook.php';
    $userOnDb = null;
    $facebook = new Facebook(array(
        'appId'  => '167459620119553',
        'secret' => '0eacebe5b661e0f1e14bb3cbac0022b9',
        'cookie' => true,
        'domain'=>'ondansonrafm.com'
    ));

    $user = $facebook->getUser();

    if ($user) {
        try {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = $facebook->api('/me');
        } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
        }
    }

    $permissions= array(
        scope => 'publish_stream,publish_actions',
    );

    // Login or logout url will be needed depending on current user state.
    if ($user) {
        $logoutUrl = $facebook->getLogoutUrl();
        //try to get user from db
        $userOnDb = R::findOne('user','fb_id = :fb_id',array(':fb_id'=>$user_profile['id']));
        if(isset($userOnDb)){

        }else{
            $userOnDb = R::dispense('user');
            $userOnDb->fb_id = $user_profile['id'];
            $userOnDb->red =mt_rand ( 0 , 255 );
            $userOnDb->green =mt_rand ( 0 , 255 );
            $userOnDb->blue =mt_rand ( 0 , 255 );
            R::store($userOnDb);
        }
    } else {
        $statusUrl = $facebook->getLoginStatusUrl();
        $loginUrl = $facebook->getLoginUrl($permissions);
    }


    if($userOnDb and $userOnDb->last_login_week!= date('W')){
        $userOnDb->last_login_week = date('W');
        R::store($userOnDb);
        try {
            $ret_obj = $facebook->api('/me/feed', 'POST',
                array(
                    'link' => 'ondansonrafm.com',
                    'message' => $user_profile['first_name'].' OndanSonraFM dinlemeye başladı!'
                ));
            $userOnDb->lastShared = date('W');
            R::store($userOnDb);
        } catch(FacebookApiException $e) {
        }
    }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>
      ondansonra.fm - Beta
    </title>
    <link href="/css/reset.css" rel="stylesheet" />
    <link href="/css/jquery.mCustomScrollbar.css" rel="stylesheet" />
    <link href="/css/simple-slider.css" rel="stylesheet" />
    <link href="/css/simple-slider-volume.css" rel="stylesheet" />
    <link href="/css/app.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Ubuntu:300,400&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <meta property="og:title" content="OndanSonraFM - Beta"/>
    <meta property="og:description" content="Gün içerisinde yayınımızı dinleyebilir, saat 10'dan sonra canlı yayınımıza eşlik edebilirsin."/>
    <meta property="og:type" content="music.radio_station"/>
    <meta property="og:url" content="http://ondansonrafm.com"/>
    <meta property="og:image" content="http://ondansonrafm.com/img/logo_fb.png"/>
    <script src="/js/jquery.js">
    </script>
  </head>
  <body>
    <div id="eventOfDay">
      <div id="eventBlock">Genel Muhabbet</div>
      <!----><div class='eventPicture' ></div>
      <img class="eventGlow" src="/img/glow.png" />
      <img class="eventGlow" src="/img/glow.png" />
      <h3>Gecenin Konusu</h3>
      <h1>4 Dakikada</h1>
      <div id='eventExp'>
          <p>Bugün muhtemelen AKP'nin sponsorluğunda twitter'da yazılmış bir hashtag.</p>
          <ul>
            <li>4 dakikada ne yapmam?</li>
            <li>4 dakikada aşk?</li>
            <li>4 dakikada hazırlanma sanatı.</li>
            <li>4 dakikada hazırlanma sanatı.</li>
          </ul>
      </div><!---->
    </div>
    <div id="wallWrapper">
      <div id="wall">
      </div>
    </div>
    <div id="bottom">
      <div id="radioPlayer">
        <div id="fixButton">
          <button id="playButton">
            &#xF011;
          </button>
          <span>
          </span>
        </div>
        <div id="volumeSlider">
          <input data-slider="true" data-slider-range="0,1" data-slider-step="0.1" data-slider-theme="volume" id="volumeInput" type="text" value="0.5" />
        </div>
        <div class="hidden" id="radioPlayerObject">
        </div>
      </div>
        <?php if(!$user) {?>
        <img id='fbButton' src='/img/fb-button.png' onclick='window.location.href="<?php echo $loginUrl;?>"'/>
        <a id="twitterButton" href="https://twitter.com/ondansonrafm" class="twitter-follow-button" data-show-count="false" data-lang="tr" data-size="large">Takip et: @ondansonrafm</a>
        <div id="SkypeButton_Call_ondansonrafm_1">
        <?php }else{?>
        <div id='loggedInFb'>
            <a id="twitterButton" href="https://twitter.com/ondansonrafm" class="twitter-follow-button" data-show-count="false" data-lang="tr" data-size="large">Takip et: @ondansonrafm</a>
            <div id="SkypeButton_Call_ondansonrafm_1">
            <div id='loggedInFbInner'>
                <span class='welcomeText'>Merhaba </span>
                <img id='fbPic' src='http://graph.facebook.com/<?php echo $user_profile['id'];?>/picture?width=30&height=30'/>
                <span class='welcomeText'><?php echo $user_profile['first_name'];?></span>
            </div>
            <div id="sendMessage">
                <div id='postButton'>Duvara birşeyler göndermek istermisin?</div>
                <div id="sendMessageInner">
                    <label>Mesajın:</label>
                    <textarea id="messageToSend"></textarea>
                    <button class='sendButton' onclick='sendMessage();'>Gönder</button>
                </div>
            </div>

            <?php ;?>
        </div>
        <?php }?>
    </div>
    <div id="fb-root">
    </div>
  </body>
  <script src="/js/jquery.mCustomScrollbar.min.js">
  </script>
  <script type="text/javascript" src="http://www.skypeassets.com/i/scom/js/skype-uri.js">

  </script>
  <script src="/js/jquery.jplayer.min.js">
  </script>
  <script src="/js/isotope.js">
  </script>
  <script src="/js/simple-slider.min.js">
  </script>
  <script src="/js/app.js">
  </script>
  <script src="/js/linker.js"></script>
</html>