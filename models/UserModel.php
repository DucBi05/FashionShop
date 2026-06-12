<?php
/**
 * UserModel - Quản lý tất cả thao tác DB liên quan User
 * 
 * Sử dụng: 
 *   $userModel = new UserModel($conn);
 *   $result = $userModel->register($fullname, $email, $password);
 */

class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ============================================
    // ĐĂNG KÝ
    // ============================================
    public function register($fullname, $email, $password) {
        // Kiểm tra email đã tồn tại chưa
        if ($this->getUserByEmail($email)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }

        // Hash mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $fullname, $email, $hashedPassword);

        if ($stmt->execute()) {
            $userId = $stmt->insert_id;
            $stmt->close();
            return [
                'success' => true,
                'message' => 'Đăng ký thành công',
                'user_id' => $userId
            ];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại'];
    }

    // ============================================
    // ĐĂNG NHẬP
    // ============================================
    public function login($email, $password) {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email không tồn tại'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Mật khẩu không đúng'];
        }

        // Trả về user info (loại bỏ password)
        unset($user['password'], $user['reset_token'], $user['reset_expires']);

        return [
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => $user
        ];
    }

    // ============================================
    // LẤY USER THEO ID
    // ============================================
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // ============================================
    // LẤY USER THEO EMAIL
    // ============================================
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // ============================================
    // CẬP NHẬT HỒ SƠ
    // ============================================
    public function updateProfile($id, $data) {
        $allowedFields = ['fullname', 'phone', 'address'];
        $updates = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $types .= 's';
                $values[] = $data[$field];
            }
        }

        if (empty($updates)) {
            return ['success' => false, 'message' => 'Không có dữ liệu để cập nhật'];
        }

        $types .= 'i';
        $values[] = $id;

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $stmt->close();
            // Lấy lại user mới nhất
            $user = $this->getUserById($id);
            unset($user['password'], $user['reset_token'], $user['reset_expires']);
            return [
                'success' => true,
                'message' => 'Cập nhật hồ sơ thành công',
                'user' => $user
            ];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Lỗi cập nhật, vui lòng thử lại'];
    }

    // ============================================
    // ĐỔI MẬT KHẨU
    // ============================================
    public function changePassword($id, $oldPassword, $newPassword) {
        $user = $this->getUserById($id);

        if (!$user) {
            return ['success' => false, 'message' => 'Người dùng không tồn tại'];
        }

        if (!password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Mật khẩu cũ không đúng'];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Lỗi đổi mật khẩu'];
    }

    // ============================================
    // TẠO TOKEN RESET MẬT KHẨU
    // ============================================
    public function createResetToken($email) {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email không tồn tại trong hệ thống'];
        }

        // Tạo token ngẫu nhiên
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare(
            "UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $token, $expires, $user['id']);

        if ($stmt->execute()) {
            $stmt->close();
            return [
                'success' => true,
                'message' => 'Token đã được tạo. Vui lòng sử dụng token để đặt lại mật khẩu.',
                'token' => $token  // Hiển thị trên màn hình (localhost dev mode)
            ];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Lỗi tạo token'];
    }

    // ============================================
    // ĐẶT LẠI MẬT KHẨU BẰNG TOKEN
    // ============================================
    public function resetPassword($token, $newPassword) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            return ['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn'];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare(
            "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?"
        );
        $stmt->bind_param("si", $hashedPassword, $user['id']);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.'];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Lỗi đặt lại mật khẩu'];
    }
}
?>
