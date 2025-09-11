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

        // Actualizar hidden field
        categoryHidden.value = categories.join(",")
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
  categoryHidden.value = categories.join(",")
  renderCategories()
}

function renderCategories() {
  const categoryTags = document.getElementById("categoryTags")
  if (!categoryTags) return

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

  // First check if log has password
  const checkPassData = new FormData();
  checkPassData.append('idLog', id);

  fetch('/ajax/logHasPass', {
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
      // Obtener y mostrar el contenido con contraseña
      getLogContent(id, password).then(logData => {
        displayLogContent(logData, id, password);
        psw = password;
      });
    } else {
      // Obtener y mostrar el contenido sin contraseña
      getLogContent(id).then(logData => {
        displayLogContent(logData, id);
        psw = '';
      });
    }
  })
  .catch(error => {
    console.error('Fetch error:', error);
    closePopup('journalPopupView');
  });
}

// Función para obtener datos del log
async function getLogContent(id, password = null) {
  const formData = new FormData();
  formData.append('idLog', id);
  if (password) {
    formData.append('pass', password);
  }

  try {
    const response = await fetch('/ajax/getLogById', {
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

// Función para mostrar el contenido del log
function displayLogContent(data, id, password = null) {
  try {
    document.getElementById('logContentView').innerHTML = data.content || '';
    document.getElementById('logIdView').value = id;

    const tagsHtml = (data.tags || '')
      .split(',')
      .filter(tag => tag.trim())
      .map(tag => `<div class="category-tag">${tag.trim()}</div>`)
      .join('');

    document.getElementById('categoryTagsView').innerHTML = tagsHtml;

    // Formatear y mostrar la fecha
    const date = new Date(data.date * 1000);
    const formattedDate = date.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).replace(',', '');
    document.getElementById('logDateView').textContent = formattedDate;

  } catch (e) {
    console.error("Error displaying log content:", e);
    alert('Error displaying log content');
    closePopup('journalPopupView');
  }
}

function editLog() {
    const id = document.getElementById("logIdView").value;
    /*const tags = document.getElementById("categoryTagsView").innerText.split("\n").filter(tag => tag.trim() !== "");
    content = document.getElementById("logContentView").innerHTML; // Usando innerHTML en lugar de innerText
    content = content.replace(/<br\s*\/?>/gi, "");
    const date = document.getElementById("logDate").value; // Obtenemos la fecha del log*/
    closePopup('journalPopupView');

    if (psw != '') {
        log = getLogContent(id, psw);
    } else {
        log = getLogContent(id);
    }

    console.log(log);

    openJournalPopup();

    log.then(valor => {
        // Establecemos el ID primero
        document.getElementById("logIdModify").value = id;

        // Manejamos el contenido
        if (valor.content) {
            valor.content = valor.content.replace(/<br\s*\/?>/gi, "");
            document.getElementById("logContent").value = valor.content;
        }
        
        // Manejamos la fecha
        if (valor.date) {
            let d = new Date(valor.date * 1000);
            let formatted = d.toISOString().slice(0,16);
            document.getElementById("entryDate").value = formatted;
        }

        // Manejamos los tags
        if (valor.tags) {
            // Convertimos el string de tags en array
            const tags = valor.tags.split(',').filter(tag => tag.trim());
            
            // Actualizamos las categorías globales
            categories = tags;
            
            // Actualizamos la visualización de tags
            document.getElementById("categoryTags").innerHTML = tags.map(tag => `
                <div class="category-tag">
                    ${tag.trim()}
                    <button type="button" class="remove-btn" onclick="removeCategory('${tag.trim()}')">×</button>
                </div>
            `).join("");
            
            // Actualizamos el campo hidden
            document.getElementById("categoryHidden").value = tags.join(",");
        } else {
            // Resetear tags si no hay
            categories = [];
            document.getElementById("categoryTags").innerHTML = "";
            document.getElementById("categoryHidden").value = "";
        }
    }).catch(error => {
        console.error('Error cargando el log:', error);
        alert('Error al cargar el contenido del log');
    });
        
    
}

function deleteLog() {
    const logId = document.getElementById("logIdView").value;
    
    if (confirm('¿Estás seguro de que deseas eliminar esta entrada?')) {
        const formData = new FormData();
        formData.append('logId', logId);
        
        fetch('/dash/deleteLog', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                closePopup('journalPopupView');
                window.location.reload(); // Recargar para ver los cambios
            } else {
                throw new Error('Error al eliminar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la entrada');
        });
    }
}

let saveTimer;

function autoSave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
        const content = document.getElementById("logContent").value; // o innerHTML si usas contenteditable
        const idLog = document.getElementById("logIdModify").value;
        const tags = document.getElementById("categoryHidden").value;
        const date = document.getElementById("entryDate").value;
        const psw = document.getElementById("newLogPass").value;
        const params = new URLSearchParams();
        params.append("content", content);
        params.append("idLog", idLog);
        params.append("tags", tags);
        params.append("date", date);
        params.append("password", psw);
        fetch("/ajax/autoSaveLog", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(response => response.text())
        .then(data => {
            /*if(data.success) {
                console.log("Guardado automático: ", data.message);
            } else {
                console.error("Error en guardado automático: ", data.message);
            }*/
           console.log((data));
        })
        .catch(error => {
            console.error("Error en guardado automático: ", error);
        });
    }, 1500); // espera 1,5s después de dejar de escribir
}

// Close popup when clicking outside
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("popup-overlay")) {
    e.target.classList.remove("active")
    renderCategories()
    document.getElementById("newLogForm").reset();
    document.getElementById("logContent").value = "";
    document.getElementById("logIdModify").value = "";
    document.getElementById("categoryTags").innerHTML = "";
    document.getElementById("categoryHidden").value = "";
  }
})
