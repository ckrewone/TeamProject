var socket = io.connect();

// w przypadku połączenia z serwerem, poproś o nazwę użytkownika z anonimowym oddzwonieniem
socket.on('connect', function(){
  // wywołaj funkcję "adduser" po stronie serwera i wyślij jeden parametr (wartość prompt)
  socket.emit('adduser', prompt("What's your name?"));
});

// detektor, kiedy serwer wydaje komunikat "updatechat", aktualizuje treść czatu
socket.on('updatechat', function (username, data) {
  $('#pconversation').append('<b>'+username + ':</b> ' + data + '<br>');
});

// detektor, kiedy serwer wysyła "aktualizatorów", aktualizuje listę nazw użytkowników


socket.on('updateusers', function(data) {
  $('#users').empty();
  $.each(data, function(key, value) {
    $('#users').append('<div>' + key + '</div>');
  });
});



// przy ładowaniu strony
$(function(){
  // when the client clicks SEND
  $('#datasend').click( function() {
    var message = $('#data').val();
    $('#data').val('');
    // tell server to execute 'sendchat' and send along one parameter
    socket.emit('sendchat', message);
  });

  // when the client hits ENTER on their keyboard
  $('#data').keypress(function(e) {
    if(e.which == 13) {
      $(this).blur();
      $('#datasend').focus().click();
    }
  });
});
