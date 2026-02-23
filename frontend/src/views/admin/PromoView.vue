<template>
  <div class="admin-promo">
    <div class="container mobile-frame">
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Promo & Kupon</span>
        <button class="add-btn" @click="openModal()">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
      </header>

      <!-- Loader -->
      <div v-if="loading" class="loader">Memuat data promo...</div>

      <!-- Active Promos -->
      <div v-else class="promo-list animate-fade-up">
        <div class="section-tag">Kupon Aktif</div>
        
        <div v-for="promo in promoList" :key="promo.id" class="promo-card" :class="promo.discount_type === 'percent' ? 'disc' : 'shipping'">
          <div class="card-side">
            <div class="cutout top"></div>
            <div class="cutout bottom"></div>
          </div>
          <div class="promo-main">
            <div class="promo-info">
              <span class="p-code">{{ promo.code }}</span>
              <h4 class="p-title">
                {{ promo.discount_type === 'percent' ? 'Diskon Persentase' : 'Diskon Nominal' }}
              </h4>
              <p class="p-expiry" v-if="promo.valid_to">Hingga: {{ formatDate(promo.valid_to) }}</p>
              <p class="p-expiry" v-else>Berlaku Selamanya</p>
              
              <div class="p-targets">
                <span v-if="promo.product_id">🎯 {{ promo.product_name }}</span>
                <span v-else>🎯 Semua Produk</span>
                <br>
                <span v-if="promo.customer_id">👤 {{ promo.customer_name }}</span>
                <span v-else>👤 Semua Customer (Umum)</span>
                <br>
                <span v-if="promo.quota">📦 Kuota: {{ promo.claimed_count }} / {{ promo.quota }}</span>
                <span v-else>📦 Kuota: Tanpa Batas ({{ promo.claimed_count }} diklaim)</span>
              </div>
            </div>
            <div class="promo-val">
              <span class="v-amt">{{ promo.discount_value }}</span>
              <span class="v-unit">{{ promo.discount_type === 'percent' ? '%' : 'Rp' }}</span>
              <button @click="deletePromo(promo.id)" class="del-btn">Hapus</button>
            </div>
          </div>
        </div>

        <div v-if="promoList.length === 0" class="empty-promo">
           <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#eee" stroke-width="1.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
           <p>Belum ada kupon promo aktif.</p>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Transition name="slide-up">
      <div v-if="isModalOpen" class="form-overlay" @click.self="isModalOpen = false">
        <div class="form-content">
          <div class="form-header">
            <h3>Buat Kupon Baru</h3>
            <button @click="isModalOpen = false" class="close-btn">✕</button>
          </div>
          
          <form @submit.prevent="savePromo" class="promo-form">
            <div class="form-group">
              <label>Kode Kupon</label>
              <input v-model="form.code" type="text" placeholder="Cth: DISKON20" required class="input-light" />
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label>Tipe Diskon</label>
                <select v-model="form.discount_type" class="input-light">
                  <option value="percent">Persentase (%)</option>
                  <option value="fixed">Nominal (Rp)</option>
                </select>
              </div>
              <div class="form-group">
                <label>Nilai Diskon</label>
                <input v-model="form.discount_value" type="number" min="1" step="0.01" placeholder="Nilai" required class="input-light" />
              </div>
            </div>

            <div class="form-group">
              <label>Berlaku Untuk Produk (Opsional)</label>
              <select v-model="form.product_id" class="input-light">
                <option value="">-- Semua Produk --</option>
                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>

            <div class="form-group">
              <label>Berlaku Untuk Pelanggan (Opsional)</label>
              <select v-model="form.customer_id" class="input-light">
                <option value="">-- Semua Pelanggan (Umum) --</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }} ({{ c.email }})</option>
              </select>
            </div>

            <div class="form-group">
              <label>Batas Kuota / Maksimal Penggunaan (Opsional)</label>
              <input v-model="form.quota" type="number" min="1" placeholder="Kosongkan jika tanpa batas" class="input-light" />
            </div>

            <div class="form-group">
              <label>Tanggal Kedaluwarsa (Opsional)</label>
              <input v-model="form.valid_to" type="datetime-local" class="input-light" />
            </div>

            <div v-if="errorMsg" class="error-msg">{{ errorMsg }}</div>

            <button type="submit" class="submit-btn" :disabled="saving">
              {{ saving ? 'Menyimpan...' : 'Simpan Kupon' }}
            </button>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const isModalOpen = ref(false);
const loading = ref(true);
const saving = ref(false);
const errorMsg = ref('');

const promoList = ref([]);
const products = ref([]);
const customers = ref([]);

const form = ref({
  code: '',
  discount_type: 'percent',
  discount_value: '',
  quota: '',
  product_id: '',
  customer_id: '',
  valid_to: '',
  status: 'active'
});

const fetchCoreData = async () => {
  loading.value = true;
  try {
    const [promoRes, prodRes, custRes] = await Promise.all([
      client.get('promos'),
      client.get('products?limit=100'),
      client.get('customers?role=customer')
    ]);
    
    promoList.value = promoRes.data.data.promos;
    products.value = prodRes.data.data.products;
    customers.value = custRes.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const openModal = () => { 
  form.value = { code: '', discount_type: 'percent', discount_value: '', quota: '', product_id: '', customer_id: '', valid_to: '', status: 'active'};
  errorMsg.value = '';
  isModalOpen.value = true; 
};

const savePromo = async () => {
  saving.value = true;
  errorMsg.value = '';
  try {
    await client.post('promos', form.value);
    isModalOpen.value = false;
    await fetchCoreData();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan promo';
  } finally {
    saving.value = false;
  }
};

const deletePromo = async (id) => {
  if(!confirm('Hapus kupon promo ini?')) return;
  try {
    await client.delete(`promos/${id}`);
    await fetchCoreData();
  } catch(err) {
    console.error(err);
  }
};

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
};

onMounted(fetchCoreData);
</script>

<style scoped>
.admin-promo { min-height: 100vh; background: #fdfdfd; padding-bottom: 50px; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; }
.title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }
.add-btn { background: #111; color: white; width: 44px; height: 44px; border-radius: 14px; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

.loader { text-align: center; padding: 40px; color: #999; font-weight: 700; }

.section-tag { font-size: 0.75rem; font-weight: 800; color: #bbb; text-transform: uppercase; margin: 20px 1.5rem 15px; letter-spacing: 1px; }

.promo-list { padding: 0 1.5rem; display: flex; flex-direction: column; gap: 15px; }

/* Ticket/Coupon UI */
.promo-card { background: white; border-radius: 20px; display: flex; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.03)); position: relative; border: 1px solid #f3f3f3; }
.promo-card.disc { border-left: 5px solid #ef4444; }
.promo-card.shipping { border-left: 5px solid #8b5cf6; } /* Used for fixed nominal currently */

.card-side { width: 12px; position: relative; background: #fdfdfd; }
.cutout { position: absolute; width: 22px; height: 22px; background: #fdfdfd; border: 1px solid #f3f3f3; border-radius: 50%; left: -11px; }
.cutout.top { top: -11px; }
.cutout.bottom { bottom: -11px; }

.promo-main { flex: 1; padding: 15px 20px; display: flex; justify-content: space-between; align-items: stretch; }
.promo-info { flex: 1; }
.p-code { display: inline-block; background: #f8f9fa; padding: 4px 10px; border-radius: 6px; font-weight: 900; font-size: 0.8rem; color: #111; margin-bottom: 8px; border: 1px dashed #ddd; }
.p-title { font-size: 0.95rem; font-weight: 800; color: #111; margin-bottom: 3px; }
.p-expiry { font-size: 0.75rem; color: #bbb; font-weight: 600; margin-bottom: 8px;}
.p-targets { font-size: 0.7rem; color: #666; font-weight: 600; background: #f9f9f9; padding: 6px; border-radius: 6px; margin-top: 5px; line-height: 1.4; }

.promo-val { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between;}
.v-amt { font-size: 1.5rem; font-weight: 900; color: #111; line-height: 1; margin-bottom: 2px;}
.v-unit { font-size: 0.8rem; font-weight: 800; color: #999; }
.del-btn { background: #fef2f2; color: #ef4444; border: none; padding: 5px 10px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.75rem; margin-top: auto;}

.empty-promo { text-align: center; padding: 50px; color: #ccc; font-weight: 700; font-size: 0.85rem; }

/* Form Overlay */
.form-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1001; display: flex; align-items: flex-end; }
.form-content { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 30px; border-top-right-radius: 30px; padding: 2rem 1.7rem; text-align: left; max-height: 90vh; overflow-y: auto;}
.form-header { display: flex; justify-content: space-between; margin-bottom: 1.5rem; align-items: center;}
.form-header h3 { font-size: 1.3rem; font-weight: 900; }
.close-btn { background: #f5f5f5; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-weight: bold;}

.promo-form { display: flex; flex-direction: column; gap: 15px; }
.form-group label { display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 6px; color: #555; }
.input-light { width: 100%; background: #f4f5f7; border: 1px solid #f1f5f9; border-radius: 12px; padding: 0 14px; height: 48px; font-size: 0.9rem; font-family: inherit; font-weight: 600; color: #111; outline: none;}
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.error-msg { color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-align: center; }
.submit-btn { width: 100%; height: 50px; border-radius: 14px; border: none; font-weight: 800; background: #111; color: white; margin-top: 10px;}
.submit-btn:disabled { opacity: 0.6; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }
</style>
