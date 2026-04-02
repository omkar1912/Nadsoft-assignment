<?php

/**
 * Rating API Endpoint
 * Handles rating operations for businesses
 */

require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$businessId = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;

$conn = getConnection();

switch ($action) {
    case 'submit':
        submitRating($conn, $businessId, $method);
        break;
    case 'get':
        getRatings($conn, $businessId);
        break;
    case 'average':
        getAverageRating($conn, $businessId);
        break;
    default:
        sendJsonResponse(['error' => 'Invalid action'], 400);
}

$conn->close();

/**
 * Submit or update rating for a business
 */
function submitRating($conn, $businessId, $method)
{
    if ($method !== 'POST') {
        sendJsonResponse(['error' => 'Method not allowed'], 405);
    }

    if ($businessId <= 0) {
        sendJsonResponse(['error' => 'Invalid business ID'], 400);
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $rating = isset($_POST['rating']) ? (float)$_POST['rating'] : 0;

    if (empty($name) || empty($email) || empty($phone)) {
        sendJsonResponse(['error' => 'Name, email, and phone are required'], 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(['error' => 'Invalid email format'], 400);
    }

    if ($rating < 0 || $rating > 5) {
        sendJsonResponse(['error' => 'Rating must be between 0 and 5'], 400);
    }

    $check = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
    $check->bind_param("i", $businessId);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        sendJsonResponse(['error' => 'Business not found'], 404);
    }
    $check->close();

    $stmt = $conn->prepare("SELECT id FROM ratings WHERE business_id = ? AND (email = ? OR phone = ?)");
    $stmt->bind_param("iss", $businessId, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE ratings SET name = ?, email = ?, phone = ?, rating = ? WHERE id = ?");
        $stmt->bind_param("sssdi", $name, $email, $phone, $rating, $existing['id']);

        if ($stmt->execute()) {
            $stmt->close();
            $ratingInfo = calculateAverageRating($conn, $businessId);
            sendJsonResponse([
                'success' => true,
                'message' => 'Rating updated successfully',
                'avg_rating' => $ratingInfo['avg_rating'],
                'total_ratings' => $ratingInfo['total_ratings'],
                'action' => 'updated'
            ]);
        } else {
            sendJsonResponse(['error' => 'Failed to update rating'], 500);
        }
    } else {
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO ratings (business_id, name, email, phone, rating) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssd", $businessId, $name, $email, $phone, $rating);

        if ($stmt->execute()) {
            $stmt->close();
            $ratingInfo = calculateAverageRating($conn, $businessId);
            sendJsonResponse([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'avg_rating' => $ratingInfo['avg_rating'],
                'total_ratings' => $ratingInfo['total_ratings'],
                'action' => 'inserted'
            ]);
        } else {
            sendJsonResponse(['error' => 'Failed to submit rating'], 500);
        }
    }
}

/**
 * Get all ratings for a business
 */
function getRatings($conn, $businessId)
{
    if ($businessId <= 0) {
        sendJsonResponse(['error' => 'Invalid business ID'], 400);
    }

    $stmt = $conn->prepare("SELECT * FROM ratings WHERE business_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();

    $ratings = [];
    while ($row = $result->fetch_assoc()) {
        $ratings[] = $row;
    }
    $stmt->close();

    sendJsonResponse(['success' => true, 'data' => $ratings]);
}

/**
 * Get average rating for a business
 */
function getAverageRating($conn, $businessId)
{
    if ($businessId <= 0) {
        sendJsonResponse(['error' => 'Invalid business ID'], 400);
    }

    $ratingInfo = calculateAverageRating($conn, $businessId);
    sendJsonResponse([
        'success' => true,
        'avg_rating' => $ratingInfo['avg_rating'],
        'total_ratings' => $ratingInfo['total_ratings']
    ]);
}

/**
 * Calculate average rating for a business
 */
function calculateAverageRating($conn, $businessId)
{
    $stmt = $conn->prepare("SELECT COALESCE(AVG(rating), 0) as avg_rating, COUNT(id) as total_ratings FROM ratings WHERE business_id = ?");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return [
        'avg_rating' => round((float)$row['avg_rating'], 1),
        'total_ratings' => (int)$row['total_ratings']
    ];
}
