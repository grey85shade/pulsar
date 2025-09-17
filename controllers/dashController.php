<?php

include_once ("models/eventsRepository.php");
//include_once ("AppUtils.php");
class dashController
{
    private $notesService;

    public function __construct() {
        include_once('services/notesService.php');
        $this->notesService = new notesService();
    }
    public function index ()
    {
     
        $notes = $this->notesService->getAllNotes();
        $events = $this->getCurrentEvents();
        
        require_once ("views/dash/index.php");

    }

    public function newNote () 
    {
        if (!isset($_SESSION['idUser']) || 
            !isset($_POST['noteTags']) || 
            !isset($_POST['noteDate']) || 
            !isset($_POST['noteContent'])) {
            AppUtils::setFlash('Datos incompletos', 'error');
            header('Location: /dash');
            exit;
        }
        $idUser = $_SESSION['idUser'];
        $tags = trim($_POST['noteTags']);
        $date = trim($_POST['noteDate']);
        $content = trim($_POST['noteContent']);
        $pass = (!empty($_POST['newNotePass'])) ? $_POST['newNotePass'] : null;
        $idNote = (!empty($_POST['noteIdModify'])) ? $_POST['noteIdModify'] : null;

        $response = json_decode($this->notesService->newNote($idUser, $idNote, $content, $date, $tags,  $pass), true);

        if ($response['error']) {
            AppUtils::setFlash($response['error'], 'error');
        } else {
            AppUtils::setFlash('Journal entry saved successfully.', 'success');
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

    public function deleteNote()
    {
        if (!isset($_SESSION['idUser']) || !isset($_POST['noteId'])) {
            AppUtils::setFlash('Error deleting record', 'error');
            header('Location: /dash/index');
            exit;
        }
        
        $response = json_decode($this->notesService->deleteNote($_POST['noteId']), true);
        
        if ($response['success']) {
            AppUtils::setFlash('Note deleted successfully.', 'success');
        } else {
            AppUtils::setFlash('Could not delete the note.', 'error');
        }
        
        exit;
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
