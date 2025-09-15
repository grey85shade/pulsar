// Popup functions
let categories = []
let psw = '';

function initializeCategoryInput() {
  const categoryInput = document.getElementById("categoryInput")
  const categoryTags = document.getElementById("categoryTags")
  const categoryHidden = document.getElementById("categoryHidden")

  if (!categoryInput || !categoryTags) return

  categoryInput.addEventListener("keydown", function (e) {
    if (e.key === "," || e.key === " " || e.key === "Enter") {
      e.preventDefault()
      const value = this.value.trim()
      if (value && !categories.includes(value)) {
        addCategory(value)
        this.value = ""
      }
    }
  })

  categoryInput.addEventListener("blur", function () {
    const value = this.value.trim()
    if (value && !categories.includes(value)) {
      addCategory(value)
      this.value = ""
    }
  })
}

function addCategory(category) {
  categories.push(category)
  renderCategories()
}

function removeCategory(category) {
  categories = categories.filter((cat) => cat !== category)
  renderCategories()
}

function renderCategories() {
  const categoryTags = document.getElementById("categoryTags")
  const categoryHidden = document.getElementById("categoryHidden")
  if (!categoryTags || !categoryHidden) return

  // Update hidden field with current categories
  categoryHidden.value = categories.join(",")

  // Render visible tags
  categoryTags.innerHTML = categories
    .map(
      (category) => `
    <div class="category-tag">
      ${category}
      <button type="button" class="remove-btn" onclick="removeCategory('${category}')">×</button>
    </div>
  `,
    )
    .join("")
}

function openJournalPopup() {
  document.getElementById("journalPopup").classList.add("active")
  setTimeout(() => {
    initializeCategoryInput()
  }, 100)
}

function openJournalPopupView(id) {
  document.getElementById("journalPopupView").classList.add("active");

  // First check if note has password
  const checkPassData = new FormData();
  checkPassData.append('idNote', id);

  fetch('/ajax/noteHasPass', {
    method: 'POST',
    body: checkPassData
  })
  .then(response => response.json())
  .then(data => {
    
    if (data.hasPass) {
      // Show password prompt
      const password = prompt("This entry is password protected. Please enter the password:");
      if (!password) {
        closePopup('journalPopupView');
        return;
      }
      // Get and display the content with password
      getNoteContent(id, password).then(noteData => {
        displayNoteContent(noteData, id, password);
      });
    } else {
      // Get and display the content without password
      getNoteContent(id).then(noteData => {
        displayNoteContent(noteData, id);
      });
    }
  })
  .catch(error => {
    console.error('Fetch error:', error);
    closePopup('journalPopupView');
  });
}

// Function to get note data
async function getNoteContent(id, password = null) {
  const formData = new FormData();
  formData.append('idNote', id);
  if (password) {
    formData.append('pass', password);
    psw = password; // Store password globally for editing
  }

  try {
    const response = await fetch('/ajax/getNoteById', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.error) {
      throw new Error(data.error);
    }
    
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Function to display the note content
function displayNoteContent(data, id, password = null) {
  try {
    document.getElementById('noteContentView').innerHTML = data.content || '';
    document.getElementById('noteIdView').value = id;

    const tagsHtml = (data.tags || '')
      .split(',')
      .filter(tag => tag.trim())
      .map(tag => `<div class="category-tag">${tag.trim()}</div>`)
      .join('');

    document.getElementById('categoryTagsView').innerHTML = tagsHtml;

    // Format and display the date
    const date = new Date(data.date * 1000);
    const formattedDate = date.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).replace(',', '');
    document.getElementById('noteDateView').textContent = formattedDate;

  } catch (e) {
    console.error("Error displaying note content:", e);
    alert('Error displaying note content');
    closePopup('journalPopupView');
  }
}

function editNote() {
    const id = document.getElementById("noteIdView").value;
    closePopup('journalPopupView');

    if (psw != '') {
        note = getNoteContent(id, psw);
    } else {
        note = getNoteContent(id);
    }

    openJournalPopup();

    note.then(valor => {
        // Set ID first
        document.getElementById("noteIdModify").value = id;

        // Handle content
        if (valor.content) {
            valor.content = valor.content.replace(/<br\s*\/?>/gi, "");
            document.getElementById("noteContent").value = valor.content;
        }
        
        // Handle date
        if (valor.date) {
            let d = new Date(valor.date * 1000);
            let formatted = d.toISOString().slice(0,16);
            document.getElementById("entryDate").value = formatted;
        }

        // Handle tags
        if (valor.tags) {
            // Convert tags string to array
            const tags = valor.tags.split(',').filter(tag => tag.trim());
            
            // Update global categories
            categories = tags;
            
            // Update tags visualization
            document.getElementById("categoryTags").innerHTML = tags.map(tag => `
                <div class="category-tag">
                    ${tag.trim()}
                    <button type="button" class="remove-btn" onclick="removeCategory('${tag.trim()}')">×</button>
                </div>
            `).join("");
            
            // Update hidden field
            document.getElementById("categoryHidden").value = tags.join(",");
        } else {
            // Reset tags if none
            categories = [];
            document.getElementById("categoryTags").innerHTML = "";
            document.getElementById("categoryHidden").value = "";
        }
    }).catch(error => {
        console.error('Error loading note:', error);
        alert('Error loading note content');
    });
}

function deleteNote() {
    const noteId = document.getElementById("noteIdView").value;
    
    if (confirm('Are you sure you want to delete this entry?')) {
        const formData = new FormData();
        formData.append('noteId', noteId);
        
        fetch('/dash/deleteNote', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                closePopup('journalPopupView');
                location.reload();
            } else {
                alert('Error deleting entry');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting entry');
        });
    }
}

let saveTimer;

function autoSave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
        const content = document.getElementById("noteContent").value;
        const idNote = document.getElementById("noteIdModify").value;
        const tags = document.getElementById("categoryHidden").value;
        const date = document.getElementById("entryDate").value;
        const psw = document.getElementById("newNotePass").value;
        const params = new URLSearchParams();
        params.append("content", content);
        params.append("idNote", idNote);
        params.append("tags", tags);
        params.append("date", date);
        params.append("password", psw);
        fetch("/ajax/autoSaveNote", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(response => response.json())
        //.then(data => {console.log(data);})
        .then(data => {
            try {
                //const result = JSON.parse(data);
                if (!data.success) {
                    console.error('AutoSave error:', data.message);
                } else{
                    console.log('AutoSave successful');
                }
            } catch (e) {
                console.error('Error parsing autosave response:', e);
            }
        })
        .catch(error => {
            console.error('AutoSave error:', error);
        });
    }, 1500); // wait 1.5s after stopping typing
}

function searchInNotes(searchTerm) {
    searchTerm = searchTerm.toLowerCase();
    const entries = document.querySelectorAll('.journal-entry');
    
    entries.forEach(entry => {
        // Search in full content
        const fullContent = entry.querySelector('.entry-full-content').textContent.toLowerCase();
        // Search in tags
        const tags = Array.from(entry.querySelectorAll('.entry-tag')).map(tag => tag.textContent.toLowerCase());
        // Search in date
        const date = entry.querySelector('.entry-time').textContent.toLowerCase();
        
        // If there's a match in any field, show the entry
        const matchFound = fullContent.includes(searchTerm) || 
                         tags.some(tag => tag.includes(searchTerm)) || 
                         date.includes(searchTerm);
        
        entry.style.display = matchFound ? 'flex' : 'none';
    });
}

// Close popup when clicking outside
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("popup-overlay")) {
    e.target.classList.remove("active")
    renderCategories()
    document.getElementById("newNoteForm").reset();
    document.getElementById("noteContent").value = "";
    document.getElementById("noteIdModify").value = "";
    document.getElementById("categoryTags").innerHTML = "";
    document.getElementById("categoryHidden").value = "";
  }
})