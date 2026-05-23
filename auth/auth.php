<?php
include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="auth-page">
    <div class="auth-card">
      <div class="auth-tabs">
        <div class="auth-tab active" onclick="switchTab('login')">Đăng nhập</div>
        <div class="auth-tab" onclick="switchTab('register')">Đăng ký</div>
      </div>
      <div class="auth-form active" id="form-login">
        <div class="form-group"><label>Email</label><input type="email" placeholder="email@example.com"></div>
        <div class="form-group"><label>Mật khẩu</label><input type="password" placeholder="••••••••"></div>
        <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:.5rem" onclick="doLogin()">Đăng nhập</button>
        <p style="text-align:center;margin-top:1rem;font-size:.875rem;color:var(--muted)">Quên mật khẩu? <span style="color:var(--accent);cursor:pointer">Khôi phục</span></p>
      </div>
      <div class="auth-form" id="form-register">
        <div class="form-group"><label>Họ và tên</label><input type="text" placeholder="Nguyễn Văn A"></div>
        <div class="form-group"><label>Email</label><input type="email" placeholder="email@example.com"></div>
        <div class="form-group"><label>Mật khẩu</label><input type="password" placeholder="Tối thiểu 8 ký tự"></div>
        <div class="form-group"><label>Xác nhận mật khẩu</label><input type="password" placeholder="Nhập lại mật khẩu"></div>
        <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:.5rem" onclick="doRegister()">Tạo tài khoản</button>
      </div>
    </div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
  document.querySelector(`.auth-tab[onclick="switchTab('${tab}')"]`).classList.add('active');
  document.getElementById(`form-${tab}`).classList.add('active');
}
</script>

<?php
include("../includes/footer.php");
?>