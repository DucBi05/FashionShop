<?php
session_start();
include("includes/navbar.php");

// Nếu chưa đăng nhập → chuyển về trang auth
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "auth/auth.php");
    exit;
}
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="profile-page">
  <div class="profile-container">

    <!-- ============================================ -->
    <!-- SIDEBAR -->
    <!-- ============================================ -->
    <div class="profile-sidebar">
      <div class="profile-avatar">
        <div class="avatar-circle" id="profileAvatarCircle">
          <?= mb_substr($_SESSION['user']['fullname'], 0, 1, 'UTF-8') ?>
        </div>
        <h3 id="sidebarName"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></h3>
        <p id="sidebarEmail"><?= htmlspecialchars($_SESSION['user']['email']) ?></p>
      </div>
      <nav class="profile-nav">
        <a class="profile-nav-item active" onclick="switchProfileTab('info')" id="navInfo">
          📋 Thông tin cá nhân
        </a>
        <a class="profile-nav-item" onclick="switchProfileTab('password')" id="navPassword">
          🔒 Đổi mật khẩu
        </a>
        <a class="profile-nav-item" onclick="doLogout()">
          🚪 Đăng xuất
        </a>
      </nav>
    </div>

    <!-- ============================================ -->
    <!-- CONTENT -->
    <!-- ============================================ -->
    <div class="profile-content">

      <!-- TAB: Thông tin cá nhân -->
      <div class="profile-tab active" id="tab-info">
        <div class="profile-card">
          <div class="profile-card-header">
            <h2>Thông tin cá nhân</h2>
            <p>Quản lý thông tin hồ sơ của bạn</p>
          </div>
          <div class="profile-card-body">
            <div class="form-row">
              <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" id="profileFullname" placeholder="Nhập họ và tên">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" id="profileEmail" disabled style="opacity:0.6;cursor:not-allowed" placeholder="Email không thể thay đổi">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Số điện thoại</label>
                <input type="tel" id="profilePhone" placeholder="0912 345 678">
              </div>
              <div class="form-group">
                <label>Ngày tham gia</label>
                <input type="text" id="profileCreatedAt" disabled style="opacity:0.6;cursor:not-allowed">
              </div>
            </div>
            <div class="form-group">
              <label>Địa chỉ</label>
              <textarea id="profileAddress" rows="3" placeholder="Nhập địa chỉ giao hàng mặc định"></textarea>
            </div>
            <div id="profileError" class="form-error"></div>
            <div id="profileSuccess" class="form-success"></div>
            <button class="btn btn-primary" id="btnUpdateProfile" onclick="doUpdateProfile()">
              <span class="btn-text">💾 Lưu thay đổi</span>
              <span class="btn-loading" style="display:none">⏳ Đang lưu...</span>
            </button>
          </div>
        </div>
      </div>

      <!-- TAB: Đổi mật khẩu -->
      <div class="profile-tab" id="tab-password">
        <div class="profile-card">
          <div class="profile-card-header">
            <h2>Đổi mật khẩu</h2>
            <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu</p>
          </div>
          <div class="profile-card-body">
            <div class="form-group">
              <label>Mật khẩu hiện tại</label>
              <div class="password-wrapper">
                <input type="password" id="oldPassword" placeholder="Nhập mật khẩu hiện tại">
                <span class="toggle-pw" onclick="togglePw('oldPassword', this)">👁️</span>
              </div>
            </div>
            <div class="form-group">
              <label>Mật khẩu mới</label>
              <div class="password-wrapper">
                <input type="password" id="newPassword" placeholder="Tối thiểu 6 ký tự">
                <span class="toggle-pw" onclick="togglePw('newPassword', this)">👁️</span>
              </div>
            </div>
            <div class="form-group">
              <label>Xác nhận mật khẩu mới</label>
              <div class="password-wrapper">
                <input type="password" id="confirmNewPassword" placeholder="Nhập lại mật khẩu mới">
                <span class="toggle-pw" onclick="togglePw('confirmNewPassword', this)">👁️</span>
              </div>
            </div>
            <div id="pwError" class="form-error"></div>
            <div id="pwSuccess" class="form-success"></div>
            <button class="btn btn-primary" id="btnChangePassword" onclick="doChangePassword()">
              <span class="btn-text">🔐 Đổi mật khẩu</span>
              <span class="btn-loading" style="display:none">⏳ Đang xử lý...</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="profileToast"></div>

<script>
const BASE_URL = '<?= $base_url ?>';

// ============================================
// LOAD PROFILE KHI MỞ TRANG
// ============================================
document.addEventListener('DOMContentLoaded', loadProfile);

async function loadProfile() {
  try {
    const result = await callAPI(BASE_URL + 'api/profile.php', { action: 'get_profile' });

    if (result.success) {
      const u = result.user;
      document.getElementById('profileFullname').value = u.fullname || '';
      document.getElementById('profileEmail').value = u.email || '';
      document.getElementById('profilePhone').value = u.phone || '';
      document.getElementById('profileAddress').value = u.address || '';
      document.getElementById('profileCreatedAt').value = formatDate(u.created_at);
    } else if (result.redirect) {
      window.location.href = BASE_URL + result.redirect;
    }
  } catch (err) {
    console.error('Load profile error:', err);
  }
}

// ============================================
// CẬP NHẬT HỒ SƠ
// ============================================
async function doUpdateProfile() {
  clearMessages();
  const fullname = document.getElementById('profileFullname').value.trim();
  const phone = document.getElementById('profilePhone').value.trim();
  const address = document.getElementById('profileAddress').value.trim();

  if (!fullname) {
    showError('profileError', 'Họ và tên không được để trống');
    return;
  }

  setLoading('btnUpdateProfile', true);

  try {
    const result = await callAPI(BASE_URL + 'api/profile.php', {
      action: 'update_profile',
      fullname: fullname,
      phone: phone,
      address: address
    });

    if (result.success) {
      showSuccess('profileSuccess', result.message);
      showToast('Cập nhật thành công!');
      // Cập nhật sidebar
      document.getElementById('sidebarName').textContent = fullname;
      document.getElementById('profileAvatarCircle').textContent = fullname.charAt(0).toUpperCase();
    } else {
      showError('profileError', result.message);
    }
  } catch (err) {
    showError('profileError', 'Lỗi kết nối server');
  }

  setLoading('btnUpdateProfile', false);
}

// ============================================
// ĐỔI MẬT KHẨU
// ============================================
async function doChangePassword() {
  clearMessages();
  const oldPw = document.getElementById('oldPassword').value;
  const newPw = document.getElementById('newPassword').value;
  const confirmPw = document.getElementById('confirmNewPassword').value;

  if (!oldPw || !newPw || !confirmPw) {
    showError('pwError', 'Vui lòng điền đầy đủ thông tin');
    return;
  }

  if (newPw.length < 6) {
    showError('pwError', 'Mật khẩu mới phải có ít nhất 6 ký tự');
    return;
  }

  if (newPw !== confirmPw) {
    showError('pwError', 'Mật khẩu xác nhận không khớp');
    return;
  }

  setLoading('btnChangePassword', true);

  try {
    const result = await callAPI(BASE_URL + 'api/profile.php', {
      action: 'change_password',
      old_password: oldPw,
      new_password: newPw,
      confirm_password: confirmPw
    });

    if (result.success) {
      showSuccess('pwSuccess', result.message);
      showToast('Đổi mật khẩu thành công!');
      // Clear inputs
      document.getElementById('oldPassword').value = '';
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmNewPassword').value = '';
    } else {
      showError('pwError', result.message);
    }
  } catch (err) {
    showError('pwError', 'Lỗi kết nối server');
  }

  setLoading('btnChangePassword', false);
}

// ============================================
// ĐĂNG XUẤT
// ============================================
async function doLogout() {
  try {
    await callAPI(BASE_URL + 'api/auth.php', { action: 'logout' });
  } catch (e) {}
  window.location.href = BASE_URL + 'auth/auth.php';
}

// ============================================
// SWITCH TAB
// ============================================
function switchProfileTab(tab) {
  document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.profile-nav-item').forEach(n => n.classList.remove('active'));

  document.getElementById(`tab-${tab}`).classList.add('active');
  document.getElementById(`nav${tab.charAt(0).toUpperCase() + tab.slice(1)}`).classList.add('active');

  clearMessages();
}

// ============================================
// HELPERS
// ============================================
function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') { input.type = 'text'; btn.textContent = '🙈'; }
  else { input.type = 'password'; btn.textContent = '👁️'; }
}

async function callAPI(url, data) {
  const response = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
  return await response.json();
}

function showError(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.style.display = 'block';
  el.classList.add('shake');
  setTimeout(() => el.classList.remove('shake'), 500);
}

function showSuccess(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.style.display = 'block';
}

function clearMessages() {
  document.querySelectorAll('.form-error, .form-success').forEach(el => {
    el.textContent = '';
    el.style.display = 'none';
  });
}

function setLoading(btnId, loading) {
  const btn = document.getElementById(btnId);
  const text = btn.querySelector('.btn-text');
  const loader = btn.querySelector('.btn-loading');
  btn.disabled = loading;
  text.style.display = loading ? 'none' : 'inline';
  loader.style.display = loading ? 'inline' : 'none';
}

function showToast(message, type = 'success') {
  const toast = document.getElementById('profileToast');
  toast.textContent = (type === 'success' ? '✅ ' : '❌ ') + message;
  toast.className = `toast ${type} show`;
  setTimeout(() => toast.classList.remove('show'), 3000);
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' });
}
</script>

<?php
include("includes/footer.php");
?>
