<?php
$page_title = 'FAQs | Infinity Vape Shop';
$meta_description = 'Find answers to common questions about Infinity Vape Shop, including age verification, orders, shipping, tracking, returns, refunds, and vape products.';
$active_page = 'faqs';

$page_schema = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "FAQPage",
      "@id": "https://infinityvapeshop.com/page/faqs#webpage",
      "url": "https://infinityvapeshop.com/page/faqs",
      "name": "FAQs | Infinity Vape Shop",
      "description": "Find answers to common questions about Infinity Vape Shop, including age verification, orders, shipping, tracking, returns, refunds, and vape products.",
      "isPartOf": {
        "@id": "https://infinityvapeshop.com/#website"
      },
      "about": {
        "@id": "https://infinityvapeshop.com/#organization"
      },
      "publisher": {
        "@id": "https://infinityvapeshop.com/#organization"
      },
      "inLanguage": "en-US",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "How old do I need to be to order from Infinity Vape Shop?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "You must be 21 years of age or older to purchase vaping or nicotine products from Infinity Vape Shop. We may verify your age before processing an order to comply with applicable federal, state, and local requirements."
          }
        },
        {
          "@type": "Question",
          "name": "Why does Infinity Vape Shop verify my age?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Vaping and nicotine products are age-restricted in the United States. Age verification helps us confirm that customers meet the legal purchasing age before an order is processed or delivered."
          }
        },
        {
          "@type": "Question",
          "name": "What happens if my age cannot be verified?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "If we cannot confirm that you meet the legal purchasing age, we may ask for additional information or identification. Orders that fail age verification may be cancelled and refunded."
          }
        },
        {
          "@type": "Question",
          "name": "How do I place an order?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Choose your products, add them to your cart, and continue to checkout. You will need to provide your shipping and payment information and complete any required age verification before your order can be processed."
          }
        },
        {
          "@type": "Question",
          "name": "What payment methods do you accept?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Available payment methods are shown during checkout. Payments are processed securely through our payment providers, and your complete credit card number is not stored on Infinity Vape Shop servers."
          }
        },
        {
          "@type": "Question",
          "name": "Can I change or cancel my order after placing it?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Contact us as soon as possible if you need to change or cancel an order. We may be able to help if the order has not yet been processed or shipped. Once fulfillment has started, changes or cancellations may no longer be possible."
          }
        },
        {
          "@type": "Question",
          "name": "Why was my order cancelled?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "An order may be cancelled because of failed age verification, payment or verification issues, suspected fraudulent activity, shipping restrictions, incorrect order information, or product availability."
          }
        },
        {
          "@type": "Question",
          "name": "How long does Infinity Vape Shop take to process an order?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Most verified orders are processed within 1–2 business days, Monday through Friday, excluding holidays. Processing time is separate from delivery time."
          }
        },
        {
          "@type": "Question",
          "name": "How long does shipping take?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Standard Shipping is estimated at 3–7 business days and Express Shipping at 2–3 business days after shipment. Carrier delays, weather, holidays, and periods of high demand may affect delivery times."
          }
        },
        {
          "@type": "Question",
          "name": "How do I track my order?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Once your order ships, Infinity Vape Shop will send you a confirmation email with a tracking link that you can use to follow delivery updates."
          }
        },
        {
          "@type": "Question",
          "name": "Why hasn't my tracking information updated?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tracking updates can sometimes take time to appear after a carrier receives the package. If your tracking has shown no movement for 3 or more business days, contact us so we can review the shipment."
          }
        },
        {
          "@type": "Question",
          "name": "Why is an adult signature required?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Vaping and nicotine products are age-restricted. An eligible adult who is 21 or older may be required to sign for the package and provide identification when the order is delivered."
          }
        },
        {
          "@type": "Question",
          "name": "What happens if no one is available to sign for my package?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The carrier may attempt another delivery or hold the package at an eligible pickup location. Carrier procedures can vary, so check your tracking information for the latest instructions."
          }
        },
        {
          "@type": "Question",
          "name": "Does Infinity Vape Shop ship everywhere in the United States?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. Some states, cities, and local areas restrict or prohibit the delivery of vaping or nicotine products. We cannot fulfill orders where shipping these products is not permitted."
          }
        },
        {
          "@type": "Question",
          "name": "Does Infinity Vape Shop ship internationally?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No. Infinity Vape Shop currently ships only to eligible locations within the United States. International shipping is not available at this time."
          }
        },
        {
          "@type": "Question",
          "name": "What should I do if my tracking says delivered but I can't find my package?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "First, check around your delivery location and confirm that the shipping address and tracking details are correct. If you still cannot locate the package, contact us within 7 days of the reported delivery."
          }
        },
        {
          "@type": "Question",
          "name": "Can I return an unopened vape product?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Eligible unopened and unused products may be returned within 14 days of delivery for a refund or store credit. Original shipping charges are generally not refundable."
          }
        },
        {
          "@type": "Question",
          "name": "Can I return an opened or used vape?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Generally, no. For hygiene and safety reasons, certain opened or used products are final sale, including opened or used e-liquids, used vape devices, opened pod kits, coils, replacement pods, and nicotine pouches."
          }
        },
        {
          "@type": "Question",
          "name": "What should I do if my vape arrives damaged or defective?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Contact us within 7 days of delivery with your order number, a description of the issue, and a clear photo or short video. Once verified, an eligible replacement or refund may be arranged."
          }
        },
        {
          "@type": "Question",
          "name": "What if I received the wrong product?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "If Infinity Vape Shop sends you the wrong item, contact us within 7 days of delivery with your order number and details about the item you received so we can review the issue."
          }
        },
        {
          "@type": "Question",
          "name": "How long does a refund take?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Once an eligible return has been received and inspected, refunds are generally processed within 5–7 business days. Your bank or card provider may require additional time before the refund appears."
          }
        },
        {
          "@type": "Question",
          "name": "How do I check the nicotine strength or product details before ordering?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Check the individual product page before purchasing. Product pages should show important details such as nicotine strength, device type, estimated puff count, flavor, and other available specifications."
          }
        }
      ]
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

<style>
/* Consistent Collection Page FAQ Styling Overrides */
.faq-page-wrapper {
  max-width: 960px;
  margin: 0 auto;
  padding: 30px 16px 80px;
}
.faq-page-header {
  text-align: center;
  margin-bottom: 40px;
}
.faq-page-header h1 {
  font-size: 2.2rem;
  font-weight: 800;
  color: #0b2545;
  margin: 0 0 12px;
  letter-spacing: -0.02em;
}
.faq-page-header p {
  font-size: 1rem;
  color: #64748b;
  max-width: 680px;
  margin: 0 auto 10px;
  line-height: 1.6;
}
.faq-group-block {
  margin-bottom: 44px;
}
.faq-group-block .category-faqs-title {
  font-size: 1.45rem;
  font-weight: 800;
  color: #0b2545;
  margin: 0 0 16px;
  letter-spacing: -0.01em;
}
/* Ensure smooth accordion expansion without clipping */
.category-faq-item.open .category-faq-answer-collapse {
  max-height: 1400px !important;
}
.category-faq-answer-inner ul {
  list-style-type: disc !important;
  margin: 8px 0 14px 22px !important;
  padding-left: 6px !important;
  color: #64748b;
}
.category-faq-answer-inner li {
  display: list-item !important;
  list-style-type: disc !important;
  margin-bottom: 6px;
  font-size: 0.9rem;
  line-height: 1.68;
}
.faq-mini-table {
  width: 100%;
  max-width: 480px;
  border-collapse: collapse;
  margin: 12px 0 16px;
  border: 1px solid #edf2f7;
  border-radius: 8px;
  overflow: hidden;
}
.faq-mini-table th {
  background: #0b2545;
  color: #ffffff;
  padding: 10px 16px;
  font-size: 13.5px;
  text-align: left;
  font-weight: 700;
}
.faq-mini-table td {
  padding: 10px 16px;
  border-bottom: 1px solid #edf2f7;
  font-size: 14px;
  color: #475569;
}
.faq-mini-table tbody tr:nth-child(even) {
  background: #f8fafc;
}
.faq-still-card {
  margin-top: 50px;
  padding: 36px 24px;
  background: #f8fafc;
  border: 1px solid #edf2f7;
  border-radius: 12px;
  text-align: center;
}
.faq-still-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: #0b2545;
  margin: 0 0 10px;
}
.faq-still-desc {
  font-size: 14.5px;
  color: #64748b;
  line-height: 1.6;
  max-width: 640px;
  margin: 0 auto 16px;
}
.faq-still-links {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  margin: 16px 0;
}
.faq-still-links a {
  display: inline-block;
  padding: 7px 16px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 20px;
  color: #0b2545;
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
}
.faq-still-links a:hover {
  background: #fa5a1e;
  border-color: #fa5a1e;
  color: #ffffff;
}
.faq-age-notice {
  font-size: 13px;
  color: #94a3b8;
  margin-top: 16px;
  font-weight: 600;
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
        Frequently Asked Questions
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. FAQ ACCORDION CONTENT (Exact Collection Page Design)
     ========================================== -->
<div class="faq-page-wrapper">

  <!-- Page Header -->
  <header class="faq-page-header">
    <h1>Frequently Asked Questions About Infinity Vape Shop</h1>
    <p>
      Have questions about ordering from Infinity Vape Shop? You’ll find quick answers below about age verification, payments, shipping, tracking, returns, refunds, and vape products.
    </p>
    <p style="font-size: 0.925rem;">
      For more detailed information, you can also review our <a href="<?= url('page/shipping-policy') ?>" style="color: #fa5a1e; text-decoration: underline; font-weight: 600;">Shipping Policy</a>, <a href="<?= url('page/refund-policy') ?>" style="color: #fa5a1e; text-decoration: underline; font-weight: 600;">Refund Policy</a>, <a href="<?= url('page/privacy-policy') ?>" style="color: #fa5a1e; text-decoration: underline; font-weight: 600;">Privacy Policy</a>, and <a href="<?= url('page/terms-and-conditions') ?>" style="color: #fa5a1e; text-decoration: underline; font-weight: 600;">Terms &amp; Conditions</a>.
    </p>
  </header>

  <!-- ========================================
       GROUP 1: AGE VERIFICATION
       ======================================== -->
  <section class="category-faqs-section faq-group-block">
    <h2 class="category-faqs-title">Age Verification</h2>
    <div class="category-faqs-list">

      <!-- Q1 -->
      <div class="category-faq-item open">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="true">
            <span class="faq-q-text">1. How old do I need to be to order from Infinity Vape Shop?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>You must be 21 years of age or older to purchase vaping or nicotine products from Infinity Vape Shop.</p>
            <p>We may verify your age before processing an order to comply with applicable federal, state, and local requirements.</p>
          </div>
        </div>
      </div>

      <!-- Q2 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">2. Why does Infinity Vape Shop verify my age?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Vaping and nicotine products are age-restricted in the United States.</p>
            <p>Age verification helps us confirm that customers meet the legal purchasing age before an order is processed or delivered.</p>
          </div>
        </div>
      </div>

      <!-- Q3 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">3. What happens if my age cannot be verified?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>If we cannot confirm that you meet the legal purchasing age, we may ask for additional information or identification.</p>
            <p>Orders that fail age verification may be cancelled and refunded.</p>
            <p>For more details, please review our <a href="<?= url('page/terms-and-conditions') ?>" style="color: #fa5a1e; font-weight: 600; text-decoration: underline;">Terms &amp; Conditions</a>.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ========================================
       GROUP 2: ORDERING & PAYMENTS
       ======================================== -->
  <section class="category-faqs-section faq-group-block">
    <h2 class="category-faqs-title">Ordering &amp; Payments</h2>
    <div class="category-faqs-list">

      <!-- Q4 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">4. How do I place an order?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Choose your products, add them to your cart, and continue to checkout.</p>
            <p>You’ll need to provide your shipping and payment information and complete any required age verification before your order can be processed.</p>
          </div>
        </div>
      </div>

      <!-- Q5 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">5. What payment methods do you accept?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Available payment methods are shown during checkout.</p>
            <p>Payments are processed securely through our payment providers, and your complete credit card number is not stored on Infinity Vape Shop servers.</p>
          </div>
        </div>
      </div>

      <!-- Q6 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">6. Can I change or cancel my order after placing it?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Contact us as soon as possible if you need to change or cancel an order.</p>
            <p>We may be able to help if the order has not yet been processed or shipped. Once fulfillment has started, changes or cancellations may no longer be possible.</p>
          </div>
        </div>
      </div>

      <!-- Q7 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">7. Why was my order cancelled?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>An order may be cancelled because of:</p>
            <ul>
              <li>Failed age verification</li>
              <li>Payment or verification issues</li>
              <li>Suspected fraudulent activity</li>
              <li>Shipping restrictions</li>
              <li>Incorrect order information</li>
              <li>Product availability</li>
            </ul>
            <p>If an order is cancelled and payment has already been collected, any applicable refund will be handled according to our <a href="<?= url('page/refund-policy') ?>" style="color: #fa5a1e; font-weight: 600; text-decoration: underline;">Refund Policy</a>.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ========================================
       GROUP 3: SHIPPING & DELIVERY
       ======================================== -->
  <section class="category-faqs-section faq-group-block">
    <h2 class="category-faqs-title">Shipping &amp; Delivery</h2>
    <div class="category-faqs-list">

      <!-- Q8 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">8. How long does Infinity Vape Shop take to process an order?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Most verified orders are processed within 1–2 business days, Monday through Friday, excluding holidays.</p>
            <p>Processing time is separate from the delivery time.</p>
          </div>
        </div>
      </div>

      <!-- Q9 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">9. How long does shipping take?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Estimated delivery times are:</p>
            <table class="faq-mini-table">
              <thead>
                <tr>
                  <th>Shipping Method</th>
                  <th>Estimated Delivery</th>
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
            <p>Delivery estimates begin after your order ships.</p>
            <p>Carrier delays, weather, holidays, and periods of high demand may occasionally affect delivery times.</p>
          </div>
        </div>
      </div>

      <!-- Q10 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">10. How do I track my order?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Once your order ships, Infinity Vape Shop will send you a confirmation email with a tracking link.</p>
            <p>You can use that link to follow the latest delivery updates from the carrier.</p>
          </div>
        </div>
      </div>

      <!-- Q11 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">11. Why hasn't my tracking information updated?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Tracking updates can sometimes take time to appear after a carrier receives the package.</p>
            <p>If your tracking has shown no movement for 3 or more business days, contact us and we’ll help review the shipment.</p>
          </div>
        </div>
      </div>

      <!-- Q12 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">12. Why is an adult signature required?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Vaping and nicotine products are age-restricted.</p>
            <p>An eligible adult who is 21 or older may be required to sign for the package and provide identification when the order is delivered.</p>
          </div>
        </div>
      </div>

      <!-- Q13 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">13. What happens if no one is available to sign for my package?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>The carrier may attempt another delivery or hold the package at an eligible pickup location.</p>
            <p>Carrier procedures can vary, so check your tracking information for the latest instructions.</p>
          </div>
        </div>
      </div>

      <!-- Q14 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">14. Does Infinity Vape Shop ship everywhere in the United States?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>No.</p>
            <p>Some states, cities, and local areas restrict or prohibit the delivery of vaping or nicotine products. We cannot fulfill orders where shipping these products is not permitted.</p>
            <p>Please review our <a href="<?= url('page/shipping-policy') ?>" style="color: #fa5a1e; font-weight: 600; text-decoration: underline;">Shipping Policy</a> for more information.</p>
          </div>
        </div>
      </div>

      <!-- Q15 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">15. Does Infinity Vape Shop ship internationally?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>No. Infinity Vape Shop currently ships only to eligible locations within the United States.</p>
            <p>International shipping is not available at this time.</p>
          </div>
        </div>
      </div>

      <!-- Q16 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">16. What should I do if my tracking says delivered but I can't find my package?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>First, check around your delivery location and confirm that the shipping address and tracking details are correct.</p>
            <p>If you still cannot locate the package, contact us within 7 days of the reported delivery so we can review the shipment and, when appropriate, work with the carrier.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ========================================
       GROUP 4: RETURNS & REFUNDS
       ======================================== -->
  <section class="category-faqs-section faq-group-block">
    <h2 class="category-faqs-title">Returns &amp; Refunds</h2>
    <div class="category-faqs-list">

      <!-- Q17 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">17. Can I return an unopened vape product?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Eligible unopened and unused products may be returned within 14 days of delivery for a refund or store credit.</p>
            <p>Original shipping charges are generally not refundable.</p>
            <p>Please check our <a href="<?= url('page/refund-policy') ?>" style="color: #fa5a1e; font-weight: 600; text-decoration: underline;">Refund Policy</a> before sending an item back.</p>
          </div>
        </div>
      </div>

      <!-- Q18 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">18. Can I return an opened or used vape?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Generally, no.</p>
            <p>For hygiene and safety reasons, certain opened or used products are final sale, including:</p>
            <ul>
              <li>Opened or used e-liquids</li>
              <li>Used vape devices</li>
              <li>Opened pod kits</li>
              <li>Opened coils</li>
              <li>Opened replacement pods</li>
              <li>Nicotine pouches</li>
            </ul>
            <p>If you believe an opened product is genuinely defective, contact our support team.</p>
          </div>
        </div>
      </div>

      <!-- Q19 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">19. What should I do if my vape arrives damaged or defective?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Contact us within 7 days of delivery and provide:</p>
            <ul>
              <li>Your order number</li>
              <li>A description of the issue</li>
              <li>A clear photo or short video showing the problem</li>
            </ul>
            <p>Once the issue has been reviewed and verified, we may arrange an eligible replacement or refund.</p>
          </div>
        </div>
      </div>

      <!-- Q20 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">20. What if I received the wrong product?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>If Infinity Vape Shop sends you the wrong item, contact us within 7 days of delivery.</p>
            <p>Provide your order number and details about what you received, and we’ll review the issue and arrange the appropriate resolution.</p>
          </div>
        </div>
      </div>

      <!-- Q21 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">21. How long does a refund take?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Once an eligible return has been received and inspected, refunds are generally processed within 5–7 business days.</p>
            <p>Your bank or card provider may need additional time before the refund appears on your account.</p>
            <p>Approved refunds are normally returned to the original payment method.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ========================================
       GROUP 5: VAPE PRODUCT QUESTIONS
       ======================================== -->
  <section class="category-faqs-section faq-group-block">
    <h2 class="category-faqs-title">Vape Product Questions</h2>
    <div class="category-faqs-list">

      <!-- Q22 -->
      <div class="category-faq-item">
        <h3 class="category-faq-heading">
          <button type="button" class="category-faq-question-btn" aria-expanded="false">
            <span class="faq-q-text">22. How do I check the nicotine strength or product details before ordering?</span>
            <span class="faq-q-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </button>
        </h3>
        <div class="category-faq-answer-collapse">
          <div class="category-faq-answer-inner">
            <p>Check the individual product page before purchasing.</p>
            <p>Product pages should show important details such as nicotine strength, device type, estimated puff count, flavor, and other available specifications.</p>
            <p>Manufacturer puff counts are estimates and actual usage can vary depending on puff duration, device settings, frequency of use, and other factors.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ========================================
       STILL HAVE QUESTIONS CARD
       ======================================== -->
  <div class="faq-still-card">
    <h3 class="faq-still-title">Still Have Questions?</h3>
    <p class="faq-still-desc">
      If you couldn’t find the answer you were looking for, contact Infinity Vape Shop support team:
      <br>
      <strong>Email:</strong> <a href="mailto:support@infinityvapeshop.com" style="color: #fa5a1e; font-weight: 700; text-decoration: underline;">support@infinityvapeshop.com</a>
    </p>

    <p class="faq-still-desc" style="margin-bottom: 8px;">
      You can also find more detailed information in our:
    </p>

    <div class="faq-still-links">
      <a href="<?= url('page/shipping-policy') ?>">Shipping Policy</a>
      <a href="<?= url('page/refund-policy') ?>">Refund Policy</a>
      <a href="<?= url('page/privacy-policy') ?>">Privacy Policy</a>
      <a href="<?= url('page/terms-and-conditions') ?>">Terms &amp; Conditions</a>
      <a href="<?= url('page/fda-disclaimer') ?>">FDA Disclaimer</a>
    </div>

    <div class="faq-age-notice">
      Age Restriction: You must be 21 years of age or older to purchase vaping or nicotine products from Infinity Vape Shop.
    </div>
  </div>

</div>

<!-- Interactive Accordion JavaScript -->
<script>
  (function() {
    function initFaqAccordion() {
      var questionButtons = document.querySelectorAll('.category-faq-question-btn');
      questionButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          var item = this.closest('.category-faq-item');
          if (!item) return;
          var isOpen = item.classList.contains('open');
          item.classList.toggle('open');
          this.setAttribute('aria-expanded', !isOpen);
        });
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initFaqAccordion);
    } else {
      initFaqAccordion();
    }
  })();
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
