

<!-- Journal Block -->
<div class="dashboard-card journal-card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2>JOURNAL</h2>
            <div style="flex: 1; margin: 0 20px;">
                <input type="text" 
                       id="searchNotes" 
                       placeholder="Search in notes..." 
                       style="width: 100%; padding: 5px; border-radius: 4px; border: 1px solid #ddd;"
                       oninput="searchInNotes(this.value)">
            </div>
            <button class="add-btn" onclick="openJournalPopup()">+</button>
        </div>
    </div>
    <div class="card-content scrollable">

    <?php foreach ($notes as $note){ 
        $tags = explode(',', $note['tags']);
        ?>

        <div class="journal-entry" onclick="openJournalPopupView(<?php echo $note['id']; ?>);">
            <div class="entry-line work"></div>
            <div class="entry-content">
                <div class="entry-text">
                    <?php 
                    echo strlen($note['content']) > 100 
                        ? htmlspecialchars(substr($note['content'], 0, 100)) . '...' 
                        : htmlspecialchars($note['content']); 
                    ?>
                </div>
                <div class="entry-full-content" style="display: none;">
                    <?php echo htmlspecialchars($note['content']); ?>
                </div>
                <div class="entry-tags-time">
                    <?php foreach ($tags as $tag) { ?>
                        <div class="entry-tag work"><?php echo trim($tag); ?></div>
                    <?php } ?>
                    <div class="entry-time"><?php echo date('d/m/Y H:i', $note['date']); ?></div>
                </div>
            </div>
        </div>
    <?php } ?>

        
    </div>
</div>

<div id="journalPopup" class="popup-overlay">
    <div class="popup-content wide-popup">
        <div class="popup-header">
            <h3>Add Journal Entry</h3>
            <button class="close-btn" onclick="closePopup('journalPopup')">&times;</button>
        </div>
        <form id="newNoteForm" class="popup-form" method="post" action="/dash/newNote">
            <div class="form-group" style="display: flex; gap: 1rem;">
                <div style="flex: 3;">
                    <label>Categories:</label>
                    <div class="category-input-container">
                        <div class="category-tags" id="categoryTags"></div>
                        <input type="text" id="categoryInput" placeholder="Type categories and press comma or space..." class="category-input">
                        <input type="hidden" id="categoryHidden" name="noteTags">
                        <input type="hidden" id="noteIdModify" name="noteIdModify" >
                    </div>
                </div>
                <div style="flex: 1;">
                    <label>Date:</label>
                    <input 
                        type="datetime-local" 
                        id="entryDate" 
                        name = "noteDate"
                        class="date-input w225"
                        value="<?php echo date('Y-m-d\TH:i'); ?>" 
                    >
                </div>
                <div style="flex: 1;">
                    <label>Password</label>
                    <input 
                        type="password" 
                        id="newNotePass" 
                        name = "newNotePass"
                        class="date-input w225"
                    >
                </div>
            </div>
            <div class="form-group">
                <label>Description:</label>
            
            <input type="hidden" id="noteContent" name="noteContent">
                <div id="editor" class="editor-nota">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" onclick="closePopup('journalPopup')">Cancel</button>
                <button type="submit">Add Entry</button>
            </div>
        </form>
    </div>
</div>

<div id="journalPopupView" class="popup-overlay">
    <div class="popup-content wide-popup">
        <div class="popup-header">
            <h3>Journal Entry</h3>
            <input type="hidden" id="noteIdView">
            <input type="hidden" id="noteDate">
            
            <button class="close-btn" onclick="closePopup('journalPopupView')">&times;</button>
        </div>
        <form class="popup-form">
            <div class="form-group">
                <div class="entry-header" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="category-tags" id="categoryTagsView" style="flex: 1;"></div>
                    <div class="entry-time" id="noteDateView" style="color: #666; font-size: 0.9rem;"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="ql-bubble" style="margin-top: -56px;">
                    <div class="ql-editor">
                        <div id="noteContentView"></div>
                    </div>
                </div>
            </div>
            <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="action-buttons" style="display: flex; gap: 1rem;">
                    <button type="button" onclick="editNote()" class="action-btn">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" onclick="deleteNote()" class="action-btn delete">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
                <button type="button" onclick="closePopup('journalPopupView')">Close</button>
            </div>
        </form>
    </div>
</div>

<script>
    
var toolbarOptions = [
  ['bold', 'italic'],
  ['blockquote', 'code-block'],
  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
  [{ 'size': ['small', false, 'large', 'huge'] }],
  [{ 'color': [] }],
  ['link',],
  ['clean']
];

var quill = new Quill('#editor', {
  theme: 'snow',
  modules: {
    toolbar: toolbarOptions
  }
});


    // Antes de enviar el formulario, volcamos el contenido
    document.querySelector('form').onsubmit = function() {
        document.querySelector('#noteContent').value = quill.root.innerHTML;
    };
    
    quill.on('text-change', function() {
        document.querySelector('#noteContent').value = quill.root.innerHTML;
        autoSave();
    });
</script>
<script src="/js/notes.js"></script>