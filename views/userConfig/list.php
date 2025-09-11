<div class="content">
    <h1>Listado de Usuarios</h1>
    <table class="users-table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['user']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['surname']); ?></td>
                <td><?php echo htmlspecialchars($user['mail']); ?></td>
                <td><?php echo htmlspecialchars($user['admin']); ?></td>
                <td class="actions">
                    <a href="#" class="edit-icon" onclick="openEditUserModal(<?php echo $user['id']; ?>); return false;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/userConfig/delete/<?php echo $user['id']; ?>" class="delete-icon"
                       onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">[X]</span>
        <h2>Modificar Usuario</h2>
        <form id="editUserForm" action="/userConfig/updateUser" method="post">
            <input type="hidden" name="id" id="edit-id">
            <label>Usuario</label>
            <input type="text" name="user" id="edit-user" required>
            <label>Nombre</label>
            <input type="text" name="name" id="edit-name" required>
            <label>Apellido</label>
            <input type="text" name="surname" id="edit-surname" required>
            <label>Email</label>
            <input type="email" name="mail" id="edit-mail" required>
            <label>Admin</label>
            <select name="admin" id="edit-admin">
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>
            <label>Contraseña (dejar vacío para no cambiar)</label>
            <input type="password" name="password" id="edit-password">
            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</div>

