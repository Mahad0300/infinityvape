<?php
$page_title = 'Shipping Policy | Infinity Vape Shop';
$meta_description = 'Read Infinity Vape Shop Shipping Policy for processing times, delivery estimates, age verification, tracking, shipping costs, and U.S. restrictions.';
$active_page = 'shipping-policy';

$page_schema = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "https://infinityvapeshop.com/page/shipping-policy#webpage",
      "url": "https://infinityvapeshop.com/page/shipping-policy",
      "name": "Shipping Policy | Infinity Vape Shop",
      "description": "Read Infinity Vape Shop Shipping Policy for processing times, delivery estimates, age verification, tracking, shipping costs, and U.S. restrictions.",
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
      "hasShippingService": [
        {
          "@type": "ShippingService",
          "@id": "https://infinityvapeshop.com/page/shipping-policy#standard-shipping",
          "name": "Standard Shipping",
          "description": "Standard shipping to eligible U.S. locations. Shipping charges are calculated at checkout. Location restrictions may apply.",
          "fulfillmentType": "https://schema.org/FulfillmentTypeDelivery",
          "handlingTime": {
            "@type": "ServicePeriod",
            "duration": {
              "@type": "QuantitativeValue",
              "minValue": 1,
              "maxValue": 2,
              "unitCode": "DAY"
            },
            "businessDays": [
              "https://schema.org/Monday",
              "https://schema.org/Tuesday",
              "https://schema.org/Wednesday",
              "https://schema.org/Thursday",
              "https://schema.org/Friday"
            ]
          },
          "shippingConditions": {
            "@type": "ShippingConditions",
            "shippingDestination": {
              "@type": "DefinedRegion",
              "addressCountry": "US"
            },
            "transitTime": {
              "@type": "ServicePeriod",
              "duration": {
                "@type": "QuantitativeValue",
                "minValue": 3,
                "maxValue": 7,
                "unitCode": "DAY"
              },
              "businessDays": [
                "https://schema.org/Monday",
                "https://schema.org/Tuesday",
                "https://schema.org/Wednesday",
                "https://schema.org/Thursday",
                "https://schema.org/Friday"
              ]
            }
          }
        },
        {
          "@type": "ShippingService",
          "@id": "https://infinityvapeshop.com/page/shipping-policy#express-shipping",
          "name": "Express Shipping",
          "description": "Express shipping to eligible U.S. locations. Shipping charges are calculated at checkout. Location restrictions may apply.",
          "fulfillmentType": "https://schema.org/FulfillmentTypeDelivery",
          "handlingTime": {
            "@type": "ServicePeriod",
            "duration": {
              "@type": "QuantitativeValue",
              "minValue": 1,
              "maxValue": 2,
              "unitCode": "DAY"
            },
            "businessDays": [
              "https://schema.org/Monday",
              "https://schema.org/Tuesday",
              "https://schema.org/Wednesday",
              "https://schema.org/Thursday",
              "https://schema.org/Friday"
            ]
          },
          "shippingConditions": {
            "@type": "ShippingConditions",
            "shippingDestination": {
              "@type": "DefinedRegion",
              "addressCountry": "US"
            },
            "transitTime": {
              "@type": "ServicePeriod",
              "duration": {
                "@type": "QuantitativeValue",
                "minValue": 2,
                "maxValue": 3,
                "unitCode": "DAY"
              },
              "businessDays": [
                "https://schema.org/Monday",
                "https://schema.org/Tuesday",
                "https://schema.org/Wednesday",
                "https://schema.org/Thursday",
                "https://schema.org/Friday"
              ]
            }
          }
        }
      ]
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
        Shipping Policy
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. SHIPPING POLICY CONTENT
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container">
      
      <header class="legal-page-header">
        <h1 class="legal-page-title">Shipping Policy</h1>
      </header>

      <div class="legal-page-content">
        <p class="legal-page-intro">
          Ordering is the easy part. Once you place your order, here’s what happens next and what you can expect from us.
        </p>

        <!-- Section 1 -->
        <h2 class="legal-section-title">1. Processing Time</h2>
        <p>
          Most orders are verified and processed within 1–2 business days, Monday through Friday, excluding public holidays.
        </p>
        <p>
          As soon as your order ships, we’ll send you a confirmation email with your tracking details so you can follow your package along the way.
        </p>

        <!-- Section 2 -->
        <h2 class="legal-section-title">2. Shipping Times</h2>
        
        <div class="legal-table-responsive">
          <table class="legal-custom-table">
            <thead>
              <tr>
                <th style="width: 50%;">Shipping Method</th>
                <th>Estimated Delivery Time</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Standard Shipping</strong></td>
                <td>3–7 business days</td>
              </tr>
              <tr>
                <td><strong>Express Shipping</strong></td>
                <td>2–3 business days</td>
              </tr>
            </tbody>
          </table>
        </div>

        <p>
          Delivery estimates begin once your order has been shipped, not from the date the order was placed.
        </p>
        <p>
          While we do our best to keep deliveries on schedule, delays caused by weather, carriers, holidays, or periods of high demand can occasionally happen.
        </p>

        <!-- Section 3 -->
        <h2 class="legal-section-title">3. Age Verification on Delivery</h2>
        <p>
          Because we sell age-restricted products, shipments require signature confirmation from someone who is 21 years of age or older.
        </p>
        <p>
          If an eligible adult is not available when delivery is attempted, the carrier may try again or hold the package at a nearby location for pickup.
        </p>

        <!-- Section 4 -->
        <h2 class="legal-section-title">4. Where We Can and Can’t Ship</h2>
        <p>
          Vaping products are regulated under the PACT Act, which affects how nicotine and vaping products can be shipped within the United States.
        </p>
        <p>Because of these requirements:</p>
        <ul>
          <li>We use eligible private carriers for vape shipments rather than the U.S. Postal Service.</li>
          <li>Some states, cities, and local areas restrict or prohibit the delivery of vaping products.</li>
          <li>We are unable to complete orders going to locations where shipping these products is restricted.</li>
          <li>We do not currently offer international shipping.</li>
          <li>If we’re unable to ship your order because of a location restriction, we’ll contact you and issue a full refund.</li>
        </ul>

        <!-- Section 5 -->
        <h2 class="legal-section-title">5. Shipping Costs</h2>
        <p>
          Shipping charges are calculated during checkout based on factors such as your delivery location, order weight, and selected shipping method.
        </p>
        <p>
          If we are running a free-shipping offer or your order qualifies for a free-shipping threshold, you’ll see it clearly displayed in your cart or during checkout.
        </p>

        <!-- Section 6 -->
        <h2 class="legal-section-title">6. Tracking Your Order</h2>
        <p>
          Once your order ships, we’ll email you a tracking link.
        </p>
        <p>
          Tracking information can sometimes take a little time to update after the carrier receives the package. If your tracking hasn’t shown any movement for 3 or more business days, contact us and we’ll help look into it with the carrier.
        </p>

        <!-- Section 7 -->
        <h2 class="legal-section-title">7. Lost or Delayed Packages</h2>
        <p>
          If your tracking shows that your package has been delivered but you can’t find it, or if your shipment is significantly delayed, please contact us within 7 days.
        </p>
        <p>
          We’ll review the tracking information and, when necessary, work with the carrier to investigate the issue.
        </p>

        <!-- Section 8 -->
        <h2 class="legal-section-title">8. Questions?</h2>
        <p>
          Need help with your shipment or have a question about your order? Get in touch with us:
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
