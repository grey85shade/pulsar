<?php

include_once ("models/logsRepository.php");
include_once ("models/eventsRepository.php");
include_once ("AppUtils.php");
class dashController
{
    public function index ()
    {
        $logs = $this->getAllLogs();
        $events = $this->getCurrentEvents();
        
        require_once ("views/dash/index.php");

    }

    public function newLog () 
    {
        if (!isset($_SESSION['idUser']) || 
            !isset($_POST['logTags']) || 
            !isset($_POST['logDate']) || 
            !isset($_POST['logContent'])) {
            echo 'error 1';
            exit;
        }

        $tags = trim($_POST['logTags']);
        $date = trim($_POST['logDate']);
        $content = trim($_POST['logContent']);

        if (empty($tags) || empty($date) || empty($content) || 
            strlen($tags) > 200 || 
            strlen($content) > 4800) {
            echo 'error 2';
            var_dump($tags, $date, $content);
            exit;
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            echo 'error 3';
            exit;
        }

        if ($_POST['newLogPass'] != '') {
            $P = true;
            $content = AppUtils::cifrar($content, $_POST['newLogPass']);
        } else {
            $P = null;
        }

        $idLog = null;
        if ($_POST['logIdModify'] != '') {
            $idLog = $_POST['logIdModify'];
        }

        $logData = [
            'idUser' => $_SESSION['idUser'],
            'date' => $timestamp,
            'tags' => $tags,
            'content' => $content,
            'pass' => $P,
            'idLog' => $idLog
        ];

        $logsRepo = new logsRepository();
        if ($idLog !== null) {
            $logsRepo->editLog($logData);
        } else {
            $logsRepo->addLog($logData);
        }
        
        header('Location: /dash');
    }

    public function newEvent() 
    {
        if (!isset($_SESSION['idUser']) || 
            !isset($_POST['eventTitle']) || 
            !isset($_POST['eventDate']) || 
            !isset($_POST['eventType']) || 
            !isset($_POST['eventRepetition'])) {
            header('Location: /dash');
            exit;
        }

        $title = trim($_POST['eventTitle']);
        $date = trim($_POST['eventDate']);
        $type = intval($_POST['eventType']);
        $repetition = intval($_POST['eventRepetition']);
        $comments = isset($_POST['eventComments']) ? trim($_POST['eventComments']) : '';

        // Validaciones
        if (empty($title) || empty($date) || 
            strlen($title) > 100 || 
            strlen($comments) > 1000 ||
            !in_array($type, [0, 1]) || 
            !in_array($repetition, [0, 1, 2, 3, 4, 5])) {
            header('Location: /dash');
            exit;
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            header('Location: /dash');
            exit;
        }

        $eventData = [
            'idUser' => $_SESSION['idUser'],
            'title' => $title,
            'date' => $timestamp,
            'type' => $type,
            'repetition' => $repetition,
            'comments' => $comments
        ];

        $eventsRepo = new eventsRepository();
        $isUpdate = isset($_POST['eventUpdate']) && isset($_POST['eventIdModify']) && !empty($_POST['eventIdModify']);

        if ($isUpdate) {
            $eventId = intval($_POST['eventIdModify']);
            $result = $eventsRepo->updateEvent($eventId, $eventData);
            $message = $result ? 'Event updated successfully.' : 'Could not update event.';
        } else {
            $result = $eventsRepo->addEvent($eventData);
            $message = $result ? 'Event added successfully.' : 'Could not add event.';
        }

        if ($result) {
            AppUtils::setFlash($message, 'success');
        } else {
            AppUtils::setFlash($message, 'error');
        }

        header('Location: /dash');
        exit;
    }

    public function deleteLog()
    {
        if (!isset($_SESSION['idUser']) || !isset($_POST['logId'])) {
            header('Location: /dash/index');
            exit;
        }

        $idLog = intval($_POST['logId']);
        if ($idLog <= 0) {
            header('Location: /dash/index');
            exit;
        }

        $logsRepo = new logsRepository();
        $result = $logsRepo->deleteLog($idLog, $_SESSION['idUser']);

        if ($result) {
            AppUtils::setFlash('Log entry deleted successfully.', 'success');
        } else {
            AppUtils::setFlash('Could not delete log entry.', 'error');
        }
        return json_encode(['success' => $result]);
    }

    public function deleteEvent()
    {
        if (!isset($_SESSION['idUser']) || !isset($_POST['eventId'])) {
            return json_encode(['success' => false, 'message' => 'Invalid request']);
        }

        $eventId = intval($_POST['eventId']);
        if ($eventId <= 0) {
            return json_encode(['success' => false, 'message' => 'Invalid event ID']);
        }

        $eventsRepo = new eventsRepository();
        $result = $eventsRepo->deleteEvent($eventId, $_SESSION['idUser']);

        if ($result) {
            AppUtils::setFlash('Event deleted successfully.', 'success');
        } else {
            AppUtils::setFlash('Could not delete event.', 'error');
        }
        return json_encode(['success' => $result]);
    }

    private function getAllLogs ()
    {
        if (isset($_SESSION['idUser'])) {
            $idUser = $_SESSION['idUser'];
        } else {
            header('Location: /login');
            exit;
        }

        $logsRepo = new logsRepository();
        return $logsRepo->getAllLogs($idUser);
    }
    private function getCurrentEvents ()
    {
        $idUser = $_SESSION['idUser'];
        $eventsRepo = new eventsRepository();
        $events = $eventsRepo->getCurrentEvents($idUser);
        $now = time();

        foreach ($events as &$event) {
            if ($event['fecha'] < $now && $event['repeticion'] > 0) {
                $date = $event['fecha'];
                
                // Seguir sumando hasta encontrar la próxima fecha válida
                while ($date < $now) {
                    switch ($event['repeticion']) {
                        case 1: // Diario
                            $date = strtotime('+1 day', $date);
                            break;
                        case 2: // Semanal
                            $date = strtotime('+1 week', $date);
                            break;
                        case 3: // Bisemanal
                            $date = strtotime('+2 weeks', $date);
                            break;
                        case 4: // Mensual
                            $date = strtotime('+1 month', $date);
                            break;
                        case 5: // Anual
                            $date = strtotime('+1 year', $date);
                            break;
                    }
                }
                
                $event['fecha'] = $date;
            }
        }

        return $events;
    }
    

}
