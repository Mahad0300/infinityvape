<?php
$page_title = 'Terms & Conditions | Infinity Vape Shop';
$meta_description = 'Read Infinity Vape Shop Terms & Conditions covering age requirements, orders, payments, shipping, returns, product information, and website use.';
$active_page = 'terms';

$page_schema = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "https://infinityvapeshop.com/page/terms-and-conditions#webpage",
      "url": "https://infinityvapeshop.com/page/terms-and-conditions",
      "name": "Terms and Conditions | Infinity Vape Shop",
      "description": "Read Infinity Vape Shop Terms and Conditions covering age requirements, orders, payments, shipping, returns, product information and website use.",
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
        Terms &amp; Conditions
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. TERMS CONTENT
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container">
      
      <header class="legal-page-header">
        <h1 class="legal-page-title">Terms &amp; Conditions</h1>
      </header>

      <div class="legal-page-content">
        <p class="legal-page-intro">
          Welcome to Infinity Vape Shop! By browsing or buying from Infinityvapeshop.com, you're agreeing to the terms below. Read them in two minutes; they're written to be readable, not riddles!
        </p>

        <!-- Section 1 -->
        <h2 class="legal-section-title">1. Age Requirement</h2>
        <p>
          Anyone using or buying any products on this Site must be 21 years or older (or the legal age to purchase or use in your state, if applicable). When you place an order you agree to this requirement. Age verification is a possibility at any time and all orders that fail verification will be cancelled.
        </p>

        <!-- Section 2 -->
        <h2 class="legal-section-title">2. Use of the Site</h2>
        <p>
          This site is designed for a wide range of users, from novice to expert.
        </p>
        <p>
          The use of this Site is intended for adults in the United States only for personal non-commercial use. You are not to resell any of our products commercially without our written permission and you are not to use the Site for any illegal purpose.
        </p>

        <!-- Section 3 -->
        <h2 class="legal-section-title">3. Product Information</h2>
        <p>
          Our product descriptions, images and pricing are constantly strived to maintain accuracy. In some cases mistakes occur a price mistake, a misreported flavor, a mismatched picture. If a mistake is discovered prior to your order being shipped, we will reach out to you before continuing.
        </p>
        <p>
          Vaping products are made with an addictive chemical called nicotine. Products are offered ‘as is' and are only for adult use and not intended for use by non-smokers or pregnant women or those with a nicotine sensitivity.
        </p>

        <!-- Section 4 -->
        <h2 class="legal-section-title">4. Orders &amp; Payment</h2>
        <p>
          Order is just an offer once they place an order it is not a 'sale' until we accept and process it.
        </p>
        <p>
          We accept the payment methods listed at checkout. Unless otherwise indicated all prices are in USD not including applicable taxes and shipping.
        </p>
        <p>
          We reserve the right to refuse and cancel orders for any reason, even if it is suspected fraud, a pricing error or verification of age.
        </p>

        <!-- Section 5 -->
        <h2 class="legal-section-title">5. Shipping &amp; Delivery</h2>
        <p>
          Shipping times are approximate. Carriers, weather and demand delays can occur and are out of our control.
        </p>
        <p>
          Vape shipments are limited or banned in some states; you should check on local regulations before placing an order. Restricted area orders may be cancelled and will be refunded.
        </p>

        <!-- Section 6 -->
        <h2 class="legal-section-title">6. Returns &amp; Refunds</h2>
        <p>
          For details on eligibility, timelines and process please refer to our separate <a href="<?= url('page/refund-policy') ?>">Return Policy</a> page.
        </p>

        <!-- Section 7 -->
        <h2 class="legal-section-title">7. Intellectual Property</h2>
        <p>
          All logos, product photos, written content, design on this Site is the property of Infinity Vape Shop or our licensed partners.
        </p>
        <p>
          It is strictly prohibited to copy, reproduce or repurpose it without written permission.
        </p>

        <!-- Section 8 -->
        <h2 class="legal-section-title">8. Limitation of Liability</h2>
        <p>
          We offer this Site "as is".
        </p>
        <p>
          To the maximum extent permitted by law, Infinity Vape Shop shall not be held responsible for any indirect, incidental or consequential damages that may result from your use of any of our products or Site.
        </p>
        <p>
          No rights are here accorded to which may not practically be waived.
        </p>

        <!-- Section 9 -->
        <h2 class="legal-section-title">9. Changes to These Terms</h2>
        <p>
          These Terms may be amended from time to time as our business grows and/or the law changes.
        </p>
        <p>
          If you continue to use the Site after the change, you agree to take this new version of the Site.
        </p>
        <p>
          The date on the top will always refer to the most recent revision.
        </p>

        <!-- Section 10 -->
        <h2 class="legal-section-title">10. Governing Law</h2>
        <p>
          The Terms will be governed by the laws of the United States and the state where our business is registered, without taking into account the principles of conflicts of law.
        </p>

        <!-- Section 11 -->
        <h2 class="legal-section-title">11. Contact Us</h2>
        <p>
          Questions about these Terms? Reach out:
        </p>
        <p>
          <strong>Email:</strong> <a href="mailto:support@infinityvapeshop.com">support@infinityvapeshop.com</a>
        </p>

      </div>

    </div>
  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>

