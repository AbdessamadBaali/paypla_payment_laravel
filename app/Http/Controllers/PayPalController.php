<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Adjust the namespace as per your application structure
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Exception;

class PayPalController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $mode;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->mode = config('services.paypal.mode');
    }

    public function createOrder()
    {
        $environment = new SandboxEnvironment($this->clientId, $this->clientSecret);
        $client = new PayPalHttpClient($environment);

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "1.00"
                    ],
                    "description" => "Your product description",
                    "custom_id" => "1234566890", // Custom ID for your order
                    // Add more fields as needed
                ]
            ],
            "application_context" => [
                "cancel_url" => route('payment.cancel'),
                "return_url" => route('capture-order')
                // Additional context parameters
            ]
        ];

        try {
            $response = $client->execute($request);
            // Process the response and redirect the user to PayPal for payment approval

            return redirect()->away($response->result->links[1]->href);
        } catch (Exception $e) {
            // Handle error responses
            echo 'error';
        }
    }

    public function captureOrder(Request $request)
    {
        $orderId = $request->input('token'); // Adjust this line based on the actual parameter name from PayPal
        $environment = new SandboxEnvironment($this->clientId, $this->clientSecret);
        $client = new PayPalHttpClient($environment);
        
        $request = new OrdersCaptureRequest($orderId);

        try {
            $response = $client->execute($request);
            // Process the captured payment response
            $this->createNewUser();// Create a new user after a successful payment
            return redirect()->route('payment.success');
        } catch (Exception $e) {
            // Handle error responses
            return redirect()->route('payment.cancel');
        }
    }

    public function paymentSuccess()
    {
        // Handle successful payment
        return view('success');
    }

    public function paymentCancel()
    {
        // Handle canceled payment
        return view('cancel');
    }

    private function createNewUser()
    {
        // Logic to create a new user after successful payment
        // For example, you can use Laravel's User model to create a new user
        User::create([
            'name' => 'New User', // Customize as needed
            'email' => 'newyjhuser'.time().'@gmail.com', // Customize as needed
            // 'password' => bcrypt('password1'), // Customize as needed
            'password' => 'password'.time().'1fjhf', // Customize as needed

        ]);
    }
}
