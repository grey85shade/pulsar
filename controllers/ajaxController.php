<?php

include_once ("models/notesRepository.php");
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


     public function getNoteById ()
    {
        $idNote = $_POST['idNote'];
        if (isset($_POST['pass'])) {
            $pass = $_POST['pass'];
        } else {
            $pass = null;
        }

        $notesRepo = new notesRepository();
        $note = $notesRepo->getNoteById($idNote);
    
        if ($note) {
            if ($pass !== null && $note['pass'] != '') {
                $note['content'] = AppUtils::descifrar($note['content'], $pass);
            }
           $note['content'] = AppUtils::renderSafeHtml($note['content']);
            echo json_encode($note, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
        }
        exit;
    }

    public function noteHasPass()
    {
        $idnote = $_POST['idNote'];
        $notesRepo = new notesRepository();
        $note = $notesRepo->getNoteById($idnote);
    
        if ($note) {
            if ($note['pass'] != '' && $note['pass'] != 0) {
                echo json_encode(['hasPass' => true]);
            } else {
                echo json_encode(['hasPass' => false]);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
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

    public function autoSaveNote()
    {
        if (!isset($_SESSION['idUser']) || !isset($_POST['idNote'], $_POST['content'])) {

            if (!isset($_POST['idNote']) || !isset($_POST['content'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Missing parameters: idnote and content are required.'
                ]);
                exit;
            }

        }

        $noteId = intval($_POST['idNote']);
        $content = $_POST['content'];
        $tags = isset($_POST['tags']) ? $_POST['tags'] : '';
        $pass = (!empty($_POST['password'])) ? $_POST['password'] : null;
        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $date = strtotime($date);

        if ($noteId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid note ID']);
            exit;
        }
        
        
        $notesRepo = new notesRepository();
        $noteData = [
            'idUser' => intval($_SESSION['idUser']),
            'date' => $date,
            'tags' => $tags,
            'content' => $pass !== null ? AppUtils::cifrar($content, $pass) : $content,
            'pass' => $pass
        ];

        if ($notesRepo->editNote(array_merge($noteData, ['idNote' => $noteId]))) {
            echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not update note']);
        }
        exit;
    }
}
