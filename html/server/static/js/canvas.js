
'use strict';

(function() {

  //var socket = io();
  var canvas = document.getElementsByClassName('whiteboard')[0];
  var colors = document.getElementsByClassName('color');
  var buttons = document.getElementsByClassName('button');
  var context = canvas.getContext('2d');

  var current = {
    color: 'black'
  };
  
  var drawing = false;
 
  
   /*<< EventListenery >*/
  
  canvas.addEventListener('mousedown', onMouseDown, false);
  canvas.addEventListener('mouseup', onMouseUp, false);
  canvas.addEventListener('mouseout', onMouseUp, false);
  canvas.addEventListener('mousemove', throttle(onMouseMove, 10), false);
  

  for (var i = 0; i < colors.length; i++){
    colors[i].addEventListener('click', onColorUpdate, false);
  }
  
  for (var i = 0; i < buttons.length; i++){
    buttons[i].addEventListener('click', onClick, false);
  }


  socket.on('drawing', onDrawingEvent);
  socket.on('clear', clearCanvas);
  
   /*<< Nasluchywanie czy przycisk zostal wlaczony >*/
  
  document.getElementById('download').addEventListener('click', function() {
    downloadCanvas(this, 'image.jpg');
  }, false);

  window.addEventListener('resize', onResize, false);
  onResize();

 /*<< Rysowanie lini >*/
  function drawLine(x0, y0, x1, y1, color, emit){
    context.beginPath();
    context.moveTo(x0, y0);
    context.lineTo(x1, y1);
    context.strokeStyle = color;
    context.lineWidth = 4;
    context.stroke();
    context.closePath();
	//console.log(socket.id, mySocketID, callerSocketID);
	
	console.log("x0: " + x0 + "  y0: " + y0 + "  x1: " + x1 + "  y1: " + y1);
	
    if (!emit) { return; }
    var w = canvas.width;
    var h = canvas.height;
    socket.emit('drawing', callerSocketID, {
      x0: x0 / w,
      y0: y0 / h,
      x1: x1 / w,
      y1: y1 / h,
      color: color
    });
  }
  
  /*<< Obsluga przycisku czyszczenia canvasa >*/
  function onClick(e){
	  if(e.target.className.split(' ')[1] == 'clear'){
		clearCanvas();
		socket.emit('clear', callerSocketID);
	  }
  }
   /*<< Obsluga przyciskow zmianny koloru >*/
  function onColorUpdate(e){
    current.color = e.target.className.split(' ')[1];
  }

 /*<< Obsluga myszy >*/
  function onMouseDown(e){
    drawing = true;
	var pos = getMousePos(e);
    current.x = pos.x;
    current.y = pos.y;
	console.log(e.clientX, e.clientY + "kurwa");
  }

  function onMouseUp(e){
    if (!drawing) { return; }
    drawing = false;
	var pos = getMousePos(e);
    drawLine(current.x, current.y, pos.x, pos.y, current.color, true);
  }

  function onMouseMove(e){
    if (!drawing) { return; }
	var pos = getMousePos(e);
    drawLine(current.x, current.y, pos.x, pos.y, current.color, true);
    current.x = pos.x;
    current.y = pos.y;
  }
  
  function getMousePos(evt) {
	var rect = canvas.getBoundingClientRect(),
		scaleX = canvas.width / rect.width,
		scaleY = canvas.height / rect.height;

	return {
		x: (evt.clientX - rect.left) * scaleX,
		y: (evt.clientY - rect.top) * scaleY   
	}
}
  
 /********************************************************/
 

  // limit the number of events per second
  function throttle(callback, delay) {
    var previousCall = new Date().getTime();
    return function() {
      var time = new Date().getTime();

      if ((time - previousCall) >= delay) {
        previousCall = time;
        callback.apply(null, arguments);
      }
    };
  }

  function onDrawingEvent(data){
    var w = canvas.width;
    var h = canvas.height;
    drawLine(data.x0 * w, data.y0 * h, data.x1 * w, data.y1 * h, data.color);
  }

/*<< Resize canvasa >*/
  function onResize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  
/*<< Czyszczenie canvasa >*/
  function clearCanvas(){
	  context.clearRect(0, 0, canvas.width, canvas.height);
	  fillCanvas();
  }
  
/*<< Funkcja sciagajaca obraz >*/
  function downloadCanvas(link, filename) {
    link.href = canvas.toDataURL();
    link.download = filename;
  }
  
/*<< Wypelnienie canvasa bialym tlem >*/
  function fillCanvas(){
	context.fillStyle = '#ffffff';
	context.fillRect(0, 0, canvas.width, canvas.height);
  }
  
  fillCanvas();
  
})();
