<?php
$page_title = 'FDA Disclaimer | Infinity Vape Shop';
$meta_description = 'Read Infinity Vape Shop FDA Disclaimer for information on FDA evaluation, product claims, medical guidance, age requirements, and PACT Act compliance.';
$active_page = 'fda-disclaimer';

$page_schema = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "https://infinityvapeshop.com/page/fda-disclaimer#webpage",
      "url": "https://infinityvapeshop.com/page/fda-disclaimer",
      "name": "FDA Disclaimer | Infinity Vape Shop",
      "description": "Infinity Vape Shop FDA Disclaimer for information on FDA evaluation, product claims, medical guidance, age requirements, and PACT Act compliance.",
      "isPartOf": {
        "@id": "https://infinityvapeshop.com/#website"
      },
      "about": {
        "@id": "https://infinityvapeshop.com/#organization"
      },
      "publisher": {
        "@id": "https://infinityvapeshop.com/#organization"
      },
      "inLanguage": "en-US"
    },
    {
      "@type": "WebSite",
      "@id": "https://infinityvapeshop.com/#website",
      "url": "https://infinityvapeshop.com/",
      "name": "Infinity Vape Shop",
      "publisher": {
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
      "telephone": "+1-971-754-2825",
      "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer support",
        "email": "support@infinityvapeshop.com",
        "telephone": "+1-971-754-2825",
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
        FDA Disclaimer
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. FDA DISCLAIMER CONTENT
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container">
      
      <header class="legal-page-header">
        <h1 class="legal-page-title">FDA Disclaimer</h1>
      </header>

      <div class="legal-page-content">
        <p class="legal-page-intro">
          The statements made about these products have not been evaluated by the U.S. Food and Drug Administration (FDA).
        </p>
        <p>
          These products are not intended to diagnose, treat, cure, or prevent any disease. The effectiveness of these products and any customer testimonials have not been verified by FDA-approved research.
        </p>
        <p>
          The information provided here is for general informational purposes only and is not intended to replace professional medical advice, diagnosis, or treatment.
        </p>
        <p>
          Always consult with your healthcare provider before using any product, especially if you are:
        </p>
        <ul>
          <li>Pregnant</li>
          <li>Nursing</li>
          <li>Taking medications</li>
          <li>Living with a medical condition</li>
          <li>Under 21 years of age</li>
        </ul>

        <!-- Medical Claims & Product Use -->
        <h2 class="legal-section-title">Medical Claims &amp; Product Use</h2>
        <p>
          MiOne Brands™ does not accept liability for any medical claims made by customers or for the misuse of any products.
        </p>
        <p>
          We recommend that all users seek guidance from a qualified medical professional when considering the use of these products as part of a treatment plan.
        </p>

        <!-- Age Requirement & PACT Act Compliance -->
        <h2 class="legal-section-title">Age Requirement &amp; PACT Act Compliance</h2>
        <p>
          You must be 21 years or older to access this website and/or purchase MiOne Brands™ products.
        </p>
        <p>
          All products are shipped in compliance with the PACT Act.
        </p>

        <!-- Kratom Disclaimer -->
        <h2 class="legal-section-title">Kratom Disclaimer</h2>
        <p>
          Please note: Kratom has not been approved by the FDA as a dietary supplement.
        </p>
        <p>
          We do not ship Kratom products to U.S. states or municipalities where its sale or possession is restricted, including but not limited to:
        </p>
        <ul>
          <li>Alabama</li>
          <li>Arkansas</li>
          <li>Indiana</li>
          <li>Louisiana</li>
          <li>Rhode Island</li>
          <li>Vermont</li>
          <li>Wisconsin</li>
          <li>Sarasota County (FL)</li>
          <li>San Diego (CA)</li>
          <li>Oceanside (CA)</li>
          <li>Alton (IL)</li>
          <li>Jerseyville (IL)</li>
          <li>Edwardsville County (IL)</li>
          <li>Columbus (MS)</li>
          <li>Union County (MS)</li>
        </ul>

      </div>

    </div>
  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
