<?php
include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="policy-page">
    <h1 style="font-size:1.75rem;font-weight:800;margin-bottom:1.5rem">📋 Chính sách</h1>
    <div class="policy-card">
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem">
        <span class="ftag active" onclick="showPolicy(this,'return')">↩️ Đổi trả</span>
        <span class="ftag" onclick="showPolicy(this,'warranty')">🛡️ Bảo hành</span>
        <span class="ftag" onclick="showPolicy(this,'ship')">🚚 Vận chuyển</span>
        <span class="ftag" onclick="showPolicy(this,'privacy')">🔒 Bảo mật</span>
      </div>
      <div id="policyContent">
        <div class="policy-section"><h3>↩️ Chính sách đổi trả 30 ngày</h3><p>StyleVibe cam kết đổi trả miễn phí trong 30 ngày kể từ ngày nhận hàng. Sản phẩm cần còn nguyên tem, nhãn mác và chưa qua sử dụng. Khách hàng chỉ cần liên hệ hotline hoặc email để được hỗ trợ.</p></div>
        <div class="policy-section"><h3>📋 Điều kiện đổi trả</h3><p>• Sản phẩm lỗi do nhà sản xuất: Đổi mới 100% miễn phí<br>• Sản phẩm không vừa size: Đổi size miễn phí (1 lần)<br>• Khách hàng đổi ý: Hoàn tiền 80% giá trị đơn hàng<br>• Sản phẩm Sale off trên 50%: Không áp dụng đổi trả</p></div>
        <div class="policy-section"><h3>⏰ Thời gian xử lý</h3><p>Yêu cầu đổi trả được xử lý trong 2-3 ngày làm việc. Hoàn tiền qua tài khoản ngân hàng trong 5-7 ngày làm việc.</p></div>
      </div>
    </div>
</div>

<script>
const policyData={
  return:`<div class="policy-section"><h3>↩️ Chính sách đổi trả 30 ngày</h3><p>StyleVibe cam kết đổi trả miễn phí trong 30 ngày. Sản phẩm cần nguyên tem, nhãn mác.</p></div><div class="policy-section"><h3>Điều kiện</h3><p>• Lỗi NSX: Đổi mới 100%<br>• Sai size: Đổi 1 lần miễn phí<br>• Đổi ý: Hoàn 80%<br>• Sale >50%: Không áp dụng</p></div>`,
  warranty:`<div class="policy-section"><h3>🛡️ Chính sách bảo hành</h3><p>Sản phẩm được bảo hành 6 tháng cho lỗi kỹ thuật từ nhà sản xuất. Không bảo hành hư hỏng do sử dụng sai cách.</p></div><div class="policy-section"><h3>Quy trình bảo hành</h3><p>1. Liên hệ hotline 0901 234 567<br>2. Gửi ảnh chụp lỗi về email<br>3. Nhận mã bảo hành<br>4. Gửi sản phẩm về kho (StyleVibe thanh toán phí gửi)</p></div>`,
  ship:`<div class="policy-section"><h3>🚚 Chính sách vận chuyển</h3><p>Giao hàng nhanh 2h nội thành TP.HCM, 1-3 ngày toàn quốc. Miễn phí ship đơn từ 500K.</p></div><div class="policy-section"><h3>Bảng phí vận chuyển</h3><p>• TP.HCM – Giao nhanh 2h: 35,000đ<br>• TP.HCM – Tiêu chuẩn: Miễn phí từ 500K<br>• Tỉnh khác: 30,000-50,000đ tùy khoảng cách<br>• Đơn từ 500K: Miễn phí ship toàn quốc</p></div>`,
  privacy:`<div class="policy-section"><h3>🔒 Chính sách bảo mật</h3><p>StyleVibe cam kết bảo vệ thông tin cá nhân của khách hàng theo quy định pháp luật Việt Nam và GDPR.</p></div><div class="policy-section"><h3>Dữ liệu thu thập</h3><p>Chúng tôi chỉ thu thập thông tin cần thiết (tên, địa chỉ, email, SĐT) để xử lý đơn hàng. Không chia sẻ với bên thứ 3 trừ đối tác vận chuyển.</p></div>`
};
function showPolicy(el,key){
  document.querySelectorAll('.policy-page .ftag').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('policyContent').innerHTML=policyData[key];
}
</script>

<?php
include("../includes/footer.php");
?>