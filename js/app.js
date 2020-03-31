//Google Analytics
var lastMessage;
(function($){
    $(window).load(function(){
        $("#wallWrapper").mCustomScrollbar();
    });
})(jQuery);

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-44910726-1', 'ondansonrafm.com');
ga('send', 'pageview');



//jPlayer radio setup

$(document).ready(function(){

    var stream = {
            title: "ondansonra.fm",
            mp3: "http://listen.ondansonrafm.com:8000/airtime_128"
        },
        ready = false;

    $("#radioPlayerObject").jPlayer({
        ready: function (event) {
            ready = true;
            $(this).jPlayer("setMedia", stream);
            $(this).jPlayer('volume',0.5);
            $(this).jPlayer("play");
        },
        pause: function() {
            $(this).jPlayer("clearMedia");
        },
        error: function(event) {
            if(ready && event.jPlayer.error.type === $.jPlayer.error.URL_NOT_SET) {
                // Setup the media stream again and play it.
                $(this).jPlayer("setMedia", stream).jPlayer("play");
            }
        },
        swfPath: "http://www.jplayer.org/latest/js/Jplayer.swf",
        supplied: "mp3",
        preload: "none",
        wmode: "window",
        keyEnabled: true,
        solution: "html,flash"
    });
});

//Radio button switch
$(document).ready(function(){
    $('#playButton').addClass('on');
    $('#playButton').on('click', function(){
        if($(this).hasClass('on')){
            toggleRadio('off');
        }else{
            toggleRadio('on');
        }
        $(this).toggleClass('on');
    });
});

function toggleRadio(param){
    if(param === 'on'){
        $('#radioPlayerObject').jPlayer('play');
    }else{
        $('#radioPlayerObject').jPlayer('pause');
    }
}

$("#volumeInput").bind("slider:changed", function (event, data) {
    $('#radioPlayerObject').jPlayer('volume',data.value);
});


isotopeBuilt = false;

function sendMessage(){
    $.post( "newMessage.php", { message: $('#messageToSend').val()});
    $('#messageToSend').val("");
    startTimer();
}
setInterval(function(){refreshMessages();},5000);

lastMessage = 1;

function refreshMessages(){
    $.get( "refreshMessages.php", { last : lastMessage},function(data){
        dataArray = data.split('|||');
        lastMessage = dataArray[0];
        if(!(dataArray[1]=== "")){
            if(isotopeBuilt){
                $('#wall').prepend(dataArray[1]);
                setTimeout(function(){redrawIsotope();},100);
            }else{
                $('#wall').prepend(dataArray[1]);
                setTimeout(function(){buildIsotope();},300);
            }
        }
        reLink();
        $("#wallWrapper").mCustomScrollbar("update");
    });
}
refreshMessages();

function redrawIsotope(){
    $('#wall').isotope('reloadItems').isotope({ sortBy: 'original-order' }).isotope('option', { sortBy: 'symbol' });;
}

function buildIsotope(){
    $('#wall').isotope({
        itemSelector: '.wallItem',
        columnWidth : 300,
        gutterWidth:10

    });
    isotopeBuilt = true;
}


//Countdown olayı, post overflowu engellemek için

var count=30; // kaç saniyede bir post gönderebilsin

var counter;
function startTimer(){
    $('#sendMessageInner').html("<div id='counter'></div>");
    counter = setInterval(timer, 1000);
}


function timer()
{
    count=count-1;
    if (count <= 0)
    {
        clearInterval(counter);
        count = 30;
        $('#sendMessageInner').html('<label>Mesajın:</label><textarea rows="10" id="messageToSend"></textarea><button class="sendButton" onclick="sendMessage();">Gönder</button>');
        return;
    }
    //Geri sayımı göster
    $('#counter').html('Tekrar yazmadan önce <div id="count">' +count + '</div> saniye beklemelisin.');
}
function reLink(){
    $('.wallItem > p').linker({target:'_blank'});
}

//Twitter olayları
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');

//Skype olayları

Skype.ui({
    "name": "call",
    "element": "SkypeButton_Call_ondansonrafm_1",
    "participants": ["ondansonrafm"],
    "imageSize": 30
    });