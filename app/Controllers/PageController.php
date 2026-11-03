<?php
namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    /**
     * Shipping Policy Page
     */
    public function shippingPolicy()
    {
        $this->render('front.page.shipping-policy', [
            'page_title' => 'Shipping Policy | Infinity Vape Shop',
            'meta_description' => 'Read Infinity Vape Shop Shipping Policy for processing times, delivery estimates, age verification, tracking, shipping costs, and U.S. restrictions.',
            'active_page' => 'shipping-policy'
        ]);
    }

    /**
     * Refund & Return Policy Page
     */
    public function refundPolicy()
    {
        $this->render('front.page.refund-policy', [
            'page_title' => 'Refund Policy | Infinity Vape Shop',
            'meta_description' => 'Read Infinity Vape Shop Refund Policy for details on 14-day returns, damaged or wrong items, eligible products, refunds, and return steps.',
            'active_page' => 'refund-policy'
        ]);
    }

    /**
     * FDA Disclaimer & Nicotine Warning Page
     */
    public function fdaDisclaimer()
    {
        $this->render('front.page.fda-disclaimer', [
            'page_title' => 'FDA Disclaimer | Infinity Vape Shop',
            'meta_description' => 'Read Infinity Vape Shop FDA Disclaimer for information on FDA evaluation, product claims, medical guidance, age requirements, and PACT Act compliance.',
            'active_page' => 'fda-disclaimer'
        ]);
    }

    /**
     * Terms and Conditions Page
     */
    public function terms()
    {
        $this->render('front.page.terms-and-conditions', [
            'page_title' => 'Terms & Conditions | Infinity Vape Shop',
            'meta_description' => 'Read Infinity Vape Shop Terms & Conditions covering age requirements, orders, payments, shipping, returns, product information, and website use.',
            'active_page' => 'terms'
        ]);
    }

    /**
     * Privacy Policy Page
     */
    public function privacy()
    {
        $this->render('front.page.privacy-policy', [
            'page_title' => 'Privacy Policy | Infinity Vape Shop',
            'meta_description' => 'Read Infinity Vape Shop Privacy Policy to learn how we collect, use, protect and share personal information, including cookies, payments and privacy rights.',
            'active_page' => 'privacy'
        ]);
    }

    /**
     * Contact Us Page (with contact_messages database integration)
     */
    public function contact()
    {
        $success = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $subject = trim($_POST['subject'] ?? 'General Inquiry');
            if (empty($subject)) {
                $subject = 'General Inquiry';
            }
            $message = trim($_POST['message'] ?? '');

            if (empty($name) || empty($email) || empty($message)) {
                $error = 'Please fill in all required fields.';
            } else {
                $db = \App\Core\Database::getConnection();
                if ($db) {
                    try {
                        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute([$name, $email, $phone, $subject, $message]);
                        $success = 'Thank you for reaching out! Your message has been received and our team will get back to you shortly.';
                    } catch (\Exception $e) {
                        $error = 'Database error: Unable to submit your message. Please try again.';
                    }
                }
            }
        }

        $this->render('front.page.contact', [
            'page_title' => 'Contact Us | Infinity Vape Shop',
            'active_page' => 'contact',
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * FAQs Page
     */
    public function faqs()
    {
        $this->render('front.page.faqs', [
            'page_title' => 'FAQs | Infinity Vape Shop',
            'meta_description' => 'Find answers to common questions about Infinity Vape Shop, including age verification, orders, shipping, tracking, returns, refunds, and vape products.',
            'active_page' => 'faqs'
        ]);
    }

    /**
     * About Us Page
     */
    public function about()
    {
        $this->render('front.page.about', [
            'page_title' => 'About Us | Authentic Vape Superstore | Infinity Vape Shop',
            'active_page' => 'about'
        ]);
    }

    /**
     * Track Order Page (with orders table database lookup)
     */
    public function trackOrder()
    {
        $tracking_result = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderNumber = trim($_POST['order_number'] ?? '');
            $customerEmail = trim($_POST['customer_email'] ?? '');

            if (!empty($orderNumber) && !empty($customerEmail)) {
                $db = \App\Core\Database::getConnection();
                if ($db) {
                    try {
                        $stmt = $db->prepare('SELECT * FROM orders WHERE (order_number = ? OR id = ?) AND LOWER(customer_email) = LOWER(?) LIMIT 1');
                        $stmt->execute([$orderNumber, $orderNumber, $customerEmail]);
                        $tracking_result = $stmt->fetch(\PDO::FETCH_ASSOC);
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        $this->render('front.page.track-order', [
            'page_title' => 'Track Your Order | Infinity Vape Shop',
            'active_page' => 'track-order',
            'tracking_result' => $tracking_result
        ]);
    }
}
