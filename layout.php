<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script type="text/javascript" src="/js/js.js"></script>
    <link rel="stylesheet" id="global-css" href="/css/style.css" type="text/css" media="all" />
    <title>Pulsar Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <!--div class="logo-icon"><i class="bi bi-person-vcard"></i></div-->
                    <img src="/img/logo3-small.png" alt="Pulsar Logo" class="logo-image">
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item">
                    <i class="bi bi-grid-1x2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-bank"></i>
                    <span class="nav-text">Finanzas</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Systems</span>
                </a>
            </nav>
        </aside>





            


            <?php
                if (isset($_SESSION['flash'])) {
                    $flash = $_SESSION['flash'];
                    unset($_SESSION['flash']);
            ?>
                <div class="flash-message <?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                    <button onclick="this.parentElement.style.display='none';" class="close-flash">&times;</button>
                </div>
            <?php } ?>




        <!-- Main Content -->
        <main class="main-content">

                <?php 
                    $controllerInstance = new $controller;
                    //$controllerInstance->$action();
                    if (!empty($urlVariable)) {
                        $controllerInstance->$action($urlVariable);
                    } else {
                        $controllerInstance->$action();
                    }
                ?>
                
            
            
        </main>
    </div>

    <!-- Popups -->
    <!-- Actualizando popup de journal con campo de categorías tipo Gmail -->
    

    

    <div id="projectsPopup" class="popup-overlay">
        <div class="popup-content wide-popup">
            <div class="popup-header">
                <h3>Add Project</h3>
                <button class="close-btn" onclick="closePopup('projectsPopup')">&times;</button>
            </div>
            <form class="popup-form">
                <div class="form-group">
                    <label>Project Name:</label>
                    <input type="text" placeholder="Project name">
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select>
                        <option value="not-started">NOT STARTED</option>
                        <option value="in-progress">IN PROGRESS</option>
                        <option value="completed">COMPLETED</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Due Date:</label>
                    <input type="date">
                </div>
                <div class="form-group">
                    <label>Team Size:</label>
                    <input type="number" placeholder="Number of team members">
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closePopup('projectsPopup')">Cancel</button>
                    <button type="submit">Add Project</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
