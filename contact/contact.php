<?php
include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="contact-page">
    <h1 style="font-size:1.75rem;font-weight:800;margin-bottom:1.5rem">📍 Liên hệ & Feedback</h1>
    <div class="contact-grid">
      <div>
        <div class="contact-info">
          <div class="info-card"><div class="info-icon">📍</div><div><strong>Địa chỉ</strong><p style="font-size:.875rem;color:var(--muted);margin-top:.25rem">123 Nguyễn Huệ, Quận 1, TP.HCM</p></div></div>
          <div class="info-card"><div class="info-icon">📞</div><div><strong>Điện thoại</strong><p style="font-size:.875rem;color:var(--muted);margin-top:.25rem">0901 234 567 (8:00–22:00)</p></div></div>
          <div class="info-card"><div class="info-icon">✉️</div><div><strong>Email</strong><p style="font-size:.875rem;color:var(--muted);margin-top:.25rem">hello@stylevibe.vn</p></div></div>
          <div class="info-card"><div class="info-icon">🕐</div><div><strong>Giờ làm việc</strong><p style="font-size:.875rem;color:var(--muted);margin-top:.25rem">Thứ 2 – Chủ nhật: 8:00 – 22:00</p></div></div>
        </div>
        <div id="map"></div>
      </div>
      <div class="contact-form-box">
        <h3 style="margin-bottom:1.25rem">Gửi tin nhắn</h3>
        <div class="form-group"><label>Họ tên</label><input type="text" id="c-name" placeholder="Nguyễn Văn A"></div>
        <div class="form-group"><label>Email</label><input type="email" id="c-email" placeholder="email@example.com"></div>
        <div class="form-group"><label>Chủ đề</label>
          <select id="c-topic"><option>Hỏi về sản phẩm</option><option>Đổi trả hàng</option><option>Phản hồi chất lượng</option><option>Hợp tác kinh doanh</option><option>Khác</option></select>
        </div>
        <div class="form-group"><label>Nội dung</label><textarea id="c-msg" rows="5" placeholder="Nhập nội dung..."></textarea></div>
        <div style="margin-bottom:1rem">
          <label style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;cursor:pointer">
            <input type="range" min="1" max="5" value="5" id="feedbackRating" style="flex:1" oninput="document.getElementById('ratingVal').textContent=this.value+'⭐'">
            <span id="ratingVal">5⭐</span>
          </label>
          <p style="font-size:.75rem;color:var(--muted);margin-top:.25rem">Đánh giá trải nghiệm mua sắm</p>
        </div>
        <button class="btn btn-primary" style="width:100%;justify-content:center" onclick="sendFeedback()">Gửi phản hồi ✉️</button>
      </div>
    </div>
</div>

<script>
function sendFeedback(){
  const name=document.getElementById('c-name').value;
  const msg=document.getElementById('c-msg').value;
  if(!name||!msg){showToast('Vui lòng điền đầy đủ thông tin','error');return;}
  showToast('Gửi phản hồi thành công! Cảm ơn bạn ❤️','success');
  document.getElementById('c-name').value='';document.getElementById('c-msg').value='';
}
function initMap(){
  try{
    const map=L.map('map').setView([10.7769,106.7009],15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
    L.marker([10.7769,106.7009]).addTo(map).bindPopup('StyleVibe – 123 Nguyễn Huệ, Q.1, TP.HCM').openPopup();
  }catch(e){}
}
</script>

<?php
include("../includes/footer.php");
?>