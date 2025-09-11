<div class="login">
    <h1>Registrar Usuario Nuevo</h1>
    <form action="/userConfig/register" method="post">
        <label for="user">
            <i class="fas fa-user"></i>
        </label>
        <input type="text" name="user" placeholder="Usuario" id="user" required>
        <label for="name">
            <i class="fas fa-id-card"></i>
        </label>
        <input type="text" name="name" placeholder="Nombre" id="name" required>
        <label for="surname">
            <i class="fas fa-id-card-alt"></i>
        </label>
        <input type="text" name="surname" placeholder="Apellido" id="surname" required>
        <label for="mail">
            <i class="fas fa-envelope"></i>
        </label>
        <input type="email" name="mail" placeholder="Correo electrónico" id="mail" required>
        <label for="password">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Contraseña" id="password" required>
        <input type="submit" value="Registrar">
    </form>
</div>
