<header class="main-header">
    <h1>DASHBOARD</h1>
</header>

<div class="dashboard-grid">
    <!-- Agrupando los dos primeros bloques en un contenedor con altura fija -->
    <div class="dashboard-top">
        <?php
            include "partial_logs.php";
        ?>
        <!-- Events Block -->
         <?php
            include "partial_events.php";
        ?>
        
    </div>

    <!-- Projects Block -->
    <div class="dashboard-card projects-card">
        <div class="card-header">
            <h2>PROJECTS</h2>
            <button class="add-btn" onclick="openProjectsPopup()">+</button>
        </div>
        <div class="card-content">
            <div class="projects-grid">
                <div class="project-card">
                    <div class="project-header">
                        <h3>Website Redesign</h3>
                        <span class="project-status in-progress">IN PROGRESS</span>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 75%"></div>
                        </div>
                        <span class="progress-text">75%</span>
                    </div>
                    <div class="project-details">
                        <div class="project-detail">
                            <span class="detail-label">Due:</span>
                            <span class="detail-value">Dec 15, 2024</span>
                        </div>
                        <div class="project-detail">
                            <span class="detail-label">Team:</span>
                            <span class="detail-value">5 members</span>
                        </div>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-header">
                        <h3>Mobile App</h3>
                        <span class="project-status completed">COMPLETED</span>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 100%"></div>
                        </div>
                        <span class="progress-text">100%</span>
                    </div>
                    <div class="project-details">
                        <div class="project-detail">
                            <span class="detail-label">Completed:</span>
                            <span class="detail-value">Nov 20, 2024</span>
                        </div>
                        <div class="project-detail">
                            <span class="detail-label">Team:</span>
                            <span class="detail-value">3 members</span>
                        </div>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-header">
                        <h3>Marketing Strategy</h3>
                        <span class="project-status not-started">NOT STARTED</span>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0%</span>
                    </div>
                    <div class="project-details">
                        <div class="project-detail">
                            <span class="detail-label">Start:</span>
                            <span class="detail-value">Jan 10, 2025</span>
                        </div>
                        <div class="project-detail">
                            <span class="detail-label">Team:</span>
                            <span class="detail-value">4 members</span>
                        </div>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-header">
                        <h3>Database Migration</h3>
                        <span class="project-status in-progress">IN PROGRESS</span>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 40%"></div>
                        </div>
                        <span class="progress-text">40%</span>
                    </div>
                    <div class="project-details">
                        <div class="project-detail">
                            <span class="detail-label">Due:</span>
                            <span class="detail-value">Jan 30, 2025</span>
                        </div>
                        <div class="project-detail">
                            <span class="detail-label">Team:</span>
                            <span class="detail-value">2 members</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>