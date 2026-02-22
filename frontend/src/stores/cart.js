import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: JSON.parse(localStorage.getItem('cart') || '[]'),
    }),
    getters: {
        totalItems: (state) => state.items.reduce((acc, item) => acc + item.qty, 0),
        subtotal: (state) => state.items.reduce((acc, item) => acc + (item.harga_jual * item.qty), 0),
        deliveryFee: () => 15000,
        discount: () => 0,
        total: (state) => {
            const sub = state.items.reduce((acc, item) => acc + (item.harga_jual * item.qty), 0);
            return sub + 15000;
        }
    },
    actions: {
        addItem(product) {
            const existing = this.items.find(i => i.id === product.id);
            if (existing) {
                existing.qty++;
            } else {
                this.items.push({
                    ...product,
                    qty: 1
                });
            }
            this.save();
        },
        removeItem(id) {
            this.items = this.items.filter(i => i.id !== id);
            this.save();
        },
        updateQty(id, qty) {
            const item = this.items.find(i => i.id === id);
            if (item) {
                item.qty = Math.max(1, qty);
                this.save();
            }
        },
        clearCart() {
            this.items = [];
            this.save();
        },
        save() {
            localStorage.setItem('cart', JSON.stringify(this.items));
        }
    }
});
