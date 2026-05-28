<?php
// StrideOn - Cart Action Handler (AJAX)
// Desenvolvido por: Eng. Software Malvin Manguele
header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSecureSession();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$action = sanitize($_POST['action'] ?? '');
$userId = isLoggedIn() ? (int)$_SESSION['user_id'] : null;
$sessId = getSessionCartId();

// ── Helper: devolve contagem e total atualizados ─────────────
function cartResponse(PDO $pdo, ?int $userId, string $sessId, array $extra = []): string {
    if ($userId) {
        $s = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $s->execute([$userId]);
        $s2 = $pdo->prepare(
            "SELECT SUM(c.quantity * p.price) FROM cart c
             JOIN products p ON c.product_id = p.id WHERE c.user_id = ?"
        );
        $s2->execute([$userId]);
    } else {
        $s = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE session_id = ?");
        $s->execute([$sessId]);
        $s2 = $pdo->prepare(
            "SELECT SUM(c.quantity * p.price) FROM cart c
             JOIN products p ON c.product_id = p.id WHERE c.session_id = ?"
        );
        $s2->execute([$sessId]);
    }
    $count = (int)$s->fetchColumn();
    $total = number_format((float)$s2->fetchColumn(), 0, ',', '.');
    return json_encode(array_merge(
        ['success' => true, 'count' => $count, 'total' => $total],
        $extra
    ));
}

// ── Helper: verifica que o item do carrinho pertence ao utilizador atual ──
// CORREÇÃO IDOR: sem esta verificação qualquer user pode apagar/editar
// itens de outros utilizadores enviando um cart_id arbitrário.
function cartItemBelongsToUser(PDO $pdo, int $cartId, ?int $userId, string $sessId): bool {
    if ($userId) {
        $s = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
        $s->execute([$cartId, $userId]);
    } else {
        $s = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND session_id = ?");
        $s->execute([$cartId, $sessId]);
    }
    return (bool)$s->fetchColumn();
}

switch ($action) {

    // ── ADICIONAR ─────────────────────────────────────────────
    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Produto inválido']);
            exit;
        }

        // Verificar se o produto existe e está ativo
        $s = $pdo->prepare("SELECT id, stock FROM products WHERE id = ? AND active = 1");
        $s->execute([$productId]);
        $prod = $s->fetch();
        if (!$prod) {
            echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
            exit;
        }

        // Verificar se já está no carrinho
        if ($userId) {
            $s = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $s->execute([$userId, $productId]);
        } else {
            $s = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
            $s->execute([$sessId, $productId]);
        }
        $existing = $s->fetch();

        if ($existing) {
            $s = $pdo->prepare("UPDATE cart SET quantity = quantity + 1, updated_at = NOW() WHERE id = ?");
            $s->execute([$existing['id']]);
        } else {
            if ($userId) {
                $s = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                $s->execute([$userId, $productId]);
            } else {
                $s = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)");
                $s->execute([$sessId, $productId]);
            }
        }
        echo cartResponse($pdo, $userId, $sessId);
        break;

    // ── ATUALIZAR QUANTIDADE ──────────────────────────────────
    case 'update':
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $qty    = max(1, (int)($_POST['qty'] ?? 1));

        if (!$cartId) {
            echo json_encode(['success' => false, 'message' => 'Item inválido']);
            exit;
        }

        // CORREÇÃO IDOR: verifica que o item pertence a este utilizador/sessão
        if (!cartItemBelongsToUser($pdo, $cartId, $userId, $sessId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            exit;
        }

        $s = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $s->execute([$qty, $cartId]);

        // Total do item para atualizar a linha na tabela
        $s2 = $pdo->prepare(
            "SELECT c.quantity * p.price FROM cart c
             JOIN products p ON c.product_id = p.id WHERE c.id = ?"
        );
        $s2->execute([$cartId]);
        $itemTotal = number_format((float)$s2->fetchColumn(), 0, ',', '.');

        echo cartResponse($pdo, $userId, $sessId, ['item_total' => $itemTotal]);
        break;

    // ── REMOVER ───────────────────────────────────────────────
    case 'remove':
        $cartId = (int)($_POST['cart_id'] ?? 0);

        if (!$cartId) {
            echo json_encode(['success' => false, 'message' => 'Item inválido']);
            exit;
        }

        // CORREÇÃO IDOR: verifica que o item pertence a este utilizador/sessão
        if (!cartItemBelongsToUser($pdo, $cartId, $userId, $sessId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            exit;
        }

        $s = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $s->execute([$cartId]);
        echo cartResponse($pdo, $userId, $sessId);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
