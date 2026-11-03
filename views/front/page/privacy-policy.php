<?php
$page_title = 'Privacy Policy | Infinity Vape Shop';
$meta_description = 'Read Infinity Vape Shop Privacy Policy to learn how we collect, use, protect and share personal information, including cookies, payments and privacy rights.';
$active_page = 'privacy';

$page_schema = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "https://infinityvapeshop.com/page/privacy-policy#webpage",
      "url": "https://infinityvapeshop.com/page/privacy-policy",
      "name": "Privacy Policy | Infinity Vape Shop",
      "description": "Read Infinity Vape Shop Privacy Policy to learn how we collect, use, protect and share personal information, including cookies, payments and privacy rights.",
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

<style>
.legal-table-responsive {
  width: 100%;
  overflow-x: auto;
  margin: 22px 0 32px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}
.legal-custom-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 15px;
  line-height: 1.6;
}
.legal-custom-table th {
  background-color: #082142;
  color: #ffffff;
  padding: 14px 20px;
  font-weight: 700;
  font-size: 14px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border: 1px solid #082142;
}
.legal-custom-table td {
  padding: 14px 20px;
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #f1f5f9;
  color: #334155;
  vertical-align: top;
}
.legal-custom-table td:last-child {
  border-right: none;
}
.legal-custom-table tbody tr:nth-child(even) {
  background-color: #f8fafc;
}
.legal-custom-table tbody tr:hover {
  background-color: #f1f5f9;
}
.legal-sub-heading {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  margin: 18px 0 8px 0;
}
.legal-page-content p {
  color: #334155;
  line-height: 1.75;
  margin-bottom: 16px;
  font-size: 15px;
}
.legal-page-content ul {
  margin: 8px 0 18px 24px;
  padding: 0;
  color: #334155;
  line-height: 1.75;
  font-size: 15px;
}
.legal-page-content li {
  margin-bottom: 8px;
}
.legal-page-content a {
  color: #fa5a1e;
  text-decoration: underline;
  font-weight: 500;
}
.legal-page-content a:hover {
  color: #e0450d;
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
        Privacy Policy
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. PRIVACY POLICY CONTENT
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container">
      
      <header class="legal-page-header">
        <h1 class="legal-page-title">Privacy Policy</h1>
      </header>

      <div class="legal-page-content">
        <p class="legal-page-intro">
          Your trust is as important as your next order at Infinity Vape Shop. This Privacy Policy is intended to provide you with information on what information we are collecting, why we are collecting it, and choices you have (in plain English, not legal mumbo-jumbo).
        </p>
        <p>
          Our website, Infinityvapeshop.com (“we”, “us”, “our” or “the Site”) practices as outlined below. If you don't agree, please don't use the Site.
        </p>

        <!-- Section 1 -->
        <h2 class="legal-section-title">1. This Policy Applies To</h2>
        <p>
          This Site is for adults, legal purchasing age (21+, or minimum age in your state). Please leave the Site now if you are minored, as we do not knowingly collect data from minors and that is a serious matter (read more in Section 8).
        </p>

        <!-- Section 2 -->
        <h2 class="legal-section-title">2. Information We Collect</h2>
        <p>
          We gather data from three sources: you provide the data, we get data from your device, and our partners share data with us.
        </p>

        <h3 class="legal-sub-heading">Data Entered by the User</h3>
        <ul>
          <li>Name, email address, phone and shipping/billing address.</li>
          <li>Payment information (processed securely — we will cover in Section 4).</li>
          <li>Date of birth will only be used for the purpose of determining a person's age.</li>
          <li>Any information you submit in contact forms, live chat or customer support enquiries.</li>
          <li>Marketing preferences (if you want e-mail and/or SMS).</li>
        </ul>

        <h3 class="legal-sub-heading">Information Collected Automatically</h3>
        <ul>
          <li>IP address, browser type and device information.</li>
          <li>Click behavior, pages viewed and time spent on the Site.</li>
          <li>Linking to your web pages or search queries that led to this page.</li>
          <li>Cookies and other tracking technologies (see Section 5).</li>
        </ul>

        <h3 class="legal-sub-heading">Information from Third Parties</h3>
        <ul>
          <li>ID Verification Providers.</li>
          <li>The payment processor(s) who confirmed the transaction.</li>
          <li>Marketing and analytics partners (such as ad platforms, email tools).</li>
        </ul>

        <!-- Section 3 -->
        <h2 class="legal-section-title">3. Why We Collect It</h2>
        <p>
          We don't collect data for the sake of collecting it. Below is an explanation of the purpose of each piece:
        </p>

        <div class="legal-table-responsive">
          <table class="legal-custom-table">
            <thead>
              <tr>
                <th style="width: 38%;">Purpose</th>
                <th>Examples</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Order processing</strong></td>
                <td>Payment processing, order updates.</td>
              </tr>
              <tr>
                <td><strong>Asking for your age</strong></td>
                <td>Verifying that you are the legal age to purchase before checkout.</td>
              </tr>
              <tr>
                <td><strong>Analysing which products, pages, or categories work well</strong></td>
                <td>Knowing what products, pages, or categories are successful and which are not.</td>
              </tr>
              <tr>
                <td><strong>Customer support</strong></td>
                <td>Answering questions, solving problems, handling returns.</td>
              </tr>
              <tr>
                <td><strong>Marketing (opt-in only)</strong></td>
                <td>Providing deals, restock notifications, or new product announcements.</td>
              </tr>
              <tr>
                <td><strong>Food Safety</strong></td>
                <td>Ensuring that food products meet state and federal safety standards.</td>
              </tr>
              <tr>
                <td><strong>Food Quality</strong></td>
                <td>Ensuring that food products comply with state and federal quality standards.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Section 4 -->
        <h2 class="legal-section-title">4. Payment Security</h2>
        <p>
          Your complete credit card number is not kept on our servers. Your card information is always encrypted and passed through payment processors that are PCI-DSS compliant, not through us; which means you are not risking your card information being compromised by the systems in which we use.
        </p>

        <!-- Section 5 -->
        <h2 class="legal-section-title">5. Cookies &amp; Tracking Technologies</h2>
        <p>
          Cookies are small files that aid the Site to remember you, maintain your shopping cart through visits to the Site and to know how people use the Site.
        </p>
        <p>We use:</p>
        <ul>
          <li><strong>Technical cookies:</strong> Necessary for the website to function and provide users with a service compliant with their requirements, such as for facilitating navigation and access to the Internet.</li>
          <li><strong>Analytics cookies:</strong> Let us know which pages and products are being looked at.</li>
          <li><strong>Marketing cookies:</strong> These are used to retarget ads and to determine the effectiveness of their marketing campaigns (where legally allowed for this product type).</li>
        </ul>
        <p>
          It is possible to control or disable cookies at any time in the settings of your browser. Please be aware that if you block important cookies, your checkout will fail.
        </p>

        <!-- Section 6 -->
        <h2 class="legal-section-title">6. Determine Who You Share Information With</h2>
        <p>
          Define who you give information to. We do not share your personal information with any other third parties. Some of the data we share with them is limited, such as:
        </p>
        <ul>
          <li><strong>Shipping Carriers:</strong> Companies that offer shipping services — to send your order.</li>
          <li><strong>Payment Processors:</strong> To complete transactions securely.</li>
          <li><strong>Age Verification Services:</strong> To prove eligibility to purchase.</li>
          <li><strong>Marketing/Analytics Platforms:</strong> Only with data that helps us to understand and enhance the shopping experience.</li>
          <li><strong>Law Enforcement or Regulators:</strong> Only when required by law (e.g., subpoena, regulatory investigation).</li>
        </ul>
        <p>
          Your information will not be made available to any third party without your permission or for the third party's own marketing purposes.
        </p>

        <!-- Section 7 -->
        <h2 class="legal-section-title">7. Your Privacy Rights (CCPA/CPRA &amp; State Privacy Laws)</h2>
        <p>
          As a consumer in California, Colorado, Virginia, Connecticut or other state with an in effect consumer privacy law, you have the general right to:
        </p>
        <ul>
          <li>Have an understanding of personal data that have been collected from us.</li>
          <li>Obtain a copy of that information.</li>
          <li>Eliminate personal data that we have, except for specific cases.</li>
          <li>Identify and fix incorrect personal data.</li>
          <li>Avoid the sale and sharing of personal data (Not selling data, but you can opt out of targeted advertising).</li>
          <li><strong>Non-discrimination:</strong> We will not refuse to provide you a service, charge you more or less than another customer or offer you a lesser quality service based on your exercise of these rights.</li>
        </ul>
        <p>
          If you wish to exercise any of these rights please contact us on the contact details provided in Section 11. Your identity will be checked prior to fulfilling your request in accordance with the law.
        </p>

        <!-- Section 8 -->
        <h2 class="legal-section-title">8. Age Verification &amp; Data from Minors</h2>
        <p>
          We are not aware that we collect personal information from anyone who is too young to be legally able to purchase vaping and nicotine products. Age verification will be necessary when checking in and out of site.
        </p>
        <p>
          Should it be determined that we have collected information from a minor by mistake, it will be deleted as soon as it is discovered.
        </p>

        <!-- Section 9 -->
        <h2 class="legal-section-title">9. Data Retention</h2>
        <p>
          We retain information only for as long as is required, which is to process orders, comply with applicable laws and taxes, settle disputes and enforce our agreement.
        </p>
        <p>
          Specific recordkeeping requirements may apply to regulated products, such as ours, and could require that some transaction records be kept for longer periods.
        </p>

        <!-- Section 10 -->
        <h2 class="legal-section-title">10. Data Security</h2>
        <p>
          Your information is protected by industry-standard safeguards, such as restricting access to your information, using secure internal access, and encryption.
        </p>
        <p>
          However, there are no guarantees of absolute security of any internet-based method of transmission and we cannot guarantee security.
        </p>

        <!-- Section 11 -->
        <h2 class="legal-section-title">11. Contact Us</h2>
        <p>
          Questions, requests, more information on how your data is used? Reach out:
        </p>
        <p>
          <strong>Email:</strong> <a href="mailto:support@infinityvapeshop.com">support@infinityvapeshop.com</a><br>
          Please send all correspondence to: <a href="mailto:support@infinityvapeshop.com">support@infinityvapeshop.com</a>
        </p>

        <!-- Section 12 -->
        <h2 class="legal-section-title">12. This Policy Comes Into Effect on the Date of This Adoption</h2>
        <p>
          This Privacy Policy may be amended from time to time, as we change our practices, introduce new products or as laws change.
        </p>

      </div>

    </div>
  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
