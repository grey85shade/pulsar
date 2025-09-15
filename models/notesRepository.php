<?php

 class notesRepository extends dbRepository {
    
    public function getAllNotes($idUser, $libreta = null) {
        $sql = "SELECT * FROM notes where user = " . $idUser . " ORDER BY date DESC";
        $result = $this->con->query($sql);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
    }

    public function getNoteById($idNote) {
        $sql = "SELECT * FROM notes where id = " . intval($idNote) . " AND user = " . $_SESSION['idUser'];
        $result = $this->con->query($sql);
        
        return $result ? $result->fetch_assoc() : null;
    }

    public function addNote($noteData)
    {
        // Preparar la consulta usando prepared statements para mayor seguridad
        $sql = "INSERT INTO notes (user, date, tags, content, pass) VALUES (?, ?, ?, ?, ?)";
        
        // Preparar el statement
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("iisss", 
            $noteData['idUser'],
            $noteData['date'],
            $noteData['tags'],
            $noteData['content'],
            $noteData['pass']
        );

        // Ejecutar la consulta
        $result = $stmt->execute();
        
        // Cerrar el statement
        $stmt->close();
        
        return $result;
    }

    public function editNote($noteData) {
        // Preparar la consulta usando prepared statements para mayor seguridad
        $sql = "UPDATE notes 
        SET `user` = ?, `date` = ?, `tags` = ?, `content` = ?, `pass` = ? 
        WHERE id = ?";

        // Preparar el statement
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("iisssi", 
            $noteData['idUser'],
            $noteData['date'],
            $noteData['tags'],
            $noteData['content'],
            $noteData['pass'],
            $noteData['idNote']
        );

        // Ejecutar la consulta
        $result = $stmt->execute();

        // Cerrar el statement
        $stmt->close();
        
        return $result;
    }

    public function deleteNote($idNote, $idUser) {
        $sql = "DELETE FROM notes WHERE id = ? AND user = ?";
        
        // Preparar el statement
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("ii", 
            $idNote,
            $idUser
        );

        // Ejecutar la consulta
        $result = $stmt->execute();
        
        // Cerrar el statement
        $stmt->close();
        
        return $result;
    }

}