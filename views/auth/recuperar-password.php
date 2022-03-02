<h1 class="nombre-pagina">Reestablecer</h1>
<p class="descripcion-pagina">Define tu nuevo password a continuacion</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<?php if($error) return; ?>
<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu nuevoo Password" />
    </div>
    <input type="submit" class="boton" value="Guardar Nuevo Password">
</form>
<div class="acciones">
    <a href="/">Volver a Iniciar Sesion</a>
    <a href="/crear-cuenta">¿No tienes cuenta? Registrarte</a>
</div>