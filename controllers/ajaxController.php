<?php

include_once ("models/logsRepository.php");
include_once ("models/eventsRepository.php");
include_once ("AppUtils.php");

class ajaxController
{ 
    public function getUser($id)
    {
        if (!isset($_SESSION['idUser']) || $_SESSION['admin'] != 1) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $db = new dbRepository();
        $user = $db->getUserById((int)$id);
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
        exit;
    }


     public function getLogById ()
    {
        $idLog = $_POST['idLog'];
        if (isset($_POST['pass'])) {
            $pass = $_POST['pass'];
        } else {
            $pass = null;
        }

        $logsRepo = new logsRepository();
        $log = $logsRepo->getLogById($idLog);
    
        if ($log) {
            if ($pass !== null && $log['pass'] != '') {
                $log['content'] = AppUtils::descifrar($log['content'], $pass);
            }
           $log['content'] = AppUtils::renderSafeHtml($log['content']);
            echo json_encode($log, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Log entry not found']);
        }
        exit;
    }

    public function logHasPass()
    {
        $idLog = $_POST['idLog'];
        $logsRepo = new logsRepository();
        $log = $logsRepo->getLogById($idLog);
    
        if ($log) {
            if ($log['pass'] != '' && $log['pass'] != 0) {
                echo json_encode(['hasPass' => true]);
            } else {
                echo json_encode(['hasPass' => false]);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Log entry not found']);
        }
        exit;
    }

    public function getEventById()
    {
        if (!isset($_SESSION['idUser']) || !isset($_POST['eventId'])) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $eventId = intval($_POST['eventId']);
        $eventsRepo = new eventsRepository();
        $event = $eventsRepo->getEventById($eventId);

        if ($event) {
            echo json_encode($event);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Event not found']);
        }
        exit;
    }

    public function autoSaveLog()
    {
        if (!isset($_SESSION['idUser']) || !isset($_POST['idLog'], $_POST['content'])) {

            if (!isset($_POST['idLog']) || !isset($_POST['content'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Missing parameters: idLog and content are required.'
                ]);
                exit;
            }
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $logId = intval($_POST['idLog']);
        $content = $_POST['content'];
        $tags = isset($_POST['tags']) ? $_POST['tags'] : '';
        $pass = $_POST['pass'] ?? null; 
        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $date = strtotime($date);

        if ($logId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid log ID']);
            exit;
        }

        $logsRepo = new logsRepository();
        $logData = [
            'idUser' => intval($_SESSION['idUser']),
            'date' => $date,
            'tags' => $tags,
            'content' => $pass !== null ? AppUtils::cifrar($content, $pass) : $content,
            'pass' => $pass
        ];

        if ($logsRepo->editLog(array_merge($logData, ['idLog' => $logId]))) {
            echo json_encode(['success' => true, 'message' => 'Log entry updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not update log entry']);
        }
        exit;
    }
}
