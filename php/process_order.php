<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    
    if (!$data) {
        echo json_encode(['message' => 'No se recibieron datos válidos']);
       
        http_response_code(400); 
        exit;
    }

    $userId = isset($data['userId']) ? $data['userId'] : null;
    $products = isset($data['cartItems']) ? $data['cartItems'] : null;
    $total = isset($data['cartTotal']) ? $data['cartTotal'] : null;

   
    if (is_null($userId) || is_null($products) || is_null($total)) {
        echo json_encode(['message' => 'Faltan datos necesarios (userId, items del carrito o total).']);
        http_response_code(400); 
        exit;
    }

    try {
        $stmtUser = $pdo->prepare("SELECT nombre, correo FROM usuarios WHERE id = ?");
        $stmtUser->execute([$userId]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo json_encode(['message' => 'Usuario no encontrado con el ID proporcionado.']);
            http_response_code(404); 
        }

        $customerName = $usuario['nombre']; 
        $customerEmail = $usuario['correo']; 

       
         foreach ($products as $item) {
            $productName = isset($item['productName']) ? $item['productName'] : null;
            $quantity = isset($item['quantity']) ? $item['quantity'] : null;
            $price = isset($item['price']) ? $item['price'] : null;

            if (is_null($productName) || is_null($quantity) || is_null($price)) {
                 echo json_encode(['message' => 'Faltan datos en uno de los productos del carrito.']);
                 http_response_code(400); 
                 exit(); 
            }
        }


        $pdo->beginTransaction(); 
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_email, total, status) VALUES (?, ?, ?, ?, 'pendiente')");
        if ($stmt->execute([$userId, $customerName, $customerEmail, $total])) {
            $orderId = $pdo->lastInsertId();
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("Error de DB al insertar pedido: " . print_r($errorInfo, true));
            throw new Exception("Error al insertar el pedido en la tabla 'orders': " . ($errorInfo[2] ?? 'Desconocido'));
        }

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($products as $item) {
            if (!$stmtItem->execute([$orderId, $item['productName'], $item['quantity'], $item['price']])) {
                 $errorInfo = $stmtItem->errorInfo();
                 error_log("Error de DB al insertar item: " . print_r($errorInfo, true));
                 throw new Exception("Error al insertar el producto '" . $item['productName'] . "' en la tabla 'order_items': " . ($errorInfo[2] ?? 'Desconocido'));
            }
        }

        $pdo->commit();
        echo json_encode([
            'status' => 'success',
            'orderNumber' => $orderId,
            'cartTotal' => (float)$total, 
            'status' => 'pendiente' 
        ]);

    } catch (Exception $e) {
       
        if ($pdo->inTransaction()) {
             $pdo->rollBack();
        }
        error_log("Excepción al procesar pedido: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Hubo un error al realizar el pedido. Por favor, intenta de nuevo.',
            'error_detail' => $e->getMessage()
        ]);
         http_response_code(500); 
    }
} else {
    echo json_encode(['message' => 'Método no permitido']);
    http_response_code(405);
}
?>