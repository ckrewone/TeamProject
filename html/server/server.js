//dzialajacy  Load required modules
var https   = require("https");              // http server core module
var express = require("express");           // web framework external module
var serveStatic = require('serve-static');  // serve static files
var socketIo = require("socket.io")(https);        // web socket external module
var easyrtc = require("../");               // EasyRTC external module
var fs = require('fs');



var options = {
  key: fs.readFileSync('privateKey.key'),
  cert: fs.readFileSync('certificate.crt')
};


process.title = "node-easyrtc";

var app = express();
app.use(serveStatic('static', {'index': ['index.html']}));


var webServer = https.createServer(options, app).listen(443);


var socketServer = socketIo.listen(webServer, {"log level":1});

easyrtc.setOption("logLevel", "debug");

// Overriding the default easyrtcAuth listener, only so we can directly access its callback
easyrtc.events.on("easyrtcAuth", function(socket, easyrtcid, msg, socketCallback, callback) {
    easyrtc.events.defaultListeners.easyrtcAuth(socket, easyrtcid, msg, socketCallback, function(err, connectionObj){
        if (err || !msg.msgData || !msg.msgData.credential || !connectionObj) {
            callback(err, connectionObj);
            return;
        }

        connectionObj.setField("credential", msg.msgData.credential, {"isShared":false});

        console.log("["+easyrtcid+"] Credential saved!", connectionObj.getFieldValueSync("credential"));

        callback(err, connectionObj);
    });
});

// To test, lets print the credential to the console for every room join!
easyrtc.events.on("roomJoin", function(connectionObj, roomName, roomParameter, callback) {
    console.log("["+connectionObj.getEasyrtcid()+"] Credential retrieved!", connectionObj.getFieldValueSync("credential"));
    easyrtc.events.defaultListeners.roomJoin(connectionObj, roomName, roomParameter, callback);
});


var rtc = easyrtc.listen(app, socketServer, null, function(err, rtcRef) {
    console.log("Initiated");

    rtcRef.events.on("roomCreate", function(appObj, creatorConnectionObj, roomName, roomOptions, callback) {
        console.log("roomCreate fired! Trying to create: " + roomName);

        appObj.events.defaultListeners.roomCreate(appObj, creatorConnectionObj, roomName, roomOptions, callback);
    });
});

function onConnection(socket){
  socket.on('drawing', (id, data) => socket.broadcast.to(id).emit('drawing', data));
  socket.on('clear', (id) => socket.broadcast.to(id).emit('clear'));
}

socketIo.on('connection', onConnection);