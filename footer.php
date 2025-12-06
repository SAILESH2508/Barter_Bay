<style>
  :root{
    --footer-bg: #0d0d0d;
    --footer-text: #e0e0e0;
    --footer-muted: #94a3b8;
    --accent-red: #ef233c;
    --accent-blue: #3a86ff;
    --footer-gradient: linear-gradient(135deg, #ef233c 0%, #3a86ff 100%);
    --max: 1200px;
    --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --logo-url: url("images/seal.png?v=1");
  }

  .footer-modern,
  .footer-modern * { box-sizing: border-box; font-family: system-ui, -apple-system, sans-serif; }

  .footer-modern {
    position: relative;
    background: var(--footer-bg);
    color: var(--footer-text);
    padding: 60px 0 20px;
    border-top: 4px solid var(--accent-red);
    overflow: hidden;
  }

  /* Watermark logo background */
  .footer-bg-logo {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 600px;
    height: 600px;
    background-image: var(--logo-url);
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.03;
    filter: grayscale(100%);
    z-index: 1;
    pointer-events: none;
  }

  .footer-container {
    position: relative;
    z-index: 2;
    max-width: var(--max);
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
    gap: 40px;
  }

  .footer-col { display: flex; flex-direction: column; gap: 15px; }

  .brand-logo-title { display: flex; align-items: center; gap: 15px; }
  .brand-logo-title img { width: 50px; height: 50px; object-fit: contain; }
  .brand-title { 
    font-size: 28px; 
    font-weight: 800; 
    background: var(--footer-gradient); 
    -webkit-background-clip: text; 
    -webkit-text-fill-color: transparent; 
    margin: 0;
  }

  .footer-col h4 {
    color: white;
    font-size: 18px;
    margin-bottom: 5px;
    border-left: 3px solid var(--accent-red);
    padding-left: 10px;
  }

  .footer-col p { font-size: 14px; line-height: 1.6; color: var(--footer-muted); margin: 0; }
  
  .quick-links { list-style: none; padding: 0; margin: 0; }
  .quick-links li { margin-bottom: 10px; }
  .quick-links a { 
    color: var(--footer-muted); 
    text-decoration: none; 
    transition: var(--transition);
    display: inline-block;
  }
  .quick-links a:hover { color: var(--accent-red); transform: translateX(5px); }

  .social-icons { display: flex; gap: 15px; margin-top: 10px; }
  .social-icons a {
    width: 40px; height: 40px;
    background: rgba(255,255,255,0.05);
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%; transition: var(--transition); border: 1px solid rgba(255,255,255,0.1);
  }
  .social-icons a:hover { background: var(--footer-gradient); border-color: transparent; transform: translateY(-5px); }
  .social-icons img { width: 20px; filter: invert(100%); }

  /* Newsletter Form */
  .newsletter-form { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
  .newsletter-input {
    padding: 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    border-radius: 8px;
    outline: none;
  }
  .newsletter-btn {
    padding: 12px;
    background: var(--footer-gradient);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: var(--transition);
  }
  .newsletter-btn:hover { opacity: 0.9; transform: scale(1.02); }

  .footer-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
    margin: 40px 0 20px;
  }

  .footer-bottom {
    text-align: center;
    font-size: 13px;
    color: var(--footer-muted);
    padding: 10px 0;
  }
  .footer-bottom strong { color: white; }

  @media (max-width: 900px) {
    .footer-container { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 600px) {
    .footer-container { grid-template-columns: 1fr; text-align: center; }
    .brand-logo-title { justify-content: center; }
    .footer-col h4 { border-left: none; padding-left: 0; }
    .social-icons { justify-content: center; }
  }
</style>

<footer class="footer-modern">
  <div class="footer-bg-logo"></div>
  
  <div class="footer-container">
    <div class="footer-col">
      <div class="brand-logo-title">
        <img src="images/seal.png?v=1" alt="Logo">
        <h3 class="brand-title">Barter Bay</h3>
      </div>
      <p>Your premium destination for secure bartering and trading. Join thousands of users exchanging value every day.</p>
      <div class="social-icons">
        <a href="#"><img src="https://img.icons8.com/ios-filled/50/facebook-new.png" alt="FB"></a>
        <a href="#"><img src="https://img.icons8.com/ios-filled/50/twitter.png" alt="TW"></a>
        <a href="#"><img src="https://img.icons8.com/ios-filled/50/instagram-new.png" alt="IG"></a>
        <a href="#"><img src="https://img.icons8.com/ios-filled/50/youtube-play.png" alt="YT"></a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul class="quick-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">All Products</a></li>
        <li><a href="trade.php">Trade Portal</a></li>
        <li><a href="faq.php">Help & FAQ</a></li>
        <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Contact Info</h4>
      <p>📍 Location: Salem, Tamil Nadu</p>
      <p>📧 Email: support@barterbay.com</p>
      <p>📞 Phone: +91 96887 48656</p>
      <p>⏰ Hours: Mon-Sat 9AM-7PM</p>
    </div>

    <div class="footer-col">
      <h4>Newsletter</h4>
      <p>Get the latest trades delivered to your inbox.</p>
      <form class="newsletter-form" id="newsletterForm">
        <input type="email" placeholder="Your Email" class="newsletter-input" required aria-label="Email for newsletter">
        <button type="submit" class="newsletter-btn">Subscribe</button>
      </form>
      <div id="newsletterMsg" style="display:none; color: var(--accent-blue); font-size: 14px; margin-top: 10px;">
        ✨ Thank you for subscribing!
      </div>
    </div>
  </div>

  <div class="footer-divider"></div>

  <div class="footer-bottom">
    <p>© <span id="year"></span> Barter Bay. All rights Reserved.</p>
    <p>Designed & Developed with ❤️ by <strong>Sailesh S</strong></p>
  </div>
</footer>

<script>
  document.getElementById('year').textContent = new Date().getFullYear();

  document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    this.style.display = 'none';
    document.getElementById('newsletterMsg').style.display = 'block';
  });
</script>
