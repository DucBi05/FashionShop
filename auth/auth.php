<?php
session_start();
include("../includes/navbar.php");

// Nếu đã đăng nhập → chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "profile.php");
    exit;
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="auth-page">
    <div class="auth-card">

      <!-- TABS: Đăng nhập / Đăng ký / Quên mật khẩu -->
      <div class="auth-tabs">
        <div class="auth-tab active" onclick="switchTab('login')">Đăng nhập</div>
        <div class="auth-tab" onclick="switchTab('register')">Đăng ký</div>
      </div>

      <!-- ============================================ -->
      <!-- FORM ĐĂNG NHẬP -->
      <!-- ============================================ -->
      <div class="auth-form active" id="form-login">
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="loginEmail" name="email" placeholder="email@example.com">
        </div>
        <div class="form-group">
          <label>Mật khẩu</label>
          <div class="password-wrapper">
            <input type="password" id="loginPassword" name="password" placeholder="••••••••">
            <span class="toggle-pw" onclick="togglePassword('loginPassword', this)">👁️</span>
          </div>
        </div>
        <div id="loginError" class="form-error"></div>
        <button class="btn btn-primary btn-full" id="btnLogin" onclick="doLogin()">
          <span class="btn-text">Đăng nhập</span>
          <span class="btn-loading" style="display:none">⏳ Đang xử lý...</span>
        </button>
        <p class="auth-link">
          Quên mật khẩu?
          <span onclick="switchTab('forgot')">Khôi phục</span>
        </p>
      </div>

      <!-- ============================================ -->
      <!-- FORM ĐĂNG KÝ -->
      <!-- ============================================ -->
      <div class="auth-form" id="form-register">
        <div class="form-group">
          <label>Họ và tên</label>
          <input type="text" id="regFullname" name="fullname" placeholder="Nguyễn Văn A">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="regEmail" name="email" placeholder="email@example.com">
        </div>
        <div class="form-group">
          <label>Mật khẩu</label>
          <div class="password-wrapper">
            <input type="password" id="regPassword" name="password" placeholder="Tối thiểu 6 ký tự">
            <span class="toggle-pw" onclick="togglePassword('regPassword', this)">👁️</span>
          </div>
        </div>
        <div class="form-group">
          <label>Xác nhận mật khẩu</label>
          <div class="password-wrapper">
            <input type="password" id="regConfirmPassword" name="confirm_password" placeholder="Nhập lại mật khẩu">
            <span class="toggle-pw" onclick="togglePassword('regConfirmPassword', this)">👁️</span>
          </div>
        </div>
        <div id="regError" class="form-error"></div>
        <div id="regSuccess" class="form-success"></div>
        <button class="btn btn-primary btn-full" id="btnRegister" onclick="doRegister()">
          <span class="btn-text">Tạo tài khoản</span>
          <span class="btn-loading" style="display:none">⏳ Đang xử lý...</span>
        </button>
        <p class="auth-link">
          Đã có tài khoản?
          <span onclick="switchTab('login')">Đăng nhập</span>
        </p>
      </div>

      <!-- ============================================ -->
      <!-- FORM QUÊN MẬT KHẨU -->
      <!-- ============================================ -->
      <div class="auth-form" id="form-forgot">
        <div class="auth-form-title">
          <span style="font-size:2rem">🔑</span>
          <h3>Khôi phục mật khẩu</h3>
          <p style="font-size:0.85rem;color:var(--muted)">Nhập email để nhận mã khôi phục</p>
        </div>
        <div class="form-group">
          <label>Email đã đăng ký</label>
          <input type="email" id="forgotEmail" name="email" placeholder="email@example.com">
        </div>
        <div id="forgotError" class="form-error"></div>
        <div id="forgotSuccess" class="form-success"></div>
        <button class="btn btn-primary btn-full" id="btnForgot" onclick="doForgotPassword()">
          <span class="btn-text">Gửi mã khôi phục</span>
          <span class="btn-loading" style="display:none">⏳ Đang xử lý...</span>
        </button>

        <!-- Form nhập token + mật khẩu mới (ẩn ban đầu) -->
        <div id="resetSection" style="display:none;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border)">
          <div class="auth-form-title">
            <h3>Đặt mật khẩu mới</h3>
          </div>
          <div class="form-group">
            <label>Mã khôi phục (Token)</label>
            <input type="text" id="resetToken" placeholder="Dán mã token vào đây">
          </div>
          <div class="form-group">
            <label>Mật khẩu mới</label>
            <div class="password-wrapper">
              <input type="password" id="resetPassword" placeholder="Tối thiểu 6 ký tự">
              <span class="toggle-pw" onclick="togglePassword('resetPassword', this)">👁️</span>
            </div>
          </div>
          <div class="form-group">
            <label>Xác nhận mật khẩu mới</label>
            <div class="password-wrapper">
              <input type="password" id="resetConfirmPassword" placeholder="Nhập lại mật khẩu mới">
              <span class="toggle-pw" onclick="togglePassword('resetConfirmPassword', this)">👁️</span>
            </div>
          </div>
          <div id="resetError" class="form-error"></div>
          <div id="resetSuccess" class="form-success"></div>
          <button class="btn btn-primary btn-full" id="btnReset" onclick="doResetPassword()">
            <span class="btn-text">Đặt lại mật khẩu</span>
            <span class="btn-loading" style="display:none">⏳ Đang xử lý...</span>
          </button>
        </div>

        <p class="auth-link" style="margin-top:1rem">
          <span onclick="switchTab('login')">← Quay lại đăng nhập</span>
        </p>
      </div>

    </div>
</div>

<!-- ============================================ -->
<!-- TOAST NOTIFICATION -->
<!-- ============================================ -->
<div class="toast" id="authToast"></div>

<script>
const BASE_URL = '<?= $base_url ?>';

// ============================================
// SWITCH TAB
// ============================================
function switchTab(tab) {
  // Ẩn tất cả form
  document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));

  // Hiện form tương ứng
  const form = document.getElementById(`form-${tab}`);
  if (form) form.classList.add('active');

  // Active tab (chỉ login và register có tab)
  if (tab === 'login' || tab === 'register') {
    const tabEl = document.querySelector(`.auth-tab[onclick="switchTab('${tab}')"]`);
    if (tabEl) tabEl.classList.add('active');
  }

  // Clear errors
  document.querySelectorAll('.form-error, .form-success').forEach(el => {
    el.textContent = '';
    el.style.display = 'none';
  });
}

// ============================================
// TOGGLE PASSWORD VISIBILITY
// ============================================
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = '🙈';
  } else {
    input.type = 'password';
    btn.textContent = '👁️';
  }
}

// ============================================
// HIỂN THỊ ERROR / SUCCESS
// ============================================
function showError(elementId, message) {
  const el = document.getElementById(elementId);
  el.textContent = message;
  el.style.display = 'block';
  el.classList.add('shake');
  setTimeout(() => el.classList.remove('shake'), 500);
}

function showSuccess(elementId, message) {
  const el = document.getElementById(elementId);
  el.textContent = message;
  el.style.display = 'block';
}

function clearMessages() {
  document.querySelectorAll('.form-error, .form-success').forEach(el => {
    el.textContent = '';
    el.style.display = 'none';
  });
}

// ============================================
// LOADING STATE
// ============================================
function setLoading(btnId, loading) {
  const btn = document.getElementById(btnId);
  const text = btn.querySelector('.btn-text');
  const loader = btn.querySelector('.btn-loading');
  btn.disabled = loading;
  text.style.display = loading ? 'none' : 'inline';
  loader.style.display = loading ? 'inline' : 'none';
}

// ============================================
// TOAST
// ============================================
function showToast(message, type = 'success') {
  const toast = document.getElementById('authToast');
  toast.textContent = (type === 'success' ? '✅ ' : '❌ ') + message;
  toast.className = `toast ${type} show`;
  setTimeout(() => toast.classList.remove('show'), 3000);
}

// ============================================
// GỌI API HELPER
// ============================================
async function callAPI(url, data) {
  const response = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
  return await response.json();
}

// ============================================
// ĐĂNG NHẬP
// ============================================
async function doLogin() {
  clearMessages();
  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value;

  if (!email || !password) {
    showError('loginError', 'Vui lòng nhập email và mật khẩu');
    return;
  }

  setLoading('btnLogin', true);

  try {
    const result = await callAPI(BASE_URL + 'api/auth.php', {
      action: 'login',
      email: email,
      password: password
    });

    if (result.success) {
      showToast('Đăng nhập thành công!');
      setTimeout(() => {
        window.location.href = BASE_URL + 'index.php';
      }, 800);
    } else {
      showError('loginError', result.message);
    }
  } catch (err) {
    showError('loginError', 'Lỗi kết nối server');
  }

  setLoading('btnLogin', false);
}

// ============================================
// ĐĂNG KÝ
// ============================================
async function doRegister() {
  clearMessages();
  const fullname = document.getElementById('regFullname').value.trim();
  const email = document.getElementById('regEmail').value.trim();
  const password = document.getElementById('regPassword').value;
  const confirmPassword = document.getElementById('regConfirmPassword').value;

  if (!fullname || !email || !password || !confirmPassword) {
    showError('regError', 'Vui lòng điền đầy đủ thông tin');
    return;
  }

  if (password.length < 6) {
    showError('regError', 'Mật khẩu phải có ít nhất 6 ký tự');
    return;
  }

  if (password !== confirmPassword) {
    showError('regError', 'Mật khẩu xác nhận không khớp');
    return;
  }

  setLoading('btnRegister', true);

  try {
    const result = await callAPI(BASE_URL + 'api/auth.php', {
      action: 'register',
      fullname: fullname,
      email: email,
      password: password,
      confirm_password: confirmPassword
    });

    if (result.success) {
      showSuccess('regSuccess', 'Đăng ký thành công! Đang chuyển sang đăng nhập...');
      showToast('Đăng ký thành công!');
      setTimeout(() => switchTab('login'), 1500);
    } else {
      showError('regError', result.message);
    }
  } catch (err) {
    showError('regError', 'Lỗi kết nối server');
  }

  setLoading('btnRegister', false);
}

// ============================================
// QUÊN MẬT KHẨU
// ============================================
async function doForgotPassword() {
  clearMessages();
  const email = document.getElementById('forgotEmail').value.trim();

  if (!email) {
    showError('forgotError', 'Vui lòng nhập email');
    return;
  }

  setLoading('btnForgot', true);

  try {
    const result = await callAPI(BASE_URL + 'api/auth.php', {
      action: 'forgot_password',
      email: email
    });

    if (result.success) {
      showSuccess('forgotSuccess', result.message);
      // Hiện form đặt lại mật khẩu
      document.getElementById('resetSection').style.display = 'block';
      // Auto-fill token (localhost dev mode)
      if (result.token) {
        document.getElementById('resetToken').value = result.token;
      }
      showToast('Token đã được tạo!');
    } else {
      showError('forgotError', result.message);
    }
  } catch (err) {
    showError('forgotError', 'Lỗi kết nối server');
  }

  setLoading('btnForgot', false);
}

// ============================================
// ĐẶT LẠI MẬT KHẨU
// ============================================
async function doResetPassword() {
  clearMessages();
  const token = document.getElementById('resetToken').value.trim();
  const password = document.getElementById('resetPassword').value;
  const confirmPassword = document.getElementById('resetConfirmPassword').value;

  if (!token || !password || !confirmPassword) {
    showError('resetError', 'Vui lòng điền đầy đủ thông tin');
    return;
  }

  if (password.length < 6) {
    showError('resetError', 'Mật khẩu phải có ít nhất 6 ký tự');
    return;
  }

  if (password !== confirmPassword) {
    showError('resetError', 'Mật khẩu xác nhận không khớp');
    return;
  }

  setLoading('btnReset', true);

  try {
    const result = await callAPI(BASE_URL + 'api/auth.php', {
      action: 'reset_password',
      token: token,
      password: password,
      confirm_password: confirmPassword
    });

    if (result.success) {
      showSuccess('resetSuccess', result.message);
      showToast('Đặt lại mật khẩu thành công!');
      setTimeout(() => switchTab('login'), 2000);
    } else {
      showError('resetError', result.message);
    }
  } catch (err) {
    showError('resetError', 'Lỗi kết nối server');
  }

  setLoading('btnReset', false);
}

// Cho phép nhấn Enter để submit
document.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    const activeForm = document.querySelector('.auth-form.active');
    if (!activeForm) return;

    if (activeForm.id === 'form-login') doLogin();
    else if (activeForm.id === 'form-register') doRegister();
    else if (activeForm.id === 'form-forgot') {
      const resetSection = document.getElementById('resetSection');
      if (resetSection.style.display !== 'none') {
        doResetPassword();
      } else {
        doForgotPassword();
      }
    }
  }
});
</script>

<?php
include("../includes/footer.php");
?>