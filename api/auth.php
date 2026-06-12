<?php
/**
 * API Auth - Xử lý đăng ký, đăng nhập, quên mật khẩu
 * 
 * Endpoint: api/auth.php
 * Method: POST
 * Content-Type: application/json hoặc form-data
 * 
 * Các action: register, login, logout, forgot_password, reset_password
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

include_once(__DIR__ . "/../config/config.php");
include_once(__DIR__ . "/../models/UserModel.php");

$userModel = new UserModel($conn);

// Nhận dữ liệu từ request
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = isset($input['action']) ? $input['action'] : '';

switch ($action) {

    // ============================================
    // ĐĂNG KÝ
    // ============================================
    case 'register':
        $fullname = trim($input['fullname'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        // Validate
        if (empty($fullname) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
            exit;
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp']);
            exit;
        }

        $result = $userModel->register($fullname, $email, $password);
        echo json_encode($result);
        break;

    // ============================================
    // ĐĂNG NHẬP
    // ============================================
    case 'login':
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu']);
            exit;
        }

        $result = $userModel->login($email, $password);

        if ($result['success']) {
            // Lưu vào session
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user'] = $result['user'];
        }

        echo json_encode($result);
        break;

    // ============================================
    // ĐĂNG XUẤT
    // ============================================
    case 'logout':
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Đã đăng xuất']);
        break;

    // ============================================
    // QUÊN MẬT KHẨU — Tạo token
    // ============================================
    case 'forgot_password':
        $email = trim($input['email'] ?? '');

        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập email']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
            exit;
        }

        $result = $userModel->createResetToken($email);
        echo json_encode($result);
        break;

    // ============================================
    // ĐẶT LẠI MẬT KHẨU — Dùng token
    // ============================================
    case 'reset_password':
        $token = trim($input['token'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        if (empty($token) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
            exit;
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp']);
            exit;
        }

        $result = $userModel->resetPassword($token, $password);
        echo json_encode($result);
        break;

    // ============================================
    // DEFAULT
    // ============================================
    default:
        echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
        break;
}
?>
