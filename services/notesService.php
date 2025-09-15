<?php

include_once ("models/notesRepository.php");
include_once ("models/eventsRepository.php");

class notesService
{
    public function newNote ($idUser, $idNote, $content, $date, $tags,  $pass) 
    {
        if (!isset($_SESSION['idUser']) || 
            !$content ||
            $content === ''
            ) {
            return json_encode(['error' => 'Datos incompletos']);
        }

        $tags = trim($tags);
        $date = trim($date);
        $content = trim($content);

        if (empty($content) || 
            strlen($tags) > 200 || 
            strlen($content) > 4800) {
            return json_encode(['error' => 'Datos no validos']);
        }

        if ($date != '') {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return json_encode(['error' => 'Fecha incorrecta']);
            }
        }
        
        if ($pass != '') {
            $P = true;
            $content = AppUtils::cifrar($content, $pass);
        } else {
            $P = null;
        }

        $noteData = [
            'idUser' => $_SESSION['idUser'],
            'date' => $timestamp,
            'tags' => $tags,
            'content' => $content,
            'pass' => $P,
            'idNote' => $idNote
        ];

        $notesRepo = new notesRepository();
        if ($idNote !== null) {
            $notesRepo->editNote($noteData);
        } else {
            $notesRepo->addNote($noteData);
        }

        return json_encode(['success' => true]);
    }

    public function deleteNote($idNote)
    {
        
        $idNote = intval($idNote);
        if ($idNote <= 0) {
            return json_encode(['error' => 'Id not valid']);
        }

        $notesRepo = new notesRepository();
        $result = $notesRepo->deleteNote($idNote, $_SESSION['idUser']);

        if ($result) {
            return json_encode(['success' => 'Note deleted successfully.']);
        } else {
            return json_encode(['error' => 'Something went wrong']);
        }
    }

    public function getAllNotes ()
    {
        if (isset($_SESSION['idUser'])) {
            $idUser = $_SESSION['idUser'];
        } else {
            header('Location: /login');
            exit;
        }

        $notesRepo = new notesRepository();
        return $notesRepo->getAllNotes($idUser);
    }
}