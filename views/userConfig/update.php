<div class="login">
    <h1>Modificar Usuario</h1>
    <form action="/userConfig/update" method="post">
        <label for="name"><i class="fas fa-id-card"></i></label>
        <input type="text" name="name" placeholder="Nombre" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <label for="surname"><i class="fas fa-id-card-alt"></i></label>
        <input type="text" name="surname" placeholder="Apellido" id="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
        <label for="mail"><i class="fas fa-envelope"></i></label>
        <input type="email" name="mail" placeholder="Correo electrónico" id="mail" value="<?php echo htmlspecialchars($user['mail']); ?>" required>
        <label for="password"><i class="fas fa-lock"></i></label>
        <input type="password" name="password" placeholder="Nueva contraseña (opcional)" id="password">
        <input type="submit" value="Guardar Cambios">
    </form>
</div>
