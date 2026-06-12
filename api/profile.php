<?php
/**
 * API Profile - Xử lý lấy/cập nhật hồ sơ, đổi mật khẩu
 * 
 * Endpoint: api/profile.php
 * Method: POST
 * Yêu cầu: Phải đăng nhập (có session)
 * 
 * Các action: get_profile, update_profile, change_password
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

include_once(__DIR__ . "/../config/config.php");
include_once(__DIR__ . "/../models/UserModel.php");

$userModel = new UserModel($conn);

// Kiểm tra đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'redirect' => 'auth/auth.php']);
    exit;
}

$userId = $_SESSION['user_id'];

// Nhận dữ liệu từ request
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = isset($input['action']) ? $input['action'] : '';

switch ($action) {

    // ============================================
    // LẤY THÔNG TIN HỒ SƠ
    // ============================================
    case 'get_profile':
        $user = $userModel->getUserById($userId);

        if ($user) {
            unset($user['password'], $user['reset_token'], $user['reset_expires']);
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng']);
        }
        break;

    // ============================================
    // CẬP NHẬT HỒ SƠ
    // ============================================
    case 'update_profile':
        $data = [
            'fullname' => trim($input['fullname'] ?? ''),
            'phone'    => trim($input['phone'] ?? ''),
            'address'  => trim($input['address'] ?? '')
        ];

        // Validate
        if (empty($data['fullname'])) {
            echo json_encode(['success' => false, 'message' => 'Họ và tên không được để trống']);
            exit;
        }

        $result = $userModel->updateProfile($userId, $data);

        // Cập nhật session
        if ($result['success'] && isset($result['user'])) {
            $_SESSION['user'] = $result['user'];
        }

        echo json_encode($result);
        break;

    // ============================================
    // ĐỔI MẬT KHẨU
    // ============================================
    case 'change_password':
        $oldPassword = $input['old_password'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit;
        }

        if (strlen($newPassword) < 6) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự']);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp']);
            exit;
        }

        $result = $userModel->changePassword($userId, $oldPassword, $newPassword);
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
