<?php

 class eventsRepository extends dbRepository {
    
    public function getCurrentEvents($idUser, $libreta = null) {
        $sql = "SELECT * FROM eventos where user = " . $idUser . " AND (fecha >= " . time() . " OR repeticion > 0) ORDER BY fecha ASC";
        $result = $this->con->query($sql);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function addEvent($eventData) {
        $sql = "INSERT INTO eventos (user, titulo, fecha, tipo, repeticion, comentarios) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("isiiis", 
            $eventData['idUser'],
            $eventData['title'],
            $eventData['date'],
            $eventData['type'],
            $eventData['repetition'],
            $eventData['comments']
        );

        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function getEventById($eventId) {
        $sql = "SELECT * FROM eventos WHERE id = ? AND user = ?";
        $stmt = $this->con->prepare($sql);
        
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("ii", $eventId, $_SESSION['idUser']);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $stmt->close();
        return $event;
    }

    public function updateEvent($eventId, $eventData) {
        $sql = "UPDATE eventos SET titulo = ?, fecha = ?, tipo = ?, repeticion = ?, comentarios = ? WHERE id = ? AND user = ?";
        
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("siiiiii", 
            $eventData['title'],
            $eventData['date'],
            $eventData['type'],
            $eventData['repetition'],
            $eventData['comments'],
            $eventId,
            $_SESSION['idUser']
        );

        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function deleteEvent($eventId, $userId) {
        $sql = "DELETE FROM eventos WHERE id = ? AND user = ?";
        
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $eventId, $userId);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
}