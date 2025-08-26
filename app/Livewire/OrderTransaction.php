<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Debt;
use Illuminate\Support\Facades\Auth;

class OrderTransaction extends Component
{
    use WithPagination;

    protected string $layout = 'components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public $categories = [];
    public $cart = [];
    public $search = '';
    public $total = 0;
    public $selectedCategory = null;
    public $paidAmount = null;
    public $changeAmount = 0;
    public $changeResult = 0;
    public $customerSearch = '';
    public $selectedCustomer = null;
    public $selectedCustomerId = null;
    public $showSuccessPopup = false;
    public $isDebt = false;
    public $dueDate;
    public $initialPayment;
    public bool $showDebtModalOnce = false;
    public $successIsDebt = false;
    public $successPaidAmount = 0;
    public $successTotal = 0;
    public int $resetCounter = 0;
    public $transaction_id;

    public function mount()
    {
        $this->categories = Category::all();
        $this->recalculateTotal();
    }

    public function setCustomPage($page)
    {
        $this->setPage($page);
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->search = '';
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (! $product || $product->stock <= 0) return;

        $price = (int) round($product->sell_price);

        foreach ($this->cart as &$item) {
            if ($item['id'] === $product->id) {
                if ($item['quantity'] + 1 > $product->stock) {
                    session()->flash('error', 'Stok tidak mencukupi.');
                    return;
                }
                $item['quantity']++;
                $item['subtotal'] = $item['quantity'] * $item['price'];
                $this->recalculateTotal();
                return;
            }
        }

        $this->cart[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'quantity' => 1,
            'subtotal' => $price,
            'image' => $product->image,
            'stock' => $product->stock,
        ];

        $this->recalculateTotal();
    }

    public function getFilteredCustomersProperty()
    {
        return Customer::query()
            ->where('name', 'like', '%' . $this->customerSearch . '%')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->selectedCustomerId = $this->selectedCustomer?->id;
        $this->customerSearch = '';
    }

    public function resetCustomer()
    {
        $this->selectedCustomer = null;
        $this->selectedCustomerId = null;
        $this->customerSearch = '';
    }

    public function updatedCustomerSearch()
    {
        $this->selectedCustomer = null;
    }

    public function updatedCart()
    {
        foreach ($this->cart as $key => $item) {
            $product = Product::find($item['id']);
            if (!$product) continue;

            if ($item['quantity'] > $product->stock) {
                $this->cart[$key]['quantity'] = $product->stock;
                session()->flash('error', 'Quantity melebihi stok produk.');
            }

            if ($item['quantity'] < 1) {
                $this->cart[$key]['quantity'] = 1;
            }

            $this->cart[$key]['price'] = (int) round($item['price']);
            $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $this->cart[$key]['price'];
        }
        $this->recalculateTotal();
    }

    public function incrementQty(int $index)
    {
        $product = Product::find($this->cart[$index]['id']);
        if ($product && $this->cart[$index]['quantity'] < $product->stock) {
            $this->cart[$index]['quantity']++;
            $this->updatedCart();
        } else {
            session()->flash('error', 'Stok tidak mencukupi.');
        }
    }

    public function decrementQty(int $index)
    {
        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity']--;
            $this->updatedCart();
        }
    }

    public function removeFromCart(int $productId): void
    {
        $this->cart = array_filter($this->cart, fn ($item) => $item['id'] !== $productId);
        $this->recalculateTotal();

        if (empty($this->cart)) {
            $this->paidAmount = null;
            $this->changeAmount = 0;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedCategory = null;
    }

    protected function recalculateTotal(): void
    {
        $this->total = collect($this->cart)->sum('subtotal');

        if ($this->total === 0) {
            $this->paidAmount = null;
            $this->changeAmount = 0;
        } else {
            $this->changeAmount = ($this->paidAmount ?? 0) - $this->total;
        }
    }

    public function payExact(): void
    {
        if (empty($this->cart)) return;

        $this->paidAmount = $this->total;
        $this->changeAmount = 0;
    }

    public function updatedIsDebt($value)
    {
        if ($value) {
            if (!$this->selectedCustomerId || !$this->dueDate) {
                $this->showDebtModalOnce = true;
            }
        } else {
            $this->showDebtModalOnce = false;
        }
    }

    public function saveDebtData()
    {
        if (!$this->selectedCustomerId) {
            $this->addError('selectedCustomerId', 'Customer harus dipilih.');
            return;
        }
    
        $this->validate([
            'dueDate' => 'required|date|after_or_equal:today',
        ], [
            'dueDate.required' => 'Tanggal jatuh tempo wajib diisi.',
            'dueDate.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
        ]);
    
        $this->selectedCustomer = Customer::find($this->selectedCustomerId);
    
        // Tutup modal
        $this->showDebtModalOnce = false;

        // Hanya jika validasi sukses, baru piutang dianggap aktif
        $this->isDebt = true;
    }

    public function toggleDebtModal()
    {
        $this->isDebt = !$this->isDebt;

        if ($this->isDebt) {
            // Jika piutang tapi belum pilih customer
            if (!$this->selectedCustomer) {
                $this->addError('selectedCustomer', 'Pilih customer terlebih dahulu sebelum menggunakan piutang.');
                $this->isDebt = false;
                return;
            }

            $this->showDebtModalOnce = true;
        } else {
            $this->showDebtModalOnce = false;
            $this->dueDate = null;
        }
    }

    public function cancelDebtModal()
    {
        $this->showDebtModalOnce = false;
        // Hanya reset jika belum lengkap
        if (!$this->selectedCustomerId || !$this->dueDate) {
            $this->isDebt = false;
        }
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong.');
            return;
        }

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            if (!$product || $item['quantity'] > $product->stock) {
                session()->flash('error', 'Stok produk tidak mencukupi untuk ' . $item['name']);
                return;
            }
        }

        if ($this->isDebt) {
            $this->validate([
                'selectedCustomerId' => 'required|exists:customers,id',
                'dueDate' => 'required|date|after_or_equal:today',
            ]);
        } elseif ($this->paidAmount < $this->total) {
            $this->addError('paidAmount', 'Uang yang dibayarkan kurang dari total.');
            return;
        }

        $this->recalculateTotal();

        $status = 'paid';

        if ($this->isDebt) {
            $status = match (true) {
                $this->paidAmount <= 0 => 'unpaid',
                $this->paidAmount < $this->total => 'partial',
                default => 'partial', // agar tidak dianggap lunas walau is_debt
            };

            // Set nilai initial_payment jika piutang
            $this->initialPayment = $this->paidAmount ?? 0;
        } else {
            $status = 'paid';
        }
        
        if ($this->isDebt && $this->initialPayment === null) {
            $this->initialPayment = 0;
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'customer_id' => $this->selectedCustomer?->id,
            'customer_name' => $this->selectedCustomer?->name ?? $this->customerSearch,
            'total' => $this->total,
            'paid' => $this->paidAmount ?? 0,
            'change' => $this->paidAmount ?? 0 - $this->total,
            'is_debt' => $this->isDebt,
            'due_date' => $this->isDebt ? $this->dueDate : null,
            'initial_payment' => $this->isDebt ? $this->initialPayment : $this->total,
            'status' => $status,
        ]);

        if ($this->isDebt && $this->selectedCustomer) {
            Debt::create([
                'customer_name' => $this->selectedCustomer->name,
                'due_date' => $this->dueDate,
                'initial_payment' => $transaction->paid,
                'total' => $this->total,
                'remaining' => $this->total - ($this->paidAmount ?? 0),
                'status' => 'unpaid',
                'transaction_id' => $transaction->id,
            ]);
        }

        foreach ($this->cart as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);

            $product = Product::find($item['id']);
            if ($product) {
                $product->stock -= $item['quantity'];
                $product->sold += $item['quantity'];
                $product->save();
            }
        }

        // $this->changeResult = $this->changeAmount;
        $this->changeResult = $this->changeAmount;
        $this->successIsDebt = $this->isDebt;
        $this->successPaidAmount = $this->paidAmount;
        $this->successTotal = $this->total;

        $this->reset([
            'cart',
            'paidAmount',
            'total',
            'changeAmount',
            'customerSearch',
            'selectedCustomer',
            'selectedCustomerId',
            'isDebt',
            'showDebtModalOnce',
            'dueDate',
            'initialPayment',
        ]);
        $this->resetCounter++;

        $this->transaction_id = $transaction->id;
        $this->showSuccessPopup = true;

        session()->flash('message', 'Transaksi berhasil disimpan.');
    }

    public function printStruk($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->status === 'paid') {
            return redirect()->route('print.struk', $transactionId);
        } else {
            return redirect()->route('print.struk.piutang', $transactionId);
        }
    }

    public function openStruk($url)
    {
        $this->js("window.open('{$url}', '_blank')");
    }

    public function render()
    {
        $query = Product::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $products = $query->paginate(8);

        if ($this->selectedCustomerId) {
            $customer = Customer::find($this->selectedCustomerId);
            if ($customer) {
                $this->selectedCustomer = $customer;
            } else {
                $this->selectedCustomer = null;
                $this->selectedCustomerId = null;
            }
        }

        return view('livewire.order-transaction', [
            'products' => $products,
            'filteredCustomers' => $this->filteredCustomers,
            'showDebtModalOnce' => $this->showDebtModalOnce,
        ]);
    }
}
