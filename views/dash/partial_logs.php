<script src="/js/logs.js"></script>

<!-- Journal Block -->
<div class="dashboard-card journal-card">
    <div class="card-header">
        <h2>JOURNAL</h2>
        <button class="add-btn" onclick="openJournalPopup()">+</button>
    </div>
    <div class="card-content scrollable">

    <?php foreach ($logs as $log){ 
        $tags = explode(',', $log['tags']);
        ?>

        <div class="journal-entry" onclick="openJournalPopupView(<?php echo $log['id']; ?>);">
            <div class="entry-line work"></div>
            <div class="entry-content">
                <div class="entry-text">
                    <?php 
                    echo strlen($log['content']) > 100 
                        ? htmlspecialchars(substr($log['content'], 0, 100)) . '...' 
                        : htmlspecialchars($log['content']); 
                    ?>
                </div>
                <div class="entry-tags-time">
                    <?php foreach ($tags as $tag) { ?>
                        <div class="entry-tag work"><?php echo trim($tag); ?></div>
                    <?php } ?>
                    <div class="entry-time"><?php echo date('d/m/Y H:i', $log['date']); ?></div>
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
        <form id="newLogForm" class="popup-form" method="post" action="/dash/newLog">
            <div class="form-group" style="display: flex; gap: 1rem;">
                <div style="flex: 3;">
                    <label>Categories:</label>
                    <div class="category-input-container">
                        <div class="category-tags" id="categoryTags"></div>
                        <input type="text" id="categoryInput" placeholder="Type categories and press comma or space..." class="category-input">
                        <input type="hidden" id="categoryHidden" name="logTags">
                        <input type="hidden" id="logIdModify" name="logIdModify" >
                    </div>
                </div>
                <div style="flex: 1;">
                    <label>Date:</label>
                    <input 
                        type="datetime-local" 
                        id="entryDate" 
                        name = "logDate"
                        class="date-input w225"
                        value="<?php echo date('Y-m-d\TH:i'); ?>" 
                    >
                </div>
                <div style="flex: 1;">
                    <label>Password</label>
                    <input 
                        type="password" 
                        id="newLogPass" 
                        name = "newLogPass"
                        class="date-input w225"
                    >
                </div>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea id="logContent" name="logContent" oninput="autoSave();" placeholder="What happened today?"></textarea>
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
            <input type="hidden" id="logIdView">
            <input type="hidden" id="logDate">
            
            <button class="close-btn" onclick="closePopup('journalPopupView')">&times;</button>
        </div>
        <form class="popup-form">
            <div class="form-group">
                <div class="entry-header" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="category-tags" id="categoryTagsView" style="flex: 1;"></div>
                    <div class="entry-time" id="logDateView" style="color: #666; font-size: 0.9rem;"></div>
                </div>
            </div>
            <div class="form-group">
                <div id="logContentView"></div>
            </div>
            <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="action-buttons" style="display: flex; gap: 1rem;">
                    <button type="button" onclick="editLog()" class="action-btn">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" onclick="deleteLog()" class="action-btn delete">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
                <button type="button" onclick="closePopup('journalPopupView')">Close</button>
            </div>
        </form>
    </div>
</div>