<?php
/**
 * Business API Endpoint
 * Handles CRUD operations for businesses
 */

require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = getConnection();

switch ($action) {
    case 'list':
        getBusinesses($conn);
        break;
    case 'get':
        getBusiness($conn, $id);
        break;
    case 'create':
        createBusiness($conn);
        break;
    case 'update':
        updateBusiness($conn, $id);
        break;
    case 'delete':
        deleteBusiness($conn, $id);
        break;
    default:
        sendJsonResponse(['error' => 'Invalid action'], 400);
}

$conn->close();

/**
 * Get all businesses with average ratings
 */
function getBusinesses($conn) {
    $sql = "SELECT b.*, 
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(r.id) as total_ratings
            FROM businesses b 
            LEFT JOIN ratings r ON b.id = r.business_id 
            GROUP BY b.id 
            ORDER BY b.created_at DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        sendJsonResponse(['error' => 'Failed to fetch businesses'], 500);
    }
    
    $businesses = [];
    while ($row = $result->fetch_assoc()) {
        $row['avg_rating'] = round((float)$row['avg_rating'], 1);
        $businesses[] = $row;
    }
    
    sendJsonResponse(['success' => true, 'data' => $businesses]);
}

/**
 * Get single business by ID
 */
function getBusiness($conn, $id) {
    if ($id <= 0) {
        sendJsonResponse(['error' => 'Invalid business ID'], 400);
    }
    
    $stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendJsonResponse(['error' => 'Business not found'], 404);
    }
    
    $business = $result->fetch_assoc();
    $stmt->close();
    
    sendJsonResponse(['success' => true, 'data' => $business]);
}

/**
 * Create new business
 */
function createBusiness($conn) {
    if ($method !== 'POST') {
        sendJsonResponse(['error' => 'Method not allowed'], 405);
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($name) || empty($address) || empty($phone) || empty($email)) {
        sendJsonResponse(['error' => 'All fields are required'], 400);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(['error' => 'Invalid email format'], 400);
    }
    
    $stmt = $conn->prepare("INSERT INTO businesses (name, address, phone, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $address, $phone, $email);
    
    if ($stmt->execute()) {
        $newId = $stmt->insert_id;
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
        $stmt->bind_param("i", $newId);
        $stmt->execute();
        $result = $stmt->get_result();
        $business = $result->fetch_assoc();
        $business['avg_rating'] = 0;
        $business['total_ratings'] = 0;
        $stmt->close();
        
        sendJsonResponse(['success' => true, 'message' => 'Business created successfully', 'data' => $business]);
    } else {
        sendJsonResponse(['error' => 'Failed to create business'], 500);
    }
}

/**
 * Update existing business
 */
function updateBusiness($conn, $id) {
    if ($method !== 'POST') {
        sendJsonResponse(['error' => 'Method not allowed'], 405);
    }
    
    if ($id <= 0) {
        sendJsonResponse(['error' => 'Invalid business ID'], 400);
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($name) || empty($address) || empty($phone) || empty($email)) {
        sendJsonResponse(['error' => 'All fields are required'], 400);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(['error' => 'Invalid email format'], 400);
    }
    
    $check = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        sendJsonResponse(['error' => 'Business not found'], 404);
    }
    $check->close();
    
    $stmt = $conn->prepare("UPDATE businesses SET name = ?, address = ?, phone = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $address, $phone, $email, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT b.*, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as total_ratings 
                               FROM businesses b LEFT JOIN ratings r ON b.id = r.business_id 
                               WHERE b.id = ? GROUP BY b.id");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $business = $result->fetch_assoc();
        $business['avg_rating'] = round((float)$business['avg_rating'], 1);
        $stmt->close();
        
        sendJsonResponse(['success' => true, 'message' => 'Business updated successfully', 'data' => $business]);
    } else {
        sendJsonResponse(['error' => 'Failed to update business'], 500);
    }
}

/**
 * Delete business
 */
function deleteBusiness($conn, $id) {
    if ($method !== 'POST') {
        sendJsonResponse(['error' => 'Method not allowed'], 405);
    }
    
    if ($id <= 0) {
        sendJsonResponse(['error' => 'Invalid business ID'], 400);
    }
    
    $check = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        sendJsonResponse(['error' => 'Business not found'], 404);
    }
    $check->close();
    
    $stmt = $conn->prepare("DELETE FROM businesses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        sendJsonResponse(['success' => true, 'message' => 'Business deleted successfully']);
    } else {
        sendJsonResponse(['error' => 'Failed to delete business'], 500);
    }
}
