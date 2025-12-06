<style>
  :root{
    --light-purple:#e9d8ff;
    --dark-text:#2b124c;
    --accent:#7b3fe4;
    --muted:#4d3569;
    --neon1:#b783ff;
    --neon2:#9955ff;
    --max:1150px;
    --pay-glow: rgba(151, 86, 255, 0.18);
    --pay-glow-strong: rgba(151, 86, 255, 0.32);
    --transition: 240ms cubic-bezier(.2,.9,.2,1);
    --logo-url: url("images/seal.png");
  }

  /* Reset-ish for footer area only */
  .footer-modern,
  .footer-modern * { box-sizing: border-box; font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; -webkit-font-smoothing:antialiased; }

  /* Skip link for keyboard users */
  .skip-to-footer{ position:absolute;left:-999px;top:auto;width:1px;height:1px;overflow:hidden; }
  .skip-to-footer:focus{ left:12px; top:12px; width:auto; height:auto; padding:8px 12px; background:#fff; color:#000; z-index:9999; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,0.12); }

  /* FOOTER BASE */
  .footer-modern{
    position:relative;
    overflow:hidden;
    background:var(--light-purple);
    border-top:3px solid var(--accent);
    color:var(--dark-text);
    padding:44px 0 18px;
  }

  /* CENTERED SOFT WIDE WATERMARK LOGO (big, behind everything) */
  .footer-bg-logo{
    position:absolute;
    left:50%;
    top:50%;
    transform:translate(-50%,-40%); /* slightly higher */
    width:1200px;
    height:1200px;
    background-image: var(--logo-url);
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity:0.06;
    filter: grayscale(100%) brightness(88%) blur(1px);
    z-index:1;
    pointer-events:none;
  }

  /* PURPLE OVERLAY (soft) */
  .footer-bg-overlay{
    position:absolute;
    inset:0;
    background: linear-gradient(180deg, rgba(214,178,255,0.18), rgba(150,100,220,0.14));
    z-index:2;
    pointer-events:none;
  }

  /* CONTENT GRID */
  .footer-container{
    position:relative;
    z-index:3;
    max-width:var(--max);
    margin:0 auto;
    padding:0 24px;
    display:grid;
    grid-template-columns: 1.6fr 1fr 1fr 1fr;
    gap:24px;
    align-items:start;
  }

  .footer-col { display:flex; flex-direction:column; gap:10px; min-width:0; }

  /* FOREGROUND LOGO + TITLE (with logo behind the H3) */
  .brand-logo-title{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:4px;
  }
  .brand-logo-title img{
    width:46px;
    height:46px;
    object-fit:contain;
    filter: drop-shadow(0 0 6px rgba(123,63,228,0.28));
    border-radius:8px;
    background:linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
    padding:6px;
  }

  /* Make the H3 compatible with background logo: we use a pseudo-element that shows a softened, wide logo behind the heading */
  .brand-title{
    position:relative;
    font-size:26px;
    font-weight:800;
    letter-spacing:0.3px;
    margin:0;
    color:var(--accent);
    text-shadow:0 0 8px rgba(123,63,228,0.18);
    line-height:1.05;
    padding:6px 10px;
    z-index:4;
  }
  .brand-title::before{
    content:"";
    position:absolute;
    left:50%;
    top:50%;
    transform: translate(-50%,-50%) scale(1.6);
    width:220px;
    height:220px;
    background-image: var(--logo-url);
    background-size:contain;
    background-repeat:no-repeat;
    background-position:center;
    opacity:0.06;
    filter:grayscale(100%) brightness(85%) blur(1px);
    z-index:-1;
    pointer-events:none;
    border-radius:28px;
  }

  /* TEXT STYLES */
  h4{ color:var(--accent); margin:0 0 6px; font-size:14px; }
  .muted{ color:var(--muted); }
  .small{ font-size:13px; line-height:1.45; }

  /* QUICK LINKS */
  .quick{ list-style:none; margin:0; padding:0; }
  .quick li{ margin-bottom:8px; }
  .quick a{
    color:var(--dark-text);
    text-decoration:none;
    display:inline-block;
    transition: color var(--transition), transform var(--transition);
  }
  .quick a:hover, .quick a:focus{ color:#000; transform: translateX(6px); outline:none; }

  /* SECURE PAYMENTS */
  .secure-title{ display:flex; align-items:center; gap:8px; margin-bottom:8px; }
  .secure-icon{ width:18px; color:var(--accent); opacity:0.98; }

  .pay-card{
    padding:12px;
    border-radius:12px;
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    box-shadow:
      0 6px 20px rgba(11,2,16,0.05),
      0 0 24px var(--pay-glow);
    transition: box-shadow var(--transition), transform var(--transition);
    will-change: box-shadow, transform;
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:flex-start;
  }
  .pay-card:focus-within, .pay-card:hover{
    transform: translateY(-6px);
    box-shadow:
      0 10px 38px rgba(11,2,16,0.07),
      0 0 48px var(--pay-glow-strong);
  }

  .pay-icons{ display:flex; align-items:center; gap:12px; flex-wrap:wrap; }

  .pay-ico{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:84px;
    height:44px;
    padding:6px;
    border-radius:8px;
    background: rgba(255,255,255,0.22);
    box-shadow: inset 0 -2px 6px rgba(0,0,0,0.03);
    flex-shrink:0;
  }
  .pay-ico img{ max-width:100%; max-height:100%; object-fit:contain; filter:brightness(120%); }

  /* QR CTA */
  .qr-cta{
    margin-left:auto;
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:10px;
    background:linear-gradient(90deg, rgba(151,86,255,0.12), rgba(151,86,255,0.08));
    border:1px solid rgba(123,63,228,0.06);
    cursor:pointer;
    font-weight:700;
    color:var(--accent);
    transition: transform var(--transition), box-shadow var(--transition);
  }
  .qr-cta:hover, .qr-cta:focus{ transform: translateY(-4px); box-shadow:0 10px 30px rgba(123,63,228,0.08); outline:none; }

  /* COD badge style */
  .cod-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:10px;
    background: linear-gradient(180deg,#3a1b68,#2b0f52);
    color: #fff;
    font-weight:700;
    letter-spacing:0.4px;
    font-size:13px;
    position:relative;
    box-shadow:
      0 8px 22px rgba(40,10,90,0.36),
      0 0 18px rgba(100,50,200,0.36);
    transition: transform var(--transition), box-shadow var(--transition);
  }
  .cod-badge svg{ width:20px; height:20px; fill:white; opacity:0.98; }
  .cod-badge:hover{ transform: translateY(-6px) scale(1.02); box-shadow: 0 14px 40px rgba(151,86,255,0.28), 0 0 64px rgba(151,86,255,0.14); }

  /* SOCIAL */
  .social-block{ margin-top:12px; display:flex; flex-direction:column; gap:6px; align-items:flex-start; }
  .follow-title{ margin:0; font-size:14px; color:var(--accent); cursor:pointer; transition: text-shadow .25s, transform .25s; }
  .follow-title:hover, .follow-title:focus{ text-shadow:0 0 6px var(--neon1), 0 0 14px var(--neon2); transform: translateY(-2px); outline:none; }

  .social-icons{ display:flex; gap:12px; margin-top:6px; }
  .social-icons a{
    display:inline-flex;
    width:36px;
    height:36px;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    background:transparent;
    transition: transform var(--transition), box-shadow var(--transition);
    text-decoration:none;
    border:1px solid transparent;
    padding:6px;
  }
  .social-icons img{
    width:20px; height:20px;
    transition: transform var(--transition), filter var(--transition);
    filter: invert(22%) sepia(20%) saturate(800%) hue-rotate(230deg);
  }
  .social-icons a:hover img, .social-icons a:focus img{ transform: translateY(-6px) scale(1.08); filter:brightness(0) invert(0%); }
  .social-icons a:focus{ box-shadow:0 8px 24px rgba(0,0,0,0.08); outline:none; border-color:rgba(123,63,228,0.12); }

  /* Newsletter small */
  .newsletter{
    margin-top:12px;
    display:flex;
    gap:8px;
    align-items:center;
    width:100%;
    max-width:420px;
  }
  .newsletter input[type="email"]{
    flex:1;
    padding:10px 12px;
    border-radius:10px;
    border:1px solid rgba(0,0,0,0.06);
    background:rgba(255,255,255,0.5);
    font-size:14px;
  }
  .newsletter button{
    padding:10px 12px;
    border-radius:10px;
    border:0;
    background:linear-gradient(90deg,var(--accent),var(--neon2));
    color:white;
    font-weight:700;
    cursor:pointer;
    transition: transform var(--transition), box-shadow var(--transition);
  }
  .newsletter button:focus, .newsletter button:hover{ transform:translateY(-3px); box-shadow:0 10px 28px rgba(123,63,228,0.12); outline:none; }

  /* Neon dividing line */
  .neon-line{
    margin: 18px auto 0;
    height:3px;
    max-width:var(--max);
    background: linear-gradient(90deg, var(--neon1), var(--neon2), var(--neon1));
    background-size:300% 100%;
    animation: neonRun 6s linear infinite;
    border-radius:3px;
  }
  @keyframes neonRun{ 0%{background-position:0% 0;} 50%{background-position:100% 0;} 100%{background-position:0% 0;} }

  /* Footer bottom */
  .footer-bottom{
    max-width:var(--max);
    margin:12px auto 26px;
    color:#3e2b5f;
    text-align:center;
    font-size:13px;
    padding:10px 20px;
    z-index:4;
    position:relative;
  }
  .footer-bottom a{ color:var(--dark-text); text-decoration:none; }
  .footer-bottom a:hover, .footer-bottom a:focus{ color:#000; text-decoration:underline; outline:none; }

  /* Accessibility focus helpers */
  a:focus, button:focus, input:focus { box-shadow: 0 0 0 3px rgba(123,63,228,0.12); border-radius:8px; }

  /* Responsive breakpoints */
  @media (max-width: 1100px) {
    .footer-container{ grid-template-columns:1fr 1fr; gap:18px; }
    .footer-bg-logo{ width:900px; height:900px; top:56%; transform:translate(-50%,-48%); }
  }
  @media (max-width: 900px) {
    .footer-container{ grid-template-columns:1fr 1fr; padding:0 16px; }
    .pay-ico{ width:72px; height:36px; }
    .footer-bg-logo{ width:700px; height:700px; top:60%; opacity:0.05; transform:translate(-50%,-46%); }
  }
  @media (max-width: 600px) {
    .footer-container{ grid-template-columns:1fr; gap:18px; padding:0 14px; }
    .brand-logo-title{ gap:8px; }
    .newsletter{ flex-direction:column; align-items:stretch; }
    .newsletter button{ width:100%; }
    .neon-line{ margin-top:16px; }
    .footer-bg-logo{ width:520px; height:520px; top:65%; opacity:0.04; transform:translate(-50%,-40%); }
  }

  /* Reduced motion */
  @media (prefers-reduced-motion: reduce) {
    .neon-line, .cod-badge, .pay-card, .brand-title::before { transition: none !important; animation: none !important; transform: none !important; }
  }
</style>

<!-- FULL FOOTER -->
<footer id="barterbay-footer" class="footer-modern" role="contentinfo" aria-label="Barter Bay site footer">

  <div class="footer-bg-overlay" aria-hidden="true"></div>
  <div class="footer-bg-logo" aria-hidden="true"></div>

  <div class="footer-container">

    <!-- About / Brand -->
    <div class="footer-col about" aria-label="About Barter Bay">

      <!-- Foreground logo placed next to the title -->
      <div class="brand-logo-title">
        <img src="images/seal.png" alt="Barter Bay seal logo" loading="lazy" />
        <h3 class="brand-title">Barter Bay</h3>
      </div>

      <p class="muted">
        <strong>Barter Bay</strong> is a modern platform for secure buying, selling, and trading — built on trust, transparency and user-first design.
      </p>

      <p class="muted small">
        <strong>Customer Support:</strong><br>
        Mon – Sat • 9:00 AM to 7:00 PM
      </p>

      <p class="small muted" style="margin-top:8px;">
        <em>We verify listings and provide buyer protection — always check seller ratings before trading.</em>
      </p>
    </div>

    <!-- Quick Links -->
    <nav class="footer-col links" aria-label="Footer quick links">
      <h4>Quick Links</h4>
      <ul class="quick" role="list">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="trade.php">Trade Items</a></li>
        <li><a href="cart.php">Your Cart</a></li>
        <li><a href="faq.php">FAQ</a></li>
      </ul>
    </nav>

    <!-- Our Commitment / Accessibility -->
    <div class="footer-col extra-info" aria-label="Our Commitment">
      <h4>Our Commitment</h4>
      <p class="muted small">
        At Barter Bay, we promise a seamless and secure marketplace experience. Transactions are protected and user safety is prioritized.
      </p>

      <h4 style="margin-top:10px;">Contact Us</h4>
      <p class="muted small">
        Need help? Email us at <a href="mailto:support@barterbay.com" style="color:var(--accent); text-decoration:underline;">support@barterbay.com</a> or call <strong>+91 96887 48656</strong>
      </p>
    </div>

    <!-- Payments + Social -->
    <div class="footer-col payments" aria-label="Payments and social links">

      <div class="secure-title">
        <svg class="secure-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path fill="currentColor" d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4zm0 6a3 3 0 100 6 3 3 0 000-6zm0 10c2.7-.7 5-3.4 5-7V7.9L12 5 7 7.9V11c0 3.6 2.3 6.3 5 7z"/>
        </svg>
        <h4>Secure Payments</h4>
      </div>

      <div class="pay-card" role="group" aria-label="Payment options">
        <div class="pay-icons">
          <div class="pay-ico" title="Razorpay - Secure Online Payment">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Razorpay_logo.svg/512px-Razorpay_logo.svg.png" alt="Razorpay" loading="lazy">
          </div>

          <div class="cod-badge" role="img" aria-label="Cash on Delivery Available">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
              <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" fill="currentColor"/>
            </svg>
            <span>COD</span>
          </div>
        </div>
      </div>

      <div class="social-block" aria-label="Follow Barter Bay">
        <h4 class="follow-title">Follow Us</h4>
        <div class="social-icons" aria-label="Social media links">
          <a href="https://facebook.com" target="_blank" aria-label="Follow us on Facebook" rel="noopener noreferrer">
            <img src="https://img.icons8.com/ios-filled/30/facebook-new.png" alt="Facebook" loading="lazy">
          </a>
          <a href="https://instagram.com" target="_blank" aria-label="Follow us on Instagram" rel="noopener noreferrer">
            <img src="https://img.icons8.com/ios-filled/30/instagram-new.png" alt="Instagram" loading="lazy">
          </a>
          <a href="https://twitter.com" target="_blank" aria-label="Follow us on Twitter" rel="noopener noreferrer">
            <img src="https://img.icons8.com/ios-filled/30/twitter.png" alt="Twitter" loading="lazy">
          </a>
          <a href="https://youtube.com" target="_blank" aria-label="Subscribe on YouTube" rel="noopener noreferrer">
            <img src="https://img.icons8.com/ios-filled/30/youtube-play.png" alt="YouTube" loading="lazy">
          </a>
        </div>
      </div>

    </div>
  </div>

  <div class="neon-line" aria-hidden="true"></div>

  <div class="footer-bottom">
    <span id="copyright-year"></span>
    &nbsp;Barter Bay • All Rights Reserved&nbsp;•&nbsp;
    <a href="contact.php">Contact</a>&nbsp;•&nbsp;
    <a href="faq.php">FAQ</a>
  </div>
</footer>

<script>
(function() {
  'use strict';
  
  // Set copyright year dynamically
  try {
    var yearEl = document.getElementById('copyright-year');
    if (yearEl) {
      yearEl.textContent = '© ' + new Date().getFullYear();
    }
  } catch(e) {
    console.error('Error setting copyright year:', e);
  }
})();
</script>
