<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f5f6fa"><meta name="description" content="Aprende física jugando: explora cinco simuladores interactivos de movimiento y fuerzas.">
  <title>Física en juego — laboratorio interactivo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/Frontend/CSS/base.css"><link rel="stylesheet" href="/Frontend/CSS/layout.css"><link rel="stylesheet" href="/Frontend/CSS/components.css"><link rel="stylesheet" href="/Frontend/CSS/simulator.css"><link rel="stylesheet" href="/Frontend/CSS/force-table.css"><link rel="stylesheet" href="/Frontend/CSS/responsive.css"><link rel="stylesheet" href="/Frontend/CSS/darkmode.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <a class="brand" href="#inicio"><span class="brand-mark">φ</span><span>fisica<span class="brand-light">.lab</span></span></a>
    <div class="side-caption">LABORATORIO</div>
    <nav id="topic-nav" aria-label="Temas de física">
      <button class="nav-item active" data-topic="freefall"><span class="nav-icon">↧</span><span>Caída libre</span><span class="nav-dot"></span></button>
      <button class="nav-item" data-topic="mru"><span class="nav-icon">→</span><span>Movimiento rectilíneo</span></button>
      <button class="nav-item" data-topic="forces"><span class="nav-icon">✣</span><span>Mesa de fuerzas</span></button>
      <button class="nav-item" data-topic="projectile"><span class="nav-icon">⌁</span><span>Movimiento parabólico</span></button>
      <button class="nav-item" data-topic="vertical"><span class="nav-icon">↑</span><span>Lanzamiento vertical</span></button>
    </nav>
    <div class="sidebar-bottom"><div class="avatar">FL</div><div><strong>Tu laboratorio</strong><small>Progreso local</small></div><span class="online-dot"></span></div>
  </aside>
  <main class="main-area">
    <header class="topbar"><div class="breadcrumb">Física <span>/</span> <strong id="crumb">Caída libre</strong></div><div class="top-actions"><span class="status-pill"><i></i> Simulador listo</span><button id="theme-toggle" class="icon-button" aria-label="Cambiar tema" title="Cambiar tema">☾</button></div></header>
    <section class="intro" id="inicio"><div class="eyebrow"><span class="eyebrow-line"></span> APRENDE HACIENDO</div><h1>La física se entiende<br>cuando <span>la pones en juego.</span></h1><p>Ajusta las variables, lanza tu experimento y descubre qué pasa.</p></section>
    <section class="workspace">
      <div class="sim-column">
        <div class="section-heading"><div><div class="eyebrow mini">EXPERIMENTO 01 <span id="experiment-count">/ 05</span></div><h2 id="topic-title">Caída libre</h2></div><button id="reset-btn" class="text-button">↻ <span>Reiniciar</span></button></div>
        <div class="sim-card">
          <div class="sim-toolbar"><div class="live-label"><span class="live-dot"></span> PATIO DE JUEGOS</div><div class="toolbar-right"><span id="motion-label">Suelta el objeto</span><span class="toolbar-divider"></span><button id="play-btn" class="play-button" aria-label="Iniciar simulación">▶</button></div></div>
          <div class="canvas-wrap"><canvas id="simulation" aria-label="Escena interactiva del experimento de física"></canvas><div class="canvas-hint" id="canvas-hint">Ajusta los valores y pulsa iniciar</div></div>
          <div class="sim-footer"><span><i class="legend-dot"></i><span id="legend-name">Objeto en movimiento</span></span><span class="sim-time" id="sim-time">t = 0.00 s</span></div>
        </div>
        <section class="results-panel"><div class="results-head"><div><div class="eyebrow mini">BITÁCORA DEL ENSAYO</div><h3>Variables, resultados y fórmulas</h3></div><span class="formula-tag" id="formula-tag">h = ½gt²</span></div><div class="table-scroll"><table class="results-table"><thead><tr><th>Tipo</th><th>Variable</th><th>Valor</th><th>Unidad</th><th>Fórmula usada</th></tr></thead><tbody id="results"><tr class="empty-row"><td colspan="5">Ejecuta el experimento para llenar la tabla.</td></tr></tbody></table></div></section>
      </div>
      <aside class="control-column">
        <div class="control-card"><div class="control-head"><div class="control-icon">⌘</div><div><div class="eyebrow mini">PANEL DE CONTROL</div><h3>Configura el ensayo</h3></div></div><p class="control-description" id="description">Ajusta todas las variables del experimento.</p><div class="unit-bar"><label>Distancia<select id="unit-length"><option value="m">m</option><option value="km">km</option></select></label><label>Velocidad<select id="unit-speed"><option value="m/s">m/s</option><option value="km/h">km/h</option><option value="km/s">km/s</option></select></label><label>Tiempo<select id="unit-time"><option value="s">s</option><option value="min">min</option><option value="h">h</option></select></label></div><form id="controls" class="control-form"></form><div class="gravity-note"><span>∞</span><span>Sin tope superior. Gravedad y masa deben ser positivas.</span><button type="button" id="gravity-info" aria-label="Ayuda de variables">i</button></div><button type="submit" form="controls" class="run-button"><span>▶</span> Ejecutar experimento <kbd>↵</kbd></button></div>
        <div class="insight-card"><div class="insight-icon">✳</div><div><div class="insight-title">PIENSA COMO CIENTÍFICO</div><p id="insight">¿Qué pasaría si duplicas la altura? Cambia el valor y comprueba tu predicción.</p></div></div>
        <div class="progress-card"><div class="progress-top"><span>Tu recorrido</span><strong id="progress-text">0 de 5 temas</strong></div><div class="progress-track"><span id="progress-bar"></span></div><small>Completa un experimento en cada tema.</small></div>
      </aside>
    </section>
    <footer class="page-footer"><span>FÍSICA.LAB <span class="footer-sep">•</span> EXPLORA. EXPERIMENTA. ENTIENDE.</span><span>Un laboratorio para mentes curiosas <span class="footer-heart">✳</span></span></footer>
  </main>
</div>
<div class="toast" id="toast" role="status"></div>
<script src="/Frontend/Scripts/simulations.js" defer></script><script src="/Frontend/Scripts/app.js" defer></script>
</body></html>
