<?php

namespace App\Controllers\Web;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class CustomerController extends BaseWebController
{
    protected ProductModel $productModel;
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;

    public function __construct()
    {
        $this->productModel   = new ProductModel();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    // =============================================
    //  DASHBOARD
    // =============================================
    public function dashboard()
    {
        $user   = $this->getUser();
        $orders = $this->orderModel->getByUser($user['id']);

        $data = [
            'title'         => 'Dashboard Customer - ESPERPAT',
            'user'          => $user,
            'orders'        => $orders,
            'totalOrders'   => count($orders),
            'pendingOrders' => count(array_filter($orders, fn($o) => $o['status'] === 'pending')),
            'cartCount'     => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('customer/dashboard', $data)
             . view('layouts/footer', $data);
    }

    // =============================================
    //  CART  (session-based, like Shopee)
    // =============================================

    /**
     * Get cart from session
     */
    private function getCart(): array
    {
        return $this->session->get('cart') ?? [];
    }

    /**
     * Save cart to session
     */
    private function saveCart(array $cart): void
    {
        $this->session->set('cart', $cart);
    }



    /**
     * Add product to cart (POST)
     */
    public function addToCart()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('qty') ?? 1));

        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        if ($product['stok'] <= 0) {
            return redirect()->back()->with('error', 'Stok produk habis.');
        }

        $cart = $this->getCart();

        // Check if product already in cart → increase qty
        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['qty'] + $qty;
            if ($newQty > $product['stok']) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product['stok'] . ', di keranjang: ' . $cart[$productId]['qty']);
            }
            $cart[$productId]['qty'] = $newQty;
        } else {
            if ($qty > $product['stok']) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product['stok']);
            }
            $cart[$productId] = [
                'product_id' => $productId,
                'name'       => $product['name'],
                'slug'       => $product['slug'],
                'image'      => $product['image'] ?? '',
                'harga'      => $product['harga_jual'],
                'stok'       => $product['stok'],
                'qty'        => $qty,
            ];
        }

        $this->saveCart($cart);

        $totalItems = $this->getCartCount();
        return redirect()->back()->with('success', '✓ ' . $product['name'] . ' ditambahkan ke keranjang! (' . $totalItems . ' item di keranjang)');
    }

    /**
     * View cart page (GET)
     */
    public function cart()
    {
        $cart = $this->getCart();

        // Refresh stock info from DB 
        foreach ($cart as $id => &$item) {
            $product = $this->productModel->find($id);
            if ($product) {
                $item['stok']  = $product['stok'];
                $item['name']  = $product['name'];
                $item['harga'] = $product['harga_jual'];
                $item['image'] = $product['image'] ?? '';
            } else {
                unset($cart[$id]); // product deleted
            }
        }
        $this->saveCart($cart);

        // Calculate totals
        $totalPrice = 0;
        $totalItems = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['harga'] * $item['qty'];
            $totalItems += $item['qty'];
        }

        $data = [
            'title'      => 'Keranjang Belanja - ESPERPAT',
            'user'       => $this->getUser(),
            'cart'       => $cart,
            'totalPrice' => $totalPrice,
            'totalItems' => $totalItems,
            'cartCount'  => $totalItems,
        ];

        return view('layouts/header', $data)
             . view('customer/cart', $data)
             . view('layouts/footer', $data);
    }

    /**
     * Update item qty in cart (POST)
     */
    public function updateCart()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) $this->request->getPost('qty'));

        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $product = $this->productModel->find($productId);
            if ($product && $qty <= $product['stok']) {
                $cart[$productId]['qty'] = $qty;
            } else {
                return redirect()->to('/customer/cart')->with('error', 'Stok tidak mencukupi.');
            }
        }

        $this->saveCart($cart);
        return redirect()->to('/customer/cart')->with('success', 'Keranjang diupdate.');
    }

    /**
     * Remove item from cart (POST)
     */
    public function removeFromCart()
    {
        $productId = (int) $this->request->getPost('product_id');
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->saveCart($cart);
        return redirect()->to('/customer/cart')->with('success', 'Produk dihapus dari keranjang.');
    }

    // =============================================
    //  CHECKOUT (multi-item from cart)
    // =============================================

    /**
     * Checkout page (GET) - show summary of cart + address/payment form
     */
    public function checkout()
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->to('/customer/cart')->with('error', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
        }

        // Refresh and validate stock
        $totalPrice = 0;
        foreach ($cart as $id => &$item) {
            $product = $this->productModel->find($id);
            if (!$product || $product['stok'] < $item['qty']) {
                return redirect()->to('/customer/cart')->with('error', 'Stok ' . ($item['name'] ?? 'produk') . ' tidak mencukupi. Silakan perbarui keranjang.');
            }
            $item['harga'] = $product['harga_jual'];
            $item['stok']  = $product['stok'];
            $totalPrice += $item['harga'] * $item['qty'];
        }
        $this->saveCart($cart);

        $data = [
            'title'      => 'Checkout - ESPERPAT',
            'user'       => $this->getUser(),
            'cart'       => $cart,
            'totalPrice' => $totalPrice,
            'totalItems' => array_sum(array_column($cart, 'qty')),
            'cartCount'  => array_sum(array_column($cart, 'qty')),
        ];

        return view('layouts/header', $data)
             . view('customer/checkout', $data)
             . view('layouts/footer', $data);
    }

    /**
     * Process checkout (POST) - create order + order items, reduce stock, clear cart
     */
    public function processCheckout()
    {
        $user = $this->getUser();
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->to('/customer/cart')->with('error', 'Keranjang kosong.');
        }

        $alamat     = $this->request->getPost('alamat');
        $pembayaran = $this->request->getPost('metode_pembayaran') ?? 'transfer';
        $catatan    = $this->request->getPost('catatan') ?? '';

        if (empty($alamat)) {
            return redirect()->back()->with('error', 'Alamat pengiriman harus diisi.')->withInput();
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Validate stock & calculate total
        $total = 0;
        $orderItems = [];

        foreach ($cart as $id => $item) {
            $product = $this->productModel->find($id);
            if (!$product || $product['stok'] < $item['qty']) {
                $db->transRollback();
                return redirect()->to('/customer/cart')->with('error', 'Stok ' . $item['name'] . ' tidak mencukupi. Silakan perbarui keranjang.');
            }

            $subtotal = $product['harga_jual'] * $item['qty'];
            $total += $subtotal;

            $orderItems[] = [
                'product_id' => $id,
                'qty'        => $item['qty'],
                'harga'      => $product['harga_jual'],
                'subtotal'   => $subtotal,
            ];

            // Reduce stock
            $this->productModel->update($id, [
                'stok' => $product['stok'] - $item['qty'],
            ]);
        }

        // Create order
        $invoiceNumber = $this->orderModel->generateInvoiceNumber();
        $this->orderModel->insert([
            'invoice_number'    => $invoiceNumber,
            'user_id'           => $user['id'],
            'total'             => $total,
            'alamat'            => $alamat,
            'status'            => 'pending',
            'metode_pembayaran' => $pembayaran,
            'catatan'           => $catatan,
        ]);
        $orderId = $this->orderModel->getInsertID();

        // Create order items
        foreach ($orderItems as &$oi) {
            $oi['order_id'] = $orderId;
            $this->orderItemModel->insert($oi);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pesanan. Silakan coba lagi.');
        }

        // Clear cart after successful order
        $this->saveCart([]);

        return redirect()->to('/customer/orders/' . $orderId)->with('success', 'Pesanan berhasil dibuat! Invoice: ' . $invoiceNumber);
    }

    /**
     * Buy now (skip cart, like "Beli Langsung")
     * GET: show confirmation form, POST: create order directly
     */
    public function beli($productId)
    {
        $product = $this->productModel->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->find($productId);

        if (!$product || $product['stok'] <= 0) {
            return redirect()->to('/produk')->with('error', 'Produk tidak ditemukan atau stok habis.');
        }

        if ($this->request->getMethod() === 'get') {
            $data = [
                'title'     => 'Beli Sekarang - ESPERPAT',
                'user'      => $this->getUser(),
                'product'   => $product,
                'cartCount' => $this->getCartCount(),
            ];

            return view('layouts/header', $data)
                 . view('customer/beli', $data)
                 . view('layouts/footer', $data);
        }

        $qty = max(1, (int) ($this->request->getPost('qty') ?? 1));
        if ($qty > $product['stok']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.')->withInput();
        }

        $alamat     = $this->request->getPost('alamat');
        $pembayaran = $this->request->getPost('metode_pembayaran') ?? 'transfer';
        $catatan    = $this->request->getPost('catatan') ?? '';

        if (empty($alamat)) {
            return redirect()->back()->with('error', 'Alamat pengiriman harus diisi.')->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Recheck stock in transaction
        $freshProduct = $this->productModel->find($productId);
        if (!$freshProduct || $freshProduct['stok'] < $qty) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Stok tidak mencukupi.')->withInput();
        }

        $subtotal = $freshProduct['harga_jual'] * $qty;

        // Reduce stock
        $this->productModel->update($productId, [
            'stok' => $freshProduct['stok'] - $qty,
        ]);

        // Create order
        $invoiceNumber = $this->orderModel->generateInvoiceNumber();
        $this->orderModel->insert([
            'invoice_number'    => $invoiceNumber,
            'user_id'           => $this->getUser()['id'],
            'total'             => $subtotal,
            'alamat'            => $alamat,
            'status'            => 'pending',
            'metode_pembayaran' => $pembayaran,
            'catatan'           => $catatan,
        ]);
        $orderId = $this->orderModel->getInsertID();

        // Create order item
        $this->orderItemModel->insert([
            'order_id'   => $orderId,
            'product_id' => $productId,
            'qty'        => $qty,
            'harga'      => $freshProduct['harga_jual'],
            'subtotal'   => $subtotal,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pesanan. Silakan coba lagi.')->withInput();
        }

        return redirect()->to('/customer/orders/' . $orderId)->with('success', 'Pesanan berhasil dibuat! Invoice: ' . $invoiceNumber);
    }

    // =============================================
    //  ORDERS
    // =============================================
    public function orders()
    {
        $user   = $this->getUser();
        $orders = $this->orderModel->getByUser($user['id']);

        $data = [
            'title'     => 'Pesanan Saya - ESPERPAT',
            'user'      => $user,
            'orders'    => $orders,
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('customer/orders', $data)
             . view('layouts/footer', $data);
    }

    public function orderDetail($id)
    {
        $user  = $this->getUser();
        $order = $this->orderModel->select('orders.*, users.name as customer_name')
                    ->join('users', 'users.id = orders.user_id', 'left')
                    ->where('orders.user_id', $user['id'])
                    ->find($id);

        if (!$order) {
            return redirect()->to('/customer/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $order['items'] = $this->orderItemModel->getByOrder($id);

        $data = [
            'title'     => 'Pesanan #' . $order['invoice_number'] . ' - ESPERPAT',
            'user'      => $user,
            'order'     => $order,
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('customer/order_detail', $data)
             . view('layouts/footer', $data);
    }

    // =============================================
    //  PROFILE
    // =============================================
    public function profile()
    {
        $data = [
            'title'     => 'Profil Saya - ESPERPAT',
            'user'      => $this->getUser(),
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('customer/profile', $data)
             . view('layouts/footer', $data);
    }

    public function updateProfile()
    {
        $user = $this->getUser();
        $userModel = new \App\Models\UserModel();

        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
        ];

        $userModel->update($user['id'], $data);
        $updatedUser = $userModel->find($user['id']);
        $this->session->set('user', $updatedUser);

        return redirect()->to('/customer/profile')->with('success', 'Profil berhasil diupdate.');
    }

    public function invoice($id)
    {
        return redirect()->to('/customer/orders/' . $id);
    }
}
