<script type="text/javascript">
(function(e, p){
    var m = location.href.match(/platform=(win8|win|mac|linux|cros)/);
    e.id = (m && m[1]) ||
           (p.indexOf('Windows NT 6.2') > -1 ? 'win8' : p.indexOf('Windows') > -1 ? 'win' : p.indexOf('Mac') > -1 ? 'mac' : p.indexOf('CrOS') > -1 ? 'cros' : 'linux');
    e.className = e.className.replace(/\bno-js\b/,'js');
})(document.documentElement, window.navigator.userAgent)
</script>

  <!-- Modal -->
  <div class="modal fade" id="voiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Voice Command Prompt</h4>
        </div>
        <div class="modal-body">
	      <div class="compact marquee">
	        <div id="info">
	          <p id="info_speak_now" style="display:none">
	            <a class="mic_on text-blink" style="display:none" title="Microphone is active. Speak now."><i class="icon-microphone icon-2x"></i></a>
	            <span> Speak now.<span>
	          </p>
	          <p id="info_no_speech" style="display:none">
	            No speech was detected. You may need to adjust your <a href=
	            "//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">microphone settings</a>.
	          </p>
	          <p id="info_no_microphone" style="display:none">
	            No microphone was found. Ensure that a microphone is installed and that
	            <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">
	            microphone settings</a> are configured correctly.
	          </p>
	          <p id="info_allow" style="display:none">
	            Click the "Allow" button above to enable your microphone.
	          </p>
	          <p id="info_denied" style="display:none">
	            Permission to use microphone was denied.
	          </p>
	          <p id="info_blocked" style="display:none">
	            Permission to use microphone is blocked. To change, go to
	            chrome://settings/contentExceptions#media-stream
	          </p>
	          <p id="info_upgrade" style="display:none">
	            Web Speech API is not supported by this browser. Upgrade to <a href=
	            "//www.google.com/chrome">Chrome</a> version 25 or later.
	          </p>
	        </div>

	        <div id="results">
	          <i class="icon-quote-right"></i> 
	          <span class="final_speech" id="final_speech_span"></span>
	          <span class="interim_speech" id="interim_speech_span"></span>
	          <i class="icon-quote-left"></i> 
	        </div>	        
	        
	        <div id="result_comands" style="display:none">
	        </div>
	        
	    </div>
        </div>
        <div class="modal-footer">

	       <div class="compact marquee" id="div_language" style="display:none" >
	          <select id="select_language" onchange="updateCountry()">
	          </select>&nbsp;&nbsp; 
	          Select your dialect
	          <select id="select_dialect">
	          </select>
	       </div>

	      <a class="try_again_now mic_off" style="display:none" title="Microphone is off. ">
	      	<i class="icon-microphone-off icon-2x"></i>
	      </a>
	      <a class="try_again_now try_again" style="display:none" title="Restat recognition">
	      	<i class="icon-refresh icon-2x"></i>
	      </a>
          <a class="voice-close" data-dismiss="modal"><i class="icon-remove icon-2x"></i></a>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="/intl/en/chrome/assets/common/js/chrome.min.js"></script> 
<script type="text/javascript">
var langs =
[['Afrikaans',       ['af-ZA']],
 ['Bahasa Indonesia',['id-ID']],
 ['Bahasa Melayu',   ['ms-MY']],
 ['Català',          ['ca-ES']],
 ['Čeština',         ['cs-CZ']],
 ['Deutsch',         ['de-DE']],
 ['English',         ['en-AU', 'Australia'],
                     ['en-CA', 'Canada'],
                     ['en-IN', 'India'],
                     ['en-NZ', 'New Zealand'],
                     ['en-ZA', 'South Africa'],
                     ['en-GB', 'United Kingdom'],
                     ['en-US', 'United States']],
 ['Español',         ['es-AR', 'Argentina'],
                     ['es-BO', 'Bolivia'],
                     ['es-CL', 'Chile'],
                     ['es-CO', 'Colombia'],
                     ['es-CR', 'Costa Rica'],
                     ['es-EC', 'Ecuador'],
                     ['es-SV', 'El Salvador'],
                     ['es-ES', 'España'],
                     ['es-US', 'Estados Unidos'],
                     ['es-GT', 'Guatemala'],
                     ['es-HN', 'Honduras'],
                     ['es-MX', 'México'],
                     ['es-NI', 'Nicaragua'],
                     ['es-PA', 'Panamá'],
                     ['es-PY', 'Paraguay'],
                     ['es-PE', 'Perú'],
                     ['es-PR', 'Puerto Rico'],
                     ['es-DO', 'República Dominicana'],
                     ['es-UY', 'Uruguay'],
                     ['es-VE', 'Venezuela']],
 ['Euskara',         ['eu-ES']],
 ['Français',        ['fr-FR']],
 ['Galego',          ['gl-ES']],
 ['Hrvatski',        ['hr_HR']],
 ['IsiZulu',         ['zu-ZA']],
 ['Íslenska',        ['is-IS']],
 ['Italiano',        ['it-IT', 'Italia'],
                     ['it-CH', 'Svizzera']],
 ['Magyar',          ['hu-HU']],
 ['Nederlands',      ['nl-NL']],
 ['Norsk bokmål',    ['nb-NO']],
 ['Polski',          ['pl-PL']],
 ['Português',       ['pt-BR', 'Brasil'],
                     ['pt-PT', 'Portugal']],
 ['Română',          ['ro-RO']],
 ['Slovenčina',      ['sk-SK']],
 ['Suomi',           ['fi-FI']],
 ['Svenska',         ['sv-SE']],
 ['Türkçe',          ['tr-TR']],
 ['български',       ['bg-BG']],
 ['Pусский',         ['ru-RU']],
 ['Српски',          ['sr-RS']],
 ['한국어',            ['ko-KR']],
 ['中文',             ['cmn-Hans-CN', '普通话 (中国大陆)'],
                     ['cmn-Hans-HK', '普通话 (香港)'],
                     ['cmn-Hant-TW', '中文 (台灣)'],
                     ['yue-Hant-HK', '粵語 (香港)']],
 ['日本語',           ['ja-JP']],
 ['Lingua latīna',   ['la']]];

for (var i = 0; i < langs.length; i++) {
  select_language.options[i] = new Option(langs[i][0], i);
}
select_language.selectedIndex = 6;
updateCountry();
select_dialect.selectedIndex = 6;
showInfo('info_start');

function updateCountry() {
  for (var i = select_dialect.options.length - 1; i >= 0; i--) {
    select_dialect.remove(i);
  }
  var list = langs[select_language.selectedIndex];
  for (var i = 1; i < list.length; i++) {
    select_dialect.options.add(new Option(list[i][1], list[i][0]));
  }
  select_dialect.style.visibility = list[1].length == 1 ? 'hidden' : 'visible';
}

var create_email = false;
var final_transcript = '';
var recognizing = false;
var ignore_onend;
var start_timestamp;
if (!('webkitSpeechRecognition' in window)) {
  upgrade();
} else {
  start_button.style.display = 'inline-block';
  var recognition = new webkitSpeechRecognition();
  recognition.continuous = true;
  recognition.interimResults = true;

  recognition.onstart = function() {
    recognizing = true;
    showInfo('info_speak_now');
    $(".mic_on").show();
    $(".try_again_now").hide();    
  };

  recognition.onerror = function(event) {
    if (event.error == 'no-speech') {
	  $(".mic_on").hide();
      showInfo('info_no_speech');
      ignore_onend = true;
    }
    if (event.error == 'audio-capture') {
	  $(".mic_on").hide();
      showInfo('info_no_microphone');
      ignore_onend = true;
    }
    if (event.error == 'not-allowed') {
      if (event.timeStamp - start_timestamp < 100) {
        showInfo('info_blocked');
      } else {
        showInfo('info_denied');
      }
      ignore_onend = true;
    }
  };

  recognition.onend = function() {
    recognizing = false;
    if (ignore_onend) {
      return;
    }
	$(".mic_on").hide();
    if (!final_transcript) {
      showInfo('info_start');
      return;
    }
    showInfo('');
    if (window.getSelection) {
      window.getSelection().removeAllRanges();
      var range = document.createRange();
      range.selectNode(document.getElementById('final_speech_span'));
      window.getSelection().addRange(range);
    }
  };

  recognition.onresult = function(event) {
    var interim_transcript = '';
    if (typeof(event.results) == 'undefined') {
      recognition.onend = null;
      recognition.stop();
      upgrade();
      return;
    }
    for (var i = event.resultIndex; i < event.results.length; ++i) {
      if (event.results[i].isFinal) {
        final_transcript += event.results[i][0].transcript;
      } else {
        interim_transcript += event.results[i][0].transcript;
      }
    }
    final_transcript = capitalize(final_transcript);
    final_speech_span.innerHTML = linebreak(final_transcript);
    interim_speech_span.innerHTML = linebreak(interim_transcript);
    if (final_transcript) {
      lookupCommand(final_transcript);
    }
  };
}

function upgrade() {
  start_button.style.visibility = 'hidden';
  showInfo('info_upgrade');
}

var two_line = /\n\n/g;
var one_line = /\n/g;
function linebreak(s) {
  return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
}

var first_char = /\S/;
function capitalize(s) {
  return s.replace(first_char, function(m) { return m.toUpperCase(); });
}


function startButton(event) {
  if (recognizing) {
    recognition.stop();
    return;
  }
  final_transcript = '';
  recognition.lang = '<?php echo $language_code; ?>';
  recognition.start();
  ignore_onend = false;
  final_speech_span.innerHTML = '';
  interim_speech_span.innerHTML = '';
  showInfo('info_allow');
  start_timestamp = event.timeStamp;
}

function showInfo(s) {
  if (s) {
    for (var child = info.firstChild; child; child = child.nextSibling) {
      if (child.style) {
        child.style.display = child.id == s ? 'inline' : 'none';
      }
    }
    info.style.visibility = 'visible';
  } else {
    info.style.visibility = 'hidden';
  }
}

function lookupCommand( command ) {

     $.ajax({
         url:"<?php echo $command_lookup_url; ?>&term=" + command ,
         type:'GET',
         dataType:'json',
         success:function (data) {
         	console.log(data);
         	display_result(data);
         }
     });

}

function display_result(data) {
	if( data.message ) {
		$('#result_comands').append('<h4>'+ data.message +'</h4>');
		if(data.commands) {
		    $('#result_comands').append('<h4>Commands <span class="badge">'+ data.commands.length +'</span></h4>');
		    $('#result_comands').append('<ul class="list-group">');
		    for (i = 0; i < data.commands.length; ++i) {
		    	$('#result_comands').append('<li class="list-group-item">' + data.commands[i] + '</li>');	
		    }
		    $('#result_comands').append('</ul>');
		}
	} else {
		if(data.found_actions.length == 1 && data.found_actions[0]['confirmation'] == false){
			window.location.href = data.found_actions[0]['url'];
		} else {
			$('#result_comands').append('<h4>Multiple match <span class="badge">'+ data.found_actions.length +'</span></h4>');
		    $('#result_comands').append('<ul class="list-group">');
		    for (i = 0; i < data.found_actions.length; ++i) {
		    	$('#result_comands').append('<li class="list-group-item"><a href="'+ data.found_actions[i]['url'] + '">' + data.found_actions[i]['title'] + '</a></li>');	
		    }
		    $('#result_comands').append('</ul>');
		}
	}
	$('#result_comands').show();
	recognition.stop();
    $(".try_again_now").show();  
	return;
}

$(function () {
	$('#voiceModal').on('hide.bs.modal', function () {
   		recognition.stop();
   		$('#result_comands').html('');
   		$('#result_comands').hide();
   		$('.try_again_now').hide();
	});
	
	$('.mic_on').on('click', function () {
		recognition.stop();
		startButton(event);
		$(".try_again_now").show(); 
	});	
});

</script>
