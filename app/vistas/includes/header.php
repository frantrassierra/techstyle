<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <link rel="icon" href="<?php echo BASE_URL; ?>images/fevicon/favicon.JPG" type="image/gif" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>HandTime</title>

  <!-- Bootstrap core CSS -->

  <link rel="stylesheet" href="css/bootstrap.css">

  <!-- Font Awesome style -->
  <link rel="stylesheet" href="css/font-awesome.min.css">

  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="css/style.css">

  <link rel="stylesheet" href="css/catalogo.css?v=1.0">

  <!-- Responsive style -->
  <!-- CSS de Bootstrap -->
  <link rel="stylesheet" href="css/responsive.css">

  <link rel="stylesheet" href="css/sweetalert2.min.css">


</head>

<body>

  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="inicio">
            <img src="images/logotipo-removebg-preview.png" style="height: 40px;" alt="">
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item">
                <!--            <a class="nav-link" href="index.php?accion=inicio">Inicio <span class="sr-only">(current)</span></a>
 -->
                <a class="nav-link" href="inicio">Inicio <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="sobrenosotros">Sobre Nosotros</a>


                <!--             <a class="nav-link" href="index.php?accion=sobrenosotros">Sobre Nosotros</a>
 -->

              </li>

              <li class="nav-item">
                <a class="nav-link" href="mostrarProductos">Catálogo</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="contacta">Contacta con nosotros</a>
              </li>


              <?php

              if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li class="nav-item">
                  <a class="nav-link" href="adminPanel">Admin panel</a>
                  </a>
                </li>
              <?php endif; ?>


            </ul>

            <div class="user_optio_box">
              <?php if (isset($_SESSION['usuario_id'])): ?>
                <!-- Si el usuario ha iniciado sesión, mostrar opciones de usuario -->

                <a href="perfil">
                  <i class="fa fa-user" aria-hidden="true"></i> Perfil
                </a>
                <a href="carrito">
                  <i class="fa fa-shopping-cart" aria-hidden="true"></i> Carrito
                </a>
                <a href="logout">
                  <i class="fa fa-sign-out" aria-hidden="true"></i> Cerrar Sesión
                </a>


              <?php else: ?>
                <!-- Si el usuario no ha iniciado sesión, mostrar opciones de login y registro -->
                <a href="login">
                  <i class="fa fa-user" aria-hidden="true"> Iniciar sesión</i>
                </a>
                <a href="registro">
                  <i class="fa fa-user-plus" aria-hidden="true"> Registrar</i>
                </a>


                </li>
              <?php endif; ?>


            </div>
          </div>
        </nav>
      </div>


    </header>