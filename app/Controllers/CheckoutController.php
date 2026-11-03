<?php
namespace App\Controllers;

use App\Core\Controller;

class CheckoutController extends Controller
{
    public function index()
    {
        $this->render('front.checkout.index', [
            'page_title' => 'Secure Checkout | Infinity Vape Shop',
            'page_js' => 'checkout'
        ]);
    }
}
