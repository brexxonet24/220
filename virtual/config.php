<?php
define('ROOT', __DIR__);
define('DATA_DIR', ROOT . '/data');
define('TRACKING_DIR', DATA_DIR . '/tracking');
define('CALIF_DIR', DATA_DIR . '/calificaciones');
define('CAL_DIR', DATA_DIR . '/calendario');
define('UPLOADS_DIR', ROOT . '/uploads');
define('SITE_NAME', 'Instituto Superior 220 AMET');
define('SITE_SHORT', 'AMET 220');

// ── BASE_URL ─────────────────────────────────────────────────────────────────
// Si el sistema está en una subcarpeta, definir aquí el prefijo.
// Ejemplos:
//   En raíz (tudominio.com/)          → define('BASE_URL', '');
//   En subcarpeta (virtual.isft220.edu.ar/virtual/) → define('BASE_URL', '');
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

foreach ([DATA_DIR, TRACKING_DIR, CALIF_DIR, CAL_DIR, UPLOADS_DIR] as $d) {
    if (!is_dir($d)) @mkdir($d, 0755, true);
}

// ── JSON helpers ──────────────────────────────────────────────────────────────
function jRead(string $file): array {
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}
function jWrite(string $file, array $data): void {
    $dir = dirname($file);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
function nextId(array $arr): int {
    return empty($arr) ? 1 : max(array_column($arr, 'id')) + 1;
}

// ── Entidades ─────────────────────────────────────────────────────────────────
function getUsuarios(): array    { return jRead(DATA_DIR . '/usuarios.json'); }
function getMaterias(): array    { return jRead(DATA_DIR . '/materias.json'); }
function getActividades(): array { return jRead(DATA_DIR . '/actividades.json'); }
function saveUsuarios(array $d)    { jWrite(DATA_DIR . '/usuarios.json', $d); }
function saveMaterias(array $d)    { jWrite(DATA_DIR . '/materias.json', $d); }
function saveActividades(array $d) { jWrite(DATA_DIR . '/actividades.json', $d); }

function findUser(int $id): ?array {
    foreach (getUsuarios() as $u) if ((int)$u['id'] === $id) return $u;
    return null;
}
function findMateria(int $id): ?array {
    foreach (getMaterias() as $m) if ((int)$m['id'] === $id) return $m;
    return null;
}

function getTracking(int $uid, int $aid): array {
    if (!is_dir(TRACKING_DIR)) @mkdir(TRACKING_DIR, 0755, true);
    return jRead(TRACKING_DIR . "/{$uid}_{$aid}.json");
}
function saveTracking(int $uid, int $aid, array $d) { if(!is_dir(TRACKING_DIR)) @mkdir(TRACKING_DIR,0755,true); jWrite(TRACKING_DIR . "/{$uid}_{$aid}.json", $d); }
function getCalif(int $mid): array    { if(!is_dir(CALIF_DIR)) @mkdir(CALIF_DIR,0755,true); return jRead(CALIF_DIR . "/{$mid}.json"); }
function saveCalif(int $mid, array $d) { if(!is_dir(CALIF_DIR)) @mkdir(CALIF_DIR,0755,true); jWrite(CALIF_DIR . "/{$mid}.json", $d); }
function getCal(int $mid): array      { if(!is_dir(CAL_DIR)) @mkdir(CAL_DIR,0755,true); return jRead(CAL_DIR . "/{$mid}.json"); }
function saveCal(int $mid, array $d)  { if(!is_dir(CAL_DIR)) @mkdir(CAL_DIR,0755,true); jWrite(CAL_DIR . "/{$mid}.json", $d); }

// ── Utils ─────────────────────────────────────────────────────────────────────
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function redirect(string $url): void {
    // Siempre anteponer BASE_URL si la URL no lo incluye ya
    if ($base !== '' && strpos($url, $base) !== 0) {
        $url = $base . $url;
    }
    header("Location: $url");
    exit;
}

function flash(string $msg, string $type = 'success'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
function fmtMinutos(int $segundos): string {
    $m = intdiv($segundos, 60);
    $s = $segundos % 60;
    return "{$m}m " . str_pad($s, 2, '0', STR_PAD_LEFT) . "s";
}
function tipoBadge(string $tipo): array {
    if ($tipo === 'lectura')      return ['Lectura',      'badge-blue'];
    if ($tipo === 'cuestionario') return ['Cuestionario', 'badge-green'];
    if ($tipo === 'video')        return ['Video',        'badge-coral'];
    if ($tipo === 'consigna')     return ['Consigna',     'badge-purple'];
    if ($tipo === 'codigo')       return ['Código',       'badge-amber'];
    return [$tipo, 'badge-gray'];
}

if (session_status() === PHP_SESSION_NONE) session_start();
