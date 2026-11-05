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
