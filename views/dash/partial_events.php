<div class="dashboard-card events-card">
    <div class="card-header">
        <h2>UPCOMING EVENTS</h2>
        <button class="add-btn" onclick="openEventsPopup()">+</button>
    </div>
    <div class="card-content scrollable">
        <!-- Añadiendo 3 gráficos circulares en una sola línea -->
        <div class="events-circles">
            <?php foreach ($events as $event) { 

                if($event['tipo'] == 0 ){
                    $todayMidnight = strtotime('today');
                    $eventMidnight = strtotime(date('Y-m-d', $event['fecha']));
                    
                    $daysLeft = ($eventMidnight - $todayMidnight) / 86400;
                    $percent = max(0, min(100, ($daysLeft * 100) / 365));   
                ?>
                
            <div class="event-circle" onclick="openEventPopupView(<?php echo $event['id']; ?>)">
                <div class="circle-chart" data-percent="<?php echo $percent; ?>">
                    <div class="circle-number"><?php echo $daysLeft; ?></div>
                </div>
                <div class="circle-label"><?php echo $event['titulo']; ?></div>
            </div>
            <?php }}?>
            
        </div>
        <div class="events-bars">
            <?php foreach ($events as $event) { 
                    $todayMidnight = strtotime('today');
                    $eventMidnight = strtotime(date('Y-m-d', $event['fecha']));
                    
                    $daysLeft = ($eventMidnight - $todayMidnight) / 86400;
                    $percent = max(0, min(100, ($daysLeft * 100) / 365));   
                ?>
            <div class="event-bar" onclick="openEventPopupView(<?php echo $event['id']; ?>)">
                <div class="bar-info">
                    <span class="bar-number"><?php echo $daysLeft; ?></span>
                    <span class="bar-label fecha"><?php echo date('d/m/Y H:i', $event['fecha']); ?></span>
                    <span class="bar-label"><?php echo $event['titulo']; ?></span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $percent; ?>%"></div>
                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>

<!-- Actualizando popup de events con campo de repetición -->
    <div id="eventsPopup" class="popup-overlay">
        <div class="popup-content wide-popup">
            <div class="popup-header">
                <h3>Add Event</h3>
                <div class="popup-header-buttons">
                    <button type="button" id="deleteEventBtn" class="delete-btn" style="display: none; margin-right: 10px; background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" onclick="deleteEvent()">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <button class="close-btn" onclick="closePopup('eventsPopup')">&times;</button>
                </div>
            </div>
            <form class="popup-form" method="post" action="/dash/newEvent" id="newEventForm">
                <input type="hidden" id="eventIdModify" name="eventIdModify" value="">
                <input type="hidden" id="eventUpdate" name="eventUpdate" value="">
                <div class="form-group">
                    <label>Event Name:</label>
                    <input type="text" 
                           name="eventTitle" 
                           id="eventTitle" 
                           placeholder="e.g., Birthday, Conference" 
                           required>
                </div>
                <div class="form-group" style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label>Date:</label>
                        <input type="date" 
                               name="eventDate" 
                               id="eventDate" 
                               value="<?php echo date('Y-m-d'); ?>"
                               required>
                    </div>
                    <div style="flex: 1;">
                        <label>Display Type:</label>
                        <select name="eventType" id="eventType" required>
                            <option value="1">Linear</option>    
                            <option value="0">Circular</option>
                        </select>
                    </div>
                
                    <div style="flex: 1;">
                        <label>Repetition:</label>
                        <select name="eventRepetition" id="eventRepetition">
                            <option value="0">No repetition</option>
                            <option value="1">Diario</option>
                            <option value="2">Semanal</option>
                            <option value="3">Bisemanal</option>
                            <option value="4">Mensual</option>
                            <option value="5">Anual</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Comments:</label>
                    <textarea name="eventComments" 
                              id="eventComments" 
                              placeholder="Add any additional details about the event"
                              rows="4"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closePopup('eventsPopup')">Cancel</button>
                    <button type="submit">Add Event</button>
                </div>
            </form>
        </div>
    </div>