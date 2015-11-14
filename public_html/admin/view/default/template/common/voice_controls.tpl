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
          <h4 class="modal-title"><?php echo $text_voice_command_prompt; ?></h4>
        </div>
        <div class="modal-body">
	      <div class="compact marquee">
	        <div id="info">
	          <div id="info_speak_now" style="display:none">
	            <a class="mic_on text-blink" style="display:none" title="<?php echo $text_voice_speak_now; ?>">
		            <i class="fa fa-microphone fa-lg"></i>
	            </a>
	            <span><?php echo $text_voice_speak_now; ?></span>
	          </div>
	          <div id="info_allow" class="alert alert-info" style="display:none">
	            <?php echo $text_voice_click_allow; ?>
	          </div>
	          <div id="info_no_speech" class="alert" style="display:none">
	          	<?php echo $text_voice_no_speach_detected; ?>
	          </div>
	          <div id="info_no_microphone" class="alert alert-error alert-danger" style="display:none">
	          	<?php echo $text_voice_no_mic_detected; ?>
	          </div>
	          <div id="info_denied" class="alert alert-error alert-danger" style="display:none">
	            <?php echo $text_voice_mic_denied; ?>
	          </div>
	          <div id="info_blocked" class="alert alert-error alert-danger" style="display:none">
				<?php echo $text_voice_mic_permission; ?>				
	          </div>
	          <div id="info_upgrade" class="alert alert-error alert-danger" style="display:none">
				<?php echo $text_voice_not_supported; ?>
	          </div>
	        </div>

	        <div id="results" style="display:none">
	          <i class="fa fa-quote-right"></i> 
	          <span class="final_speech" id="final_speech_span"></span>
	          <span class="interim_speech" id="interim_speech_span"></span>
	          <i class="fa fa-quote-left"></i> 
	        </div>	        
	        
	        <div id="result_comands" style="display:none">
	    </div>

	    </div>
        </div>
        <div class="modal-footer">
			<div class="col-sm-9">
				<div class="compact marquee" id="div_language" style="display:none" >
				   <input type="hidden" id="select_language" value="<?php echo $language_code; ?>">
				   <?php echo $text_voice_select_dialect; ?>
				   <select id="select_dialect">
				   </select>
				</div>
			</div>
			<div class="col-sm-3">
				<a class="try_again_now try_again" style="display:none" title="Retry" onclick="startButton(event)">
				  <i class="fa fa-refresh fa-2x"></i>
				</a>
				<a class="voice-close" data-dismiss="modal"><i class="fa fa-remove fa-2x"></i></a>
			</div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">

var dialects = [
['en',     ['en-US', 'United States'],
           ['en-CA', 'Canada'],
		   ['en-GB', 'United Kingdom'],
		   ['en-AU', 'Australia'],
           ['en-IN', 'India'],
           ['en-NZ', 'New Zealand'],
           ['en-ZA', 'South Africa']],
 ['es',    ['es-AR', 'Argentina'],
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
 ['it',    ['it-IT', 'Italia'],
           ['it-CH', 'Svizzera']],
 ['pt',    ['pt-BR', 'Brasil'],
           ['pt-PT', 'Portugal']],
 ['cmn',   ['cmn-Hans-CN', '普通话 (中国大陆)'],
           ['cmn-Hans-HK', '普通话 (香港)'],
           ['cmn-Hant-TW', '中文 (台灣)'],
           ['yue-Hant-HK', '粵語 (香港)']]
];


updateDialect('<?php echo $language_code; ?>');
showInfo('info_start');

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
    $("#results").show();
    final_transcript = capitalize(final_transcript);
    final_speech_span.innerHTML = linebreak(final_transcript);
    interim_speech_span.innerHTML = linebreak(interim_transcript);
    if (final_transcript) {
      lookupCommand(final_transcript);
    }
  };
}

$(function () {

	$('#select_dialect').on('change', function (event) {
		startButton(event);
	});
	
	$('#voiceModal').on('hide.bs.modal', function (event) {
  		if( recognition ) {
   			recognition.stop();
  		}

   		$('#result_comands').html('');
   		$('#result_comands').hide();
   		$('.try_again_now').hide();
	});
	
	$('.mic_on').on('click', function (event) {
		recognition.stop();
		startButton(event);
		$(".try_again_now").show(); 
	});	
});

function updateDialect( lang_code ) {
  for (var i = select_dialect.options.length - 1; i >= 0; i--) {
    select_dialect.remove(i);
  }
  var list = [];
  for (var i = 0; i < dialects.length; i++) {
  	if (dialects[i][0] == lang_code) {
  		list = dialects[i];
  	}
  } 
  if (list) {
  	for (var i = 1; i < list.length; i++) {
    	select_dialect.options.add(new Option(list[i][1], list[i][0]));
  	}
  	if(list.length > 1 ) {
  		$('#div_language').show();
  	}
  }
}

function upgrade() {
  $('#start_button i').addClass('grey_out');
  $('#start_button i').addClass('fa');
  $('#start_button i').addClass('fa-microphone-slash');
  $('#start_button i').addClass('fa-lg');
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
  if( !recognition ) {
  	return;
  }

  if (recognizing) {
    recognition.stop();
    return;
  }
  $('#result_comands').html('');
  $("#results").hide();
  final_transcript = '';
  var lang_code =  $('#select_dialect option:selected').val();
  if (!lang_code) {
  	lang_code = '<?php echo $language_code; ?>';
  }
  recognition.lang = lang_code;
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
        child.style.display = child.id == s ? 'inherit' : 'none';
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
		if( data.found_actions.length == 1 ){
			if (data.found_actions[0]['confirmation'] == true) {
				if ( confirm(<?php js_echo($text_voice_confirm_action); ?>) ) {
					window.location.href = data.found_actions[0]['url'];
				}
			} else {
				window.location.href = data.found_actions[0]['url'];
			}
			
		} else {
			$('#result_comands').append('<h4>Commands matched <span class="badge">'+ data.found_actions.length +'</span></h4>');
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

</script>
