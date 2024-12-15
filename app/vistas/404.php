<?php include '../app/vistas/includes/header.php'; ?>

<?php
// 404.php - Página de error 404

// Establecer el encabezado de respuesta HTTP como 404
http_response_code(404);

?>

</head>
<body>

<div class="error-container">
    <h1 class="error-title">404</h1>
    <p class="error-message">La página que buscas no se encuentra.</p>
    <p class="error-details">La URL que has intentado acceder no existe o ha sido movida.</p>
    <a href="inicio" class="back-link">Volver a la página de inicio</a>
</div>

<?php include '../app/vistas/includes/footer.php'; ?>

</body>
</html>
