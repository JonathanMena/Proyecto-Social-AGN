document.getElementById('loginForm').addEventListener('submit', function(event) {
  event.preventDefault();

  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  // Enviar los datos al archivo PHP usando AJAX
  $.ajax({
    url: 'login.php',
    type: 'POST',
    data: {username: username, password: password},
    success: function(response) {
      const res = JSON.parse(response);
      if (res.success) {
        // Redirigir al index si el login es exitoso
        window.location.href = 'index.php';
      } else {
        // Mostrar error si las credenciales no son correctas
        document.getElementById('error-message').textContent = res.message;
        document.getElementById('error-message').style.display = 'block';
      }
    }
  });
});
