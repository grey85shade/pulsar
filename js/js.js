function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

function openEditUserModal(userId) {
    fetch('/ajax/getUser/' + userId)
        .then(response => response.json())
        .then(data => {
            if (data && data.id) {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-user').value = data.user;
                document.getElementById('edit-name').value = data.name;
                document.getElementById('edit-surname').value = data.surname;
                document.getElementById('edit-mail').value = data.mail;
                document.getElementById('edit-admin').value = data.admin;
                document.getElementById('edit-password').value = '';
                document.getElementById('editUserModal').style.display = 'block';
            }
        });
}


// Initialize circle charts
document.addEventListener("DOMContentLoaded", () => {
  const circles = document.querySelectorAll(".circle-chart")
  circles.forEach((circle) => {
    const percent = circle.dataset.percent
    const degrees = (percent / 100) * 360
    circle.style.setProperty("--percent", degrees + "deg")
  })
})



function openEventsPopup(isEdit = false) {
  const popup = document.getElementById("eventsPopup");
  popup.classList.add("active");
  
  if (!isEdit) {
    document.getElementById("newEventForm").reset();
    document.getElementById("eventTitle").value = "";
    document.getElementById("eventComments").value = "";
    document.getElementById("eventType").value = "1";
    document.getElementById("eventRepetition").value = "0";
    document.getElementById("eventDate").value = new Date().toISOString().split('T')[0];
    document.getElementById('deleteEventBtn').style.display = 'none';
  }
}

function openEventPopupView(id) {
  const formData = new FormData();
  formData.append('eventId', id);

  fetch('/ajax/getEventById', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      alert('Error loading event');
      return;
    }

    // Llenar el formulario con los datos del evento
    document.getElementById("eventTitle").value = data.titulo;
    document.getElementById("eventDate").value = new Date(data.fecha * 1000).toISOString().split('T')[0];
    document.getElementById("eventType").value = data.tipo;
    document.getElementById("eventRepetition").value = data.repeticion;
    document.getElementById("eventComments").value = data.comentarios || '';

    const form = document.getElementById("newEventForm");
    
    // Agregar/actualizar el campo hidden para el ID
    let eventIdInput = document.getElementById("eventIdModify");
    if (!eventIdInput) {
      eventIdInput = document.createElement('input');
      eventIdInput.type = 'hidden';
      eventIdInput.id = 'eventIdModify';
      eventIdInput.name = 'eventIdModify';
      form.appendChild(eventIdInput);
    }
    eventIdInput.value = id;

    // Agregar/actualizar el campo hidden para indicar que es una actualización
    let updateInput = document.getElementById("eventUpdate");
    if (!updateInput) {
      updateInput = document.createElement('input');
      updateInput.type = 'hidden';
      updateInput.id = 'eventUpdate';
      updateInput.name = 'eventUpdate';
      form.appendChild(updateInput);
    }
    updateInput.value = '1';

    // Abrir el popup
    openEventsPopup(true);
    // Show delete button
    document.getElementById('deleteEventBtn').style.display = 'block';
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error loading event');
  });
}

function deleteEvent() {
    const eventId = document.getElementById("eventIdModify").value;
    
    if (confirm('¿Estás seguro de que deseas eliminar este evento?')) {
        const formData = new FormData();
        formData.append('eventId', eventId);
        
        fetch('/dash/deleteEvent', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                closePopup('eventsPopup');
                window.location.reload();
            } else {
                throw new Error('Error deleting event');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el evento');
        });
    }
}

function openProjectsPopup() {
  document.getElementById("projectsPopup").classList.add("active")
}

function closePopup(popupId) {
  document.getElementById(popupId).classList.remove("active")
  if (popupId === "journalPopup") {
    categories = []
    renderCategories()
    document.getElementById("newLogForm").reset();
    document.getElementById("logContent").value = "";
    document.getElementById("logIdModify").value = "";
    document.getElementById("categoryTags").innerHTML = "";
    document.getElementById("categoryHidden").value = "";
  }
}

// Sidebar navigation
document.addEventListener("click", (e) => {
  if (e.target.closest(".nav-item")) {
    // Remove active class from all nav items
    document.querySelectorAll(".nav-item").forEach((item) => {
      item.classList.remove("active")
    })

    // Add active class to clicked item
    e.target.closest(".nav-item").classList.add("active")
  }
})
