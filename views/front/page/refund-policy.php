<?php
$page_title = 'Refund Policy | Infinity Vape Shop';
$meta_description = 'Read Infinity Vape Shop Refund Policy for details on 14-day returns, damaged or wrong items, eligible products, refunds, and return steps.';
$active_page = 'refund-policy';

$page_schema = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "https://infinityvapeshop.com/page/refund-policy#webpage",
      "url": "https://infinityvapeshop.com/page/refund-policy",
      "name": "Refund Policy | Infinity Vape Shop",
      "description": "Read Infinity Vape Shop Refund Policy for details on 14-day returns, damaged or wrong items, eligible products, refunds, and return steps.",
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
      },
      "hasMerchantReturnPolicy": {
        "@type": "MerchantReturnPolicy",
        "@id": "https://infinityvapeshop.com/page/refund-policy#return-policy",
        "merchantReturnLink": "https://infinityvapeshop.com/page/refund-policy",
        "applicableCountry": "US",
        "returnPolicyCountry": "US",
        "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
        "merchantReturnDays": 14,
        "returnMethod": "https://schema.org/ReturnByMail",
        "itemCondition": [
          "https://schema.org/NewCondition",
          "https://schema.org/DamagedCondition"
        ],
        "refundType": [
          "https://schema.org/FullRefund",
          "https://schema.org/StoreCreditRefund"
        ]
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
        Refund Policy
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. REFUND POLICY CONTENT
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container">
      
      <header class="legal-page-header">
        <h1 class="legal-page-title">Refund Policy</h1>
      </header>

      <div class="legal-page-content">
        <p class="legal-page-intro">
          We want you to enjoy ordering what you get. When it doesn't go down as it should, here's exactly how to deal with it no small print gotchas, no running around.
        </p>

        <!-- Section 1 -->
        <h2 class="legal-section-title">1. Defective or Damaged Product</h2>
        <p>
          We have got you covered in case the item is faulty, damaged or doesn't work as it should.
        </p>
        <p>
          Please call us within 7 days of delivery with the order number and a quick picture or video of the problem. If verified, we will replace it or provide a refund (your choice).
        </p>

        <!-- Section 2 -->
        <h2 class="legal-section-title">2. Wrong Item Received</h2>
        <p>
          Mistakes happen. If we send you the wrong product, please contact us within 7 days of your delivery and we will send the correct product to you immediately without sending you any extra charges.
        </p>

        <!-- Section 3 -->
        <h2 class="legal-section-title">3. What We Can't Accept Back</h2>
        <p>
          The following items are final sale for various reasons, including hygiene, safety and regulations that apply to the vaping industry in general:
        </p>
        <ul>
          <li>Used or opened e-liquids</li>
          <li>Vapes that have been used and are not to be reused.</li>
          <li>Opened Pod Kits, coils or pods for replacement.</li>
          <li>Nicotine pouches</li>
        </ul>
        <p>
          This is not being picky, it's a common sense, health-focused policy from all responsible vape shops and it’s good for all our customers that shop from us.
        </p>

        <!-- Section 4 -->
        <h2 class="legal-section-title">4. Unopened, Unused Items</h2>
        <p>
          Were you thinking of changing your mind? If items are not opened, they can be sent back within 14 days of delivery for a store credit or a refund (with the exception of any original shipping fees).
        </p>

        <!-- Section 5 -->
        <h2 class="legal-section-title">5. How to Start the Return</h2>
        <p>
          The steps to begin a return:
        </p>
        <ul>
          <li>Please contact us by e-mail for your order number and reason for return.</li>
          <li>Eligibility will be determined and instructions for return will be sent.</li>
          <li>Return the product in the original container.</li>
          <li>We will process your refund/replacement in 5–7 business days after you receive and inspect it.</li>
        </ul>

        <!-- Section 6 -->
        <h2 class="legal-section-title">6. Refund Method</h2>
        <p>
          Refunds are issued back to the payment method you used to pay.
        </p>
        <p>
          This time may vary based on your bank or card provider, and may take a couple of additional business days before it appears in your statement.
        </p>

        <!-- Section 7 -->
        <h2 class="legal-section-title">7. Orders for Non-Standard Items, or Those That Might Be Unavailable</h2>
        <p>
          A full refund will be issued to those who place an order that fails age verification, is refused due to shipping restrictions to their state or suspected fraud.
        </p>

        <!-- Section 8 -->
        <h2 class="legal-section-title">8. Questions?</h2>
        <p>
          We're not your distant service person, we're a person who's happy to help. Reach out anytime:
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
