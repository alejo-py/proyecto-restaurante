<?php
// Habilitamos la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluimos la conexión a la base de datos
include 'db.php'; // Asegúrate de que 'db.php' esté configurado correctamente para crear la conexión PDO

// Verificamos si los datos fueron enviados por el frontend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del carrito desde el frontend
    $data = json_decode(file_get_contents('php://input'), true);

    // Comprobamos si los datos están presentes y no están vacíos
    if (!$data) {
        echo json_encode(['message' => 'No se recibieron datos válidos']);
        exit;
    }

    // Extraemos la información del carrito
    $customerName = isset($data['customerName']) ? $data['customerName'] : null;
    $customerEmail = isset($data['customerEmail']) ? $data['customerEmail'] : null;
    $products = isset($data['cartItems']) ? $data['cartItems'] : null;
    $total = isset($data['cartTotal']) ? $data['cartTotal'] : null;

    // Verificamos si los valores necesarios están presentes
    if (is_null($customerName) || is_null($customerEmail) || is_null($products) || is_null($total)) {
        echo json_encode(['message' => 'Faltan datos necesarios']);
        exit;
    }

    try {
        // Iniciamos una transacción
        $pdo->beginTransaction();

        // Insertamos el pedido en la tabla 'orders'
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, total) VALUES (?, ?, ?)");
        if ($stmt->execute([$customerName, $customerEmail, $total])) {
            $orderId = $pdo->lastInsertId(); // Obtenemos el ID del pedido recién insertado
        } else {
            throw new Exception("Error al insertar el pedido en la tabla 'orders'.");
        }

        // Insertamos los productos en la tabla 'order_items'
        foreach ($products as $item) {
            $productName = isset($item['productName']) ? $item['productName'] : null;
            $quantity = isset($item['quantity']) ? $item['quantity'] : null;
            $price = isset($item['price']) ? $item['price'] : null;

            // Verificamos si los elementos del carrito están completos
            if (is_null($productName) || is_null($quantity) || is_null($price)) {
                throw new Exception("Faltan datos en uno de los productos del carrito.");
            }

            // Insertamos el producto en la tabla 'order_items'
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$orderId, $productName, $quantity, $price])) {
                throw new Exception("Error al insertar el producto en la tabla 'order_items'.");
            }
        }

        // Confirmamos la transacción
        $pdo->commit();

        // Enviamos una respuesta exitosa
        echo json_encode([
            'orderNumber' => $orderId, // Corregido: devolver el número de pedido correctamente
            'cartTotal' => $total,
            'status' => 'Realizado' // Estado del pedido
        ]);
    } catch (Exception $e) {
        // Si ocurre un error, revertimos la transacción
        $pdo->rollBack();
        echo json_encode(['message' => 'Hubo un error al realizar el pedido', 'error' => $e->getMessage()]);
    }
}
?>
