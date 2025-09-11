<?php

 class logsRepository extends dbRepository {
    
    public function getAllLogs($idUser, $libreta = null) {
        $sql = "SELECT * FROM logs where user = " . $idUser . " ORDER BY date DESC";
        $result = $this->con->query($sql);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
    }

    public function getLogById($idLog) {
        $sql = "SELECT * FROM logs where id = " . intval($idLog) . " AND user = " . $_SESSION['idUser'];
        $result = $this->con->query($sql);
        
        return $result ? $result->fetch_assoc() : null;
    }

    public function addLog($logData)
    {
        // Preparar la consulta usando prepared statements para mayor seguridad
        $sql = "INSERT INTO logs (user, date, tags, content, pass) VALUES (?, ?, ?, ?, ?)";
        
        // Preparar el statement
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("iisss", 
            $logData['idUser'],
            $logData['date'],
            $logData['tags'],
            $logData['content'],
            $logData['pass']
        );

        // Ejecutar la consulta
        $result = $stmt->execute();
        
        // Cerrar el statement
        $stmt->close();
        
        return $result;
    }

    public function editLog($logData) {
        // Preparar la consulta usando prepared statements para mayor seguridad
        $sql = "UPDATE logs 
        SET `user` = ?, `date` = ?, `tags` = ?, `content` = ?, `pass` = ? 
        WHERE id = ?";
        
        // Preparar el statement
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("iisssi", 
            $logData['idUser'],
            $logData['date'],
            $logData['tags'],
            $logData['content'],
            $logData['pass'],
            $logData['idLog']
        );

        // Ejecutar la consulta
        $result = $stmt->execute();

        // Cerrar el statement
        $stmt->close();
        
        return $result;
    }

    public function deleteLog($idLog, $idUser) {
        $sql = "DELETE FROM logs WHERE id = ? AND user = ?";
        
        // Preparar el statement
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("ii", 
            $idLog,
            $idUser
        );

        // Ejecutar la consulta
        $result = $stmt->execute();
        
        // Cerrar el statement
        $stmt->close();
        
        return $result;
    }

}