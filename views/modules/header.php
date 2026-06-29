<!-- Header.php -->
<head>
    <!-- Estilos base siempre presentes -->
    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="../assets/css/header.php.css">
    
    <!-- Estilo condicional: Solo carga catalogo.css si estamos en esa página -->
    <?php if(basename($_SERVER['PHP_SELF']) == 'catalogo.php'): ?>
        <link rel="stylesheet" href="../assets/css/catalogo.php.css">
    <?php endif; ?>
</head>