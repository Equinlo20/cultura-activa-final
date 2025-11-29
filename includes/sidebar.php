<nav class="sidebar">
        <ul>
            <li class="menu-header">ADMINISTRACIÓN</li>

            <?php if (hasPermission('ver_dashboard')): ?>
                <li><a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                    <span>Dashboard</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_usuarios')): ?>
                <li><a href="usuarios.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0-5.655-4.06M15 19.128c1.373.1 2.807-.024 4.168-.429a1.69 1.69 0 0 0 1.146-2.115l-.299-1.205c-.328-.131-.67-.234-1.02-.303m-5.654 4.062a9.38 9.38 0 0 1-5.655-4.06M9 19.128c-1.373.1-2.807-.024-4.168-.429a1.69 1.69 0 0 1-1.146-2.115l.299-1.205c.328-.131.67-.234 1.02-.303m10.93-4.072a7.488 7.488 0 0 0-7.482 0m7.482 0a5.25 5.25 0 0 1 1.745.244m-11.967 0a5.25 5.25 0 0 0-1.745.244m11.967 0a7.488 7.488 0 0 0-7.482 0M12 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg>
                    <span>Usuarios</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_asistentes')): ?>
                <li><a href="asistentes.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 1.686 1.686 0 0 0 1.143-2.115l-.297-1.201c-.183-.738-.908-1.258-1.699-1.258H15m-1.741-.479A9.094 9.094 0 0 1 6 18.72m-3.741-.479A1.686 1.686 0 0 1 .857 16.126l-.297-1.201c-.791-.049-1.516-.36-2.098-.823M15 15.75H9m6 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    <span>Asistentes</span>
                </a></li>
            <?php endif; ?>

             <?php if (hasPermission('ver_roles')): ?>
                <li><a href="roles.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                    <span>Roles y Permisos</span>
                </a></li>
            <?php endif; ?>

            <li class="menu-header">EVENTOS</li>

            <?php if (hasPermission('ver_eventos')): ?>
                <li><a href="eventos.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008Zm4.5 0h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg>
                    <span>Gestión de Eventos</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_tipos_ticket')): ?>
                <li><a href="tipos_ticket.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-1.5h.75M5.25 12h.75m0-3h.75M15 6.75V18m0 0a9 9 0 1 0-18 0m18 0a9 9 0 1 1-18 0M5.25 6.75h9.75" /></svg>
                    <span>Tipos de Ticket</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_control_asistencia')): ?>
                <li><a href="control_asistencia.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <span>Control de Asistencia</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_tickets')): ?>
                <li><a href="tickets.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-1.5h5.25m-5.25 0h3m-3 0h-3m-2.25-3h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" /></svg>
                    <span>Tickets</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_patrocinadores')): ?>
                <li><a href="patrocinadores.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.31h5.518a.562.562 0 0 1 .329 1.004l-4.04 2.927a.564.564 0 0 0-.192.585l1.53 4.996a.562.562 0 0 1-.813.626l-4.473-2.31a.562.562 0 0 0-.65 0l-4.473 2.31a.562.562 0 0 1-.813-.626l1.53-4.996a.564.564 0 0 0-.192-.585l-4.04-2.927a.562.562 0 0 1 .329-1.004h5.518a.563.563 0 0 0 .475-.31l2.125-5.11Z" /></svg>
                    <span>Patrocinadores</span>
                </a></li>
            <?php endif; ?>

            <?php if (hasPermission('ver_pagos')): ?>
                <li><a href="pagos.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                    <span>Gestión de Pagos</span>
                </a></li>
            <?php endif; ?>

            <li class="menu-header">ANÁLISIS</li>

            <?php if (hasPermission('ver_estadisticas')): ?>
                <li><a href="reportes.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" /></svg>
                    <span>Estadísticas</span>
                </a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="main-container">