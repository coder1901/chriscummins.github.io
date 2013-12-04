<?php setcookie("visit", "1", time()+3600); ?> <!doctype html> <html lang="en"> <head> <title>Chris Cummins</title> <meta name="description" content="The internet homepage of full time geek and part time software developer Chris Cummins."> <meta name="author" content="Chris Cummins"> <meta name="apple-mobile-web-app-capable" content="yes"/> <meta name="viewport" content="width=1024"/> <meta property="fb:admins" content="896185001"> <meta charset="utf-8"/> <style type="text/css">body{padding-top:60px;}</style> <link rel="stylesheet" href="/assets/css/styles.css"/> <link rel="shortcut icon" href="/assets/img/favicon.ico"/> <style>
      body {
        min-height: 1000px;
      }

      /* Per-Step Styles */
      #overview {
          display: none;
          height: 1px;
      }
      .impress-on-overview .step {
          opacity: 0.3;
          cursor: pointer;
      }
      .impress-on-overview .step:hover {
          opacity: 1;
      }
      .impress-on-overview a:hover,
      .impress-on-overview a:focus {
        /* Zero link effects on overview */
        color: #4f6e8d;
      }
    </style> </head> <body class="impress-not-supported"> <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-40829804-1']);
      _gaq.push(['_trackPageview']);

      (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script> <div class="navbar navbar-fixed-top"> <div class="navbar-inner"> <div class="container"> <a class="brand" href="http://chriscummins.cc">chriscummins.cc</a> <div class="nav-collapse collapse pull-right"> <ul class="nav"> <li class="active"><a href="/">Home</a></li> <li><a href="/blog/">Blog</a></li> <li><a href="/music/">Music</a></li> <li><a href="/software/">Software</a></li> <li><a href="/pictures/">Pictures</a></li> </ul> </div> </div> </div> </div> <div id="impress"> <div id="hello" class="step" data-y="500"> <center> <p id="hellomessage"> <?php if (isset($_COOKIE["visit"])) echo "Hello again."; else echo "Hi."; ?> </p> </center> </div> <div id="thingsido" class="step" data-x="1200" data-y="-100" data-rotate="90" data-scale="2"> I love making things. Especially <br/> <a id="music" href="/music/">music</a>, <a id="software" href="/software/">software</a>, and <a id="pictures" href="/pictures/">pictures</a>. </div> <div id="writings" class="step" data-y="-1300" data-scale="0.7"> Occasionally I <a href="blog/">write</a>, when the mood takes me. </div> <div id="contactme" class="step" data-x="-1200" data-y="-350" data-rotate="270"> <center><div id="findme">You can find me elsewhere:</div> <a id="linkedin" href="http://www.linkedin.com/profile/view?id=195091553" target="_blank"> <img src="/assets/img/linkedin.png" alt="Linked In"></a> <a id="facebook" href="http://www.facebook.com/cecummins" target="_blank"> <img src="/assets/img/facebook.png" alt="Facebook"></a> <a id="youtube" href="http://www.youtube.com/user/ChrisCummins" target="_blank"> <img src="/assets/img/youtube.png" alt="YouTube"></a> <a id="soundcloud" href="http://soundcloud.com/peakstudio" target="_blank"> <img src="/assets/img/soundcloud.png" alt="SoundCloud"></a> <a id="github" href="https://github.com/ChrisCummins" target="_blank"> <img src="/assets/img/github.png" alt="GitHub"></a> </center> </div> <div id="overview" class="step" data-x="200" data-y="-300" data-scale="3" data-rotate="25"></div> <div id="source" class="step nonstep smallprint" data-x="-5000" data-y="-300" data-rotate="175"> The source code for this website is available <a href="https://github.com/ChrisCummins/chriscummins.cc" target="_blank">here</a>, and is released under the terms of the MIT License. </div> <div id="privacy" class="step nonstep smallprint" data-x="-5000" data-y="-3000" data-rotate="335"> Privacy is dead, but honestly isn't. This site uses cookies, see <a href="http://www.wikihow.com/Disable-Cookies" target="_blank">here</a> for instructions on how to disable them. I won't sell or use your personal details in any profiteering manner. I would call that a promise, but you wouldn't believe me. </div> </div> <a id="nav-arrow-left" class="nav-arrow">&lt;</a> <a id="nav-arrow-right" class="nav-arrow">&gt;</a> <div class="feedback-overlay"></div> <div class="feedback-form"> <form id="feedback-form-messageform" name="contactme" action=""> <p>Send me a message</p> <textarea id="feedback-form-message" name="message" maxlength="2000"></textarea> <p>Email</p> <input id="feedback-form-email" name="email" type="text" autocomplete="off" maxlength="50"/><br/> <input id="feedback-form-cancel" class="btn btn-large btn-warning" type="reset" value="Cancel"/> <input id="feedback-form-send" class="btn btn-large btn-success" type="submit" value="Send"/> </form> <div id="feedback-form-messagesuccess"> <center> <br/><br/><br/><br/><br/><br/> <img src="/assets/img/check.png"> <br/><br/> Thanks for getting in touch! </center> </div> </div> </body> <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <script src="/assets/js/index.js"></script> <script src="/assets/js/site.js"></script> </html>