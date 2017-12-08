


var selfEasyrtcid = "";
var haveSelfVideo = false;

var user = "Guest "
var selfEasyrtcid = "";

var callerid = null;

function addToConversation(who, msgType, content) {
  // Escape html special characters, then add linefeeds.
  content = content.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
  content = content.replace(/\n/g, "<br />");
  document.getElementById("conversation").innerHTML +=
  "<b>" + who + ":</b>&nbsp;" + content + "<br />";
}
 

function disable(domId) {
    document.getElementById(domId).disabled = "disabled";
}


function enable(domId) {
    document.getElementById(domId).disabled = "";
}

var onceOnly = true;


function connect() {	
  easyrtc.enableAudio(document.getElementById("shareAudio").checked);
  easyrtc.enableVideo(document.getElementById("shareVideo").checked);
  easyrtc.setPeerListener(addToConversation);
  easyrtc.enableDataChannels(true);
  easyrtc.setRoomOccupantListener( convertListToButtons);    
  easyrtc.connect("easyrtc.audioVideo", loginSuccess, loginFailure);			  
  if( onceOnly ) {
      easyrtc.getAudioSinkList( function(list) {
         for(let ele of list ) {
             addSinkButton(ele.label, ele.deviceId);
         }
      });
      onceOnly = false;
  }
} 


function addSinkButton(label, deviceId){
   let button = document.createElement("button");
   button.innerText = label?label:deviceId;
   button.onclick = function() {
      easyrtc.setAudioOutput( document.getElementById("callerVideo"), deviceId);
   }
   document.getElementById("audioSinkButtons").appendChild(button);
}


function hangup() {
    easyrtc.hangupAll();
    disable('hangupButton');
}





function clearConnectList() {
    var otherClientDiv = document.getElementById('otherClients');
    while (otherClientDiv.hasChildNodes()) {
        otherClientDiv.removeChild(otherClientDiv.lastChild);
    }
}


function convertListToButtons (roomName, occupants, isPrimary) {
    clearConnectList();
    var otherClientDiv = document.getElementById('otherClients');
    for(var easyrtcid in occupants) {
        var button = document.createElement('button');
        button.onclick = function(easyrtcid) {
            return function() {
                performCall(easyrtcid);
            };
        }(easyrtcid);

        var label = document.createTextNode("Call " + user + "(" + easyrtc.idToName(easyrtcid) + ")");
        button.appendChild(label);
        otherClientDiv.appendChild(button);
        }
}

function send(){
    sendStuffWS(callerid);
    console.log("Send mess to: " + easyrtc.idToName(callerid));
}


function sendStuffWS(otherEasyrtcid) {
  var text = document.getElementById("sendMessageText").value;
  if(text.replace(/\s/g, "").length === 0) { // Don"t send just whitespace
    return;
  }
 
  easyrtc.sendDataWS(otherEasyrtcid, "message",  text);
  addToConversation("Me", "message", text);
  document.getElementById("sendMessageText").value = "";
}

function setUpMirror() {
    if( !haveSelfVideo) {
        var selfVideo = document.getElementById("selfVideo");
        easyrtc.setVideoObjectSrc(selfVideo, easyrtc.getLocalStream());
        selfVideo.muted = true;
        haveSelfVideo = true;
    }
}

function performCall(otherEasyrtcid) {
    easyrtc.hangupAll();
    var acceptedCB = function(accepted, easyrtcid) {
        if( !accepted ) {
            easyrtc.showError("CALL-REJECTEd", "Sorry, your call to " + easyrtc.idToName(easyrtcid) + " was rejected");
            enable('otherClients');
        }
        callerid = otherEasyrtcid;
    };

    var successCB = function() {
        if( easyrtc.getLocalStream()) {
            setUpMirror();
        }
        enable('hangupButton');
    };
    var failureCB = function() {
        enable('otherClients');
    };
    easyrtc.call(otherEasyrtcid, successCB, failureCB, acceptedCB);
    enable('hangupButton');
}


function loginSuccess(easyrtcid) {
    disable("connectButton");
    enable("disconnectButton");
    enable('otherClients');
    selfEasyrtcid = easyrtcid;
    document.getElementById("iam").innerHTML = "I am " + user + "(" + easyrtc.cleanId(easyrtcid) + ")";
    easyrtc.showError("noerror", "logged in");
}


function loginFailure(errorCode, message) {
    easyrtc.showError(errorCode, message);
}

function disconnect() {
  easyrtc.disconnect();	
  document.getElementById("iam").innerHTML = "logged out";
  enable("connectButton");
  disable("disconnectButton"); 
  easyrtc.clearMediaStream( document.getElementById('selfVideo'));
  easyrtc.setVideoObjectSrc(document.getElementById("selfVideo"),"");
  easyrtc.closeLocalMediaStream();
  easyrtc.setRoomOccupantListener( function(){});  
  clearConnectList();
} 


easyrtc.setStreamAcceptor( function(easyrtcid, stream) {
    setUpMirror();
    var video = document.getElementById('callerVideo');
    easyrtc.setVideoObjectSrc(video,stream);
    enable("hangupButton");
});



easyrtc.setOnStreamClosed( function (easyrtcid) {
    easyrtc.setVideoObjectSrc(document.getElementById('callerVideo'), "");
    disable("hangupButton");
});


var callerPending = null;

easyrtc.setCallCancelled( function(easyrtcid){
    if( easyrtcid === callerPending) {
        document.getElementById('acceptCallBox').style.display = "none";
        callerPending = false;
    }
});


easyrtc.setAcceptChecker(function(easyrtcid, callback) {
    document.getElementById('acceptCallBox').style.display = "block";
    callerPending = easyrtcid;
    if( easyrtc.getConnectionCount() > 0 ) {
        document.getElementById('acceptCallLabel').innerHTML = "Drop current call and accept new from " + easyrtc.idToName(easyrtcid) + " ?";
    }
    else {
        document.getElementById('acceptCallLabel').innerHTML = "Accept incoming call from " + easyrtc.idToName(easyrtcid) + " ?";
    }
    var acceptTheCall = function(wasAccepted) {
        document.getElementById('acceptCallBox').style.display = "none";
        if( wasAccepted && easyrtc.getConnectionCount() > 0 ) {
            easyrtc.hangupAll();
        }
        callback(wasAccepted);
        callerPending = null;
    };
    document.getElementById("callAcceptButton").onclick = function() {
        acceptTheCall(true);
    };
    document.getElementById("callRejectButton").onclick =function() {
        acceptTheCall(false);
    };
} );


function clearBox(elementID)
{
    document.getElementById(elementID).innerHTML = "";
}