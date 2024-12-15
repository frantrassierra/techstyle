<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Solo inicia sesión si no ha sido iniciada previamente
}



function verificarAutenticacion()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login'); // Redirigir a la página de inicio de sesión
        exit();
    }
}

// Verificar si el usuario tiene el rol de administrador
function verificarRol()
{
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        // Si es admin, puede acceder
    } else {
        // Si no es admin, se redirige a la página de acceso denegado
        header('Location: denegado');
        exit();
    }
}
