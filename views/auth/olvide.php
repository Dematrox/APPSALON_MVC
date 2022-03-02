<h1 class="nombre-pagina">Cambiar contraseña</h1>
<p class="descripcion-pagina">Restablecer la contraseña con tu Email</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form class="formulario" method="POST" action="/olvide">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Tu Email" name="email">
    </div>
    <input type="submit" class="boton" value="Recuperar">
</form>
<div class="acciones">
    <a href="/">Volver a Iniciar Sesion</a>
    <a href="/crear-cuenta">¿No tienes cuenta? Registrarte</a>
</div>