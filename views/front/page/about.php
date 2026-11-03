<?php
$page_title = 'About Us | Authentic Vape Superstore | Infinity Vape Shop';
$active_page = 'about';

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
        About Us
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. ABOUT US SECTION
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container">
      
      <header class="legal-page-header">
        <h1 class="legal-page-title">About Infinity Vape Shop</h1>
        <p class="legal-page-meta">Every Flavor. In Stock. Premium Quality Guaranteed.</p>
      </header>

      <div class="legal-page-content">
        <p class="legal-page-intro">
          Welcome to Infinity Vape Shop, your trusted American e-commerce destination for authentic vaping devices, nicotine salt e-liquids, and modern oral nicotine alternatives.
        </p>

        <h2 class="legal-section-title">Our Mission</h2>
        <p>
          Founded by vaping enthusiasts and industry veterans, Infinity Vape Shop was created with a straightforward mission: to give adult smokers and vapers reliable access to verified, authentic products at competitive prices, backed by responsive customer support and speedy fulfillment.
        </p>

        <h2 class="legal-section-title">100% Guaranteed Authenticity</h2>
        <p>
          Counterfeit products have no place in our inventory. Every brand we carry — from Geek Bar, Lost Mary, and RAZ to Juice Head, Tyson 2.0, and Vaporesso — is procured exclusively through certified distribution channels. Every disposable and bottle comes sealed with anti-counterfeit QR codes and scratch-off security seals.
        </p>

        <p>
          <strong>Underage Sale Prevention:</strong> We enforce strict 21+ age verification on all purchases in compliance with federal FDA regulations and state laws.
        </p>

        <h2 class="legal-section-title">Why Choose Infinity Vape Shop?</h2>
        <ul>
          <li><strong>Largest In-Stock Selection:</strong> Over 100+ top flavors from industry-leading manufacturers.</li>
          <li><strong>Compliant & Fast Shipping:</strong> Orders packaged within 24-48 business hours with adult signature delivery.</li>
          <li><strong>Dedicated Support:</strong> Real team members available to answer technical questions and assist with orders.</li>
        </ul>

      </div>

    </div>
  </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

