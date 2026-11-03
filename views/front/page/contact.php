<?php
$page_title = 'Contact Us | Infinity Vape Shop';
$active_page = 'contact';

$page_schema = <<<'SCHEMA'
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "ContactPage",
        "@id": "https://infinityvapeshop.com/page/contact-us#webpage",
        "url": "https://infinityvapeshop.com/page/contact-us",
        "name": "Contact Us | Infinity Vape Shop",
        "description": "Have questions or need help with your order? Contact Infinity Vape Shop directly at support@infinityvapeshop.com or send us a message.",
        "isPartOf": {
          "@id": "https://infinityvapeshop.com/#website"
        },
        "about": {
          "@id": "https://infinityvapeshop.com/#organization"
        },
        "inLanguage": "en-US"
      },
      {
        "@type": "Organization",
        "@id": "https://infinityvapeshop.com/#organization",
        "name": "Infinity Vape Shop",
        "url": "https://infinityvapeshop.com/",
        "email": "support@infinityvapeshop.com",
        "contactPoint": {
          "@type": "ContactPoint",
          "contactType": "customer service",
          "email": "support@infinityvapeshop.com",
          "areaServed": "US",
          "availableLanguage": "English"
        }
      }
    ]
  }
  </script>
  SCHEMA;

include dirname(__DIR__) . '/includes/header.php';
?>

<style>
.contact-page-wrapper {
  max-width: 640px;
  margin: 0 auto;
  padding: 30px 16px 80px;
}
.contact-header {
  text-align: left;
  margin-bottom: 28px;
}
.contact-title {
  font-size: 2.2rem;
  font-weight: 800;
  color: #0b2545;
  margin: 0 0 10px;
  letter-spacing: -0.02em;
}
.contact-subtitle {
  font-size: 15px;
  color: #64748b;
  line-height: 1.65;
  margin: 0;
  max-width: 100%;
}
.contact-subtitle a {
  color: #fa5a1e;
  font-weight: 700;
  text-decoration: underline;
  transition: color 0.15s ease;
}
.contact-subtitle a:hover {
  color: #e0450d;
}
.contact-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 32px 28px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}
.contact-card-title {
  font-size: 19px;
  font-weight: 800;
  color: #0b2545;
  margin-top: 0;
  margin-bottom: 22px;
}
.contact-form-group {
  margin-bottom: 18px;
}
.contact-form-label {
  display: block;
  font-size: 13.5px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
}
.contact-form-label .req {
  color: #ef4444;
  margin-left: 2px;
}
.contact-form-input,
.contact-form-textarea {
  width: 100%;
  padding: 11px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 14.5px;
  font-family: inherit;
  color: #0f172a;
  background: #ffffff;
  box-sizing: border-box;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.contact-form-input:focus,
.contact-form-textarea:focus {
  outline: none;
  border-color: #0b2545;
  box-shadow: 0 0 0 3px rgba(11, 37, 69, 0.08);
}
.contact-form-textarea {
  resize: vertical;
  min-height: 120px;
  line-height: 1.6;
}
.btn-contact-submit {
  width: 100%;
  padding: 13px 24px;
  font-size: 15px;
  font-weight: 700;
  border-radius: 8px;
  cursor: pointer;
  border: none;
  background: #0b1a30;
  color: #ffffff;
  letter-spacing: 0.01em;
  transition: all 0.2s ease;
  margin-top: 6px;
}
.btn-contact-submit:hover {
  background: #fa5a1e;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(250, 90, 30, 0.25);
}
.contact-alert {
  padding: 14px 18px;
  border-radius: 8px;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 22px;
}
.contact-alert.success {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}
.contact-alert.error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}
@media (max-width: 576px) {
  .contact-page-wrapper {
    padding: 20px 14px 60px;
  }
  .contact-title {
    font-size: 1.75rem;
  }
  .contact-subtitle {
    font-size: 14px;
  }
  .contact-card {
    padding: 22px 18px;
  }
}
</style>

<!-- ==========================================
     1. BREADCRUMBS
     ========================================== -->
<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item">
        <a href="<?= url() ?>">Home</a>
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        Contact Us
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. CONTACT FORM (CENTERED CLEAN CARD)
     ========================================== -->
<div class="contact-page-wrapper">
  
  <header class="contact-header">
    <h1 class="contact-title">Contact Us</h1>
    <p class="contact-subtitle">
      Have questions or need help with your order? Reach out to us directly at <a href="mailto:support@infinityvapeshop.com">support@infinityvapeshop.com</a> or send us a message below.
    </p>
  </header>

  <?php if (!empty($success)): ?>
    <div class="contact-alert success">
      <strong>Success!</strong> <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="contact-alert error">
      <strong>Error:</strong> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="contact-card">
    <h2 class="contact-card-title">Send Us a Message</h2>
    
    <form action="<?= url('page/contact-us') ?>" method="POST" novalidate>
      <?= csrf_field() ?>
      
      <!-- 1. Name -->
      <div class="contact-form-group">
        <label for="contactName" class="contact-form-label">Your Name <span class="req">*</span></label>
        <input 
          type="text" 
          id="contactName" 
          name="name" 
          class="contact-form-input" 
          placeholder="John Doe" 
          required 
          autocomplete="name"
          value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
        >
      </div>

      <!-- 2. Email -->
      <div class="contact-form-group">
        <label for="contactEmail" class="contact-form-label">Email Address <span class="req">*</span></label>
        <input 
          type="email" 
          id="contactEmail" 
          name="email" 
          class="contact-form-input" 
          placeholder="john@example.com" 
          required 
          autocomplete="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        >
      </div>

      <!-- 3. Phone -->
      <div class="contact-form-group">
        <label for="contactPhone" class="contact-form-label">Phone Number (Optional)</label>
        <input 
          type="tel" 
          id="contactPhone" 
          name="phone" 
          class="contact-form-input" 
          placeholder="+1 (555) 000-0000" 
          autocomplete="tel"
          value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
        >
      </div>

      <!-- 4. Message -->
      <div class="contact-form-group">
        <label for="contactMessage" class="contact-form-label">Message <span class="req">*</span></label>
        <textarea 
          id="contactMessage" 
          name="message" 
          rows="5" 
          class="contact-form-textarea" 
          placeholder="Please describe how we can help you..." 
          required
        ><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-contact-submit">
        Send Message
      </button>

    </form>
  </div>

</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
