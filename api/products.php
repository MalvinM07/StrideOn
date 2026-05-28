<?php
// ============================================================
// StrideOn - API JSON: /api/products.php
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Methods: GET');

require_once '../includes/config.php';
require_once '../includes/functions.php';

// ── CORS: em produção restringe origens, em dev permite tudo ─
$allowedOrigins = [
    'http://localhost',
    'http://localhost/strideon',
    SITE_URL,
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (APP_ENV === 'production') {
    if (in_array($origin, $allowedOrigins, true)) {
        header("Access-Control-Allow-Origin: $origin");
        header('Vary: Origin');
    }
    // Sem CORS header = pedido bloqueado pelo browser (comportamento correto)
} else {
    // Desenvolvimento: permite tudo para facilitar testes
    header('Access-Control-Allow-Origin: *');
}

try {
    $pdo = getDB();

    $category = isset($_GET['category']) ? sanitize($_GET['category']) : null;
    $featured  = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;
    $limit    = min(100, max(1, (int)($_GET['limit'] ?? 50)));

    $where  = ['active = 1'];
    $params = [];

    if ($category) {
        $where[]  = 'category = ?';
        $params[] = $category;
    }
    if ($featured !== null) {
        $where[]  = 'featured = ?';
        $params[] = (int)$featured;
    }

    // Ordenação segura por whitelist (previne SQL injection via ORDER BY)
    $allowedSorts = ['featured', 'price', 'name', 'created_at'];
    $sortField    = in_array($_GET['sort'] ?? '', $allowedSorts, true)
                    ? $_GET['sort']
                    : 'featured';

    $sql = "SELECT id, name, description, price, category, image, stock, featured, created_at
            FROM products
            WHERE " . implode(' AND ', $where) . "
            ORDER BY $sortField DESC, created_at DESC
            LIMIT ?";
    $params[] = $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    foreach ($products as &$p) {
        $p['price']           = (float)$p['price'];
        $p['price_formatted'] = number_format($p['price'], 2, '.', '') . ' ' . CURRENCY;
        $p['image_url']       = SITE_URL . '/assets/images/' . $p['image'];
        $p['featured']        = (bool)$p['featured'];
    }

    echo json_encode([
        'success'  => true,
        'count'    => count($products),
        'currency' => CURRENCY,
        'data'     => $products,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    // Em produção não expõe detalhes do erro
    echo json_encode(['success' => false, 'error' => 'Erro interno do servidor']);
}
