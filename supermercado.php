<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "super_mercado";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Función para ejecutar procedimientos almacenados sin retorno de datos
function ejecutarProcedimiento($query, $params) {
    global $conn;
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $stmt->close();
}

// Función para ejecutar procedimientos almacenados que devuelven datos
function obtenerDatos($query, $params) {
    global $conn;
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Manejo de solicitudes POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"];
    switch ($accion) {
        case "InsertarCategoria":
            ejecutarProcedimiento("CALL InsertarCategoria(?)", [$_POST["nombre_categoria"]]);
            break;
        case "InsertarCliente":
            ejecutarProcedimiento("CALL InsertarCliente(?, ?, ?)", [$_POST["nombre"], $_POST["email"], $_POST["telefono"]]);
            break;
        case "ActualizarPrecioProducto":
            ejecutarProcedimiento("CALL ActualizarPrecioProducto(?, ?)", [$_POST["id_producto"], $_POST["nuevo_precio"]]);
            break;
        case "InsertarEmpleado":
            ejecutarProcedimiento("CALL InsertarEmpleado(?, ?, ?)", [$_POST["nombre"], $_POST["puesto"], $_POST["salario"]]);
            break;
        case "InsertarProveedor":
            ejecutarProcedimiento("CALL InsertarProveedor(?, ?)", [$_POST["nombre_proveedor"], $_POST["telefono"]]);
            break;
        case "InsertarVenta":
            ejecutarProcedimiento("CALL InsertarVenta(?, ?)", [$_POST["cliente_id"], $_POST["empleado_id"]]);
            break;
        case "InsertarPago":
            ejecutarProcedimiento("CALL InsertarPago(?, ?, ?)", [$_POST["venta_id"], $_POST["metodo_pago"], $_POST["monto"]]);
            break;
        case "AgregarProductoVenta":
            ejecutarProcedimiento("CALL AgregarProductoVenta(?, ?, ?)", [$_POST["venta_id"], $_POST["producto_id"], $_POST["cantidad"]]);
            break;
        case "EliminarCliente":
            ejecutarProcedimiento("CALL EliminarCliente(?)", [$_POST["cliente_id"]]);
            break;
        case "EliminarProducto":
            ejecutarProcedimiento("CALL EliminarProducto(?)", [$_POST["id"]]);
            break;
        case "ObtenerVentasCliente":
            $_SESSION["resultado"] = obtenerDatos("CALL ObtenerVentasCliente(?)", [$_POST["cliente_id"]]);
            break;
        case "ObtenerDetallesVenta":
            $_SESSION["resultado"] = obtenerDatos("CALL ObtenerDetallesVenta(?)", [$_POST["venta_id"]]);
            break;
    }
    header("Location: ../index.html");
    exit();
}
?>