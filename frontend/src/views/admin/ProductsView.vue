<template>
  <div class="admin-products">
    <div class="container mobile-frame">
      <!-- Header -->
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Manage Products</span>
        <button class="add-btn" @click="openModal()">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
      </header>

      <!-- Search Box -->
      <div class="search-box animate-fade-up">
        <div class="input-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input type="text" placeholder="Cari nama produk..." v-model="searchQuery" />
        </div>
      </div>

      <!-- Product Names List (Padded for density) -->
      <div class="product-list animate-fade-up" style="animation-delay: 0.1s">
        <div v-if="loading" class="loader">
           <div class="spinner"></div>
           <p>Memuat data...</p>
        </div>
        
        <div v-else-if="filteredProducts.length === 0" class="empty-state">
           <p>Produk tidak ditemukan.</p>
        </div>

        <div v-else v-for="product in filteredProducts" :key="product.id" class="minimal-card" @click="openDetail(product)">
          <div class="card-inner">
            <div class="prod-thumb">
              <img :src="baseUrl + product.image" v-if="product.image" />
              <div class="thumb-placeholder" v-else>P</div>
            </div>
            <h4 class="prod-name">{{ product.name }}</h4>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
        </div>
      </div>

      <!-- Quick Action Pop-up (Product Detail & Edit/Delete) -->
      <Transition name="fade">
        <div v-if="isDetailOpen" class="modal-overlay" @click.self="closeDetail">
          <div class="detail-drawer">
            <div class="drawer-handle"></div>
            
            <div class="drawer-header">
              <div class="prod-img-large">
                <img :src="baseUrl + selectedProduct.image" v-if="selectedProduct.image" />
                <div class="placeholder" v-else>Part Item</div>
              </div>
              <div class="header-info">
                <span class="cat-tag">{{ selectedProduct.category_name }}</span>
                <h3>{{ selectedProduct.name }}</h3>
              </div>
            </div>

            <div class="drawer-body">
              <div class="info-grid">
                <div class="info-box">
                  <span class="label">Harga Jual</span>
                  <span class="val">{{ formatCurrency(selectedProduct.harga_jual) }}</span>
                </div>
                <div class="info-box">
                  <span class="label">Stok Sekarang</span>
                  <span class="val" :class="{ low: selectedProduct.stok < 10 }">{{ selectedProduct.stok }} Pcs</span>
                </div>
                <div class="info-box full">
                  <span class="label">Deskripsi</span>
                  <p class="desc">{{ selectedProduct.deskripsi || 'Tidak ada deskripsi.' }}</p>
                </div>
              </div>
            </div>

            <div class="drawer-actions">
              <button class="btn-action restock" @click="openRestock">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Restok Barang
              </button>
              <div class="action-row-split">
                <button class="btn-action edit" @click="handleEditFromDetail">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  Edit Data
                </button>
                <button class="btn-action delete" @click="handleDelete(selectedProduct.id)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Edit/Add Form Modal (The one used for input) -->
      <Transition name="slide-up">
        <div v-if="isModalOpen" class="form-overlay" @click.self="closeModal">
          <div class="form-content">
            <div class="form-header">
              <h3>{{ isEdit ? 'Update Sparepart' : 'Tambah Sparepart' }}</h3>
              <button @click="closeModal" class="close-btn">✕</button>
            </div>
            
            <form @submit.prevent="handleSave" class="product-form">
              <div class="form-group">
                <label>Nama Produk</label>
                <input v-model="form.name" type="text" placeholder="Nama sparepart..." required />
              </div>

              <!-- Upload Gambar -->
              <div class="form-group">
                <label>Foto Produk <span class="limit">(Maks. 20KB)</span></label>
                <div class="upload-container">
                  <div class="upload-box" @click="$refs.fileInput.click()">
                    <input type="file" ref="fileInput" @change="handleFileChange" accept="image/*" class="hidden-input" />
                    <div v-if="!imagePreview && !selectedFile" class="upload-placeholder">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                      <span>Klik untuk upload</span>
                    </div>
                    <img v-else :src="imagePreview" class="preview-img" />
                    <button v-if="imagePreview" type="button" class="remove-img" @click.stop="removeImage">✕</button>
                  </div>
                  <p v-if="fileError" class="error-text">{{ fileError }}</p>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Kategori</label>
                  <select v-model="form.category_id" required>
                    <option value="" disabled>Pilih Kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Stok</label>
                  <input v-model.number="form.stok" type="number" placeholder="0" required />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Harga Beli</label>
                  <input v-model.number="form.harga_beli" type="number" placeholder="Rp" required />
                </div>
                <div class="form-group">
                  <label>Harga Jual</label>
                  <input v-model.number="form.harga_jual" type="number" placeholder="Rp" required />
                </div>
              </div>

              <div class="form-group">
                <label>Deskripsi</label>
                <textarea v-model="form.deskripsi" rows="3" placeholder="Detail barang..."></textarea>
              </div>

              <button type="submit" class="submit-btn" :disabled="submitting || !!fileError">
                {{ submitting ? 'Sedang Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </form>
          </div>
        </div>
      </Transition>

      <!-- Image Cropper Modal -->
      <Transition name="fade">
        <div v-if="isCropperOpen" class="modal-overlay crp-overlay">
          <div class="crp-popup animate-pop">
            <h3 style="margin-bottom: 15px; text-align: center; font-weight: 800;">Sesuaikan Foto (1:1)</h3>
            <div class="crop-container">
              <img ref="cropperImage" :src="rawImageUrl" style="max-width: 100%; display: block;" />
            </div>
            <div class="modal-foot">
               <button class="btn-cancel" @click="cancelCrop">Batal</button>
               <button class="btn-confirm" @click="processCrop">{{ submitting ? 'Loading...' : 'Simpan Crop' }}</button>
            </div>
          </div>
        </div>
      </Transition>
 
      <!-- Quick Restock Modal -->
      <Transition name="fade">
        <div v-if="isRestockOpen" class="modal-overlay" @click.self="isRestockOpen = false">
          <div class="restock-modal animate-pop">
            <div class="modal-head">
               <h3>Restok & Update Harga</h3>
               <p>{{ selectedProduct?.name }}</p>
            </div>
            
            <div class="restock-body">
              <!-- Qty Input -->
              <div class="qty-section">
                 <label class="input-label">Jumlah Stok Masuk</label>
                 <div class="qty-control-large">
                    <button type="button" class="q-btn" @click="restockForm.qty = Math.max(1, restockForm.qty - 1)">-</button>
                    <input type="number" v-model.number="restockForm.qty" class="q-input" />
                    <button type="button" class="q-btn plus" @click="restockForm.qty++">+</button>
                 </div>
              </div>

              <!-- Price Management -->
              <div class="price-section">
                <div class="price-card">
                  <div class="p-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    <span>Update Harga Beli</span>
                  </div>
                  <div class="p-input-group">
                    <div class="old-price">Modal Sekarang: {{ formatCurrency(selectedProduct?.harga_beli) }}</div>
                    <div class="input-with-label">
                      <span>Rp</span>
                      <input type="number" v-model.number="restockForm.harga_beli" placeholder="Input harga baru..." />
                    </div>
                  </div>
                </div>

                <div class="price-card">
                  <div class="p-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>Update Harga Jual</span>
                  </div>
                  <div class="p-input-group">
                    <div class="old-price">Harga Sekarang: {{ formatCurrency(selectedProduct?.harga_jual) }}</div>
                    <div class="input-with-label">
                      <span>Rp</span>
                      <input type="number" v-model.number="restockForm.harga_jual" placeholder="Input harga baru..." />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Margin Preview -->
              <div class="profit-box" :class="{ 'is-low': profitPercentage < 15, 'is-danger': profitPercentage < 5 }">
                <div class="p-main">
                  <span class="p-title">Estimasi Margin Laba</span>
                  <div class="p-percent">{{ profitPercentage }}%</div>
                </div>
                <div class="p-sub">
                   <span>Keuntungan Bersih: <strong>{{ formatCurrency(restockForm.harga_jual - restockForm.harga_beli) }}</strong> / item</span>
                </div>
              </div>
            </div>

            <div class="modal-foot">
               <button class="btn-cancel" @click="isRestockOpen = false">Batal</button>
               <button class="btn-confirm" @click="handleRestockSubmit" :disabled="submitting">
                  {{ submitting ? 'Proses...' : 'Update & Restok' }}
               </button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import client from '../../api/client';
import { useToast } from '../../composables/useToast';
const toast = useToast();
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const submitting = ref(false);
const searchQuery = ref('');
const baseUrl = 'https://esperpat-api.atech.my.id/';

// State Management
const isDetailOpen = ref(false);
const isModalOpen = ref(false);
const isRestockOpen = ref(false);
const isEdit = ref(false);
const selectedProduct = ref(null);
const currentId = ref(null);
const restockForm = ref({
  qty: 1,
  harga_beli: 0,
  harga_jual: 0
});

const profitPercentage = computed(() => {
  if (restockForm.value.harga_beli <= 0) return 0;
  const profit = restockForm.value.harga_jual - restockForm.value.harga_beli;
  return ((profit / restockForm.value.harga_beli) * 100).toFixed(1);
});

// Upload & Cropper State
const selectedFile = ref(null);
const fileError = ref('');
const imagePreview = ref('');
const isCropperOpen = ref(false);
const rawImageUrl = ref('');
const cropperImage = ref(null);
let cropperInstance = null;
const rawFile = ref(null);

const form = ref({
  name: '', category_id: '', stok: 0, harga_beli: 0, harga_jual: 0, deskripsi: ''
});

const fetchProducts = async () => {
  try {
    const res = await client.get('public/products');
    products.value = res.data.data.products;
  } catch (err) { console.error(err); } finally { loading.value = false; }
};

const fetchCategories = async () => {
  try {
    const res = await client.get('public/categories');
    categories.value = res.data.data;
  } catch (err) { console.error(err); }
};

const openDetail = (product) => {
  selectedProduct.value = product;
  isDetailOpen.value = true;
};

const closeDetail = () => { isDetailOpen.value = false; };

const openModal = (product = null) => {
  fileError.value = '';
  selectedFile.value = null;
  
  if (product) {
    isEdit.value = true;
    currentId.value = product.id;
    form.value = { ...product };
    imagePreview.value = product.image ? baseUrl + product.image : '';
  } else {
    isEdit.value = false;
    currentId.value = null;
    form.value = { name: '', category_id: '', stok: 0, harga_beli: 0, harga_jual: 0, deskripsi: '' };
    imagePreview.value = '';
  }
  isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  fileError.value = '';
  rawFile.value = file;

  const reader = new FileReader();
  reader.onload = (e) => {
    rawImageUrl.value = e.target.result;
    isCropperOpen.value = true;
    nextTick(() => {
      initCropper();
    });
  };
  reader.readAsDataURL(file);
  e.target.value = ''; // Reset input to allow selecting same file again
};

const initCropper = () => {
  if (cropperInstance) cropperInstance.destroy();
  if (!cropperImage.value) return;
  
  cropperInstance = new Cropper(cropperImage.value, {
    aspectRatio: 1, // Enforce square 1:1
    viewMode: 2,
    dragMode: 'move',
    autoCropArea: 1,
    restore: false,
    guides: true,
    center: true,
    highlight: false,
    cropBoxMovable: true,
    cropBoxResizable: true,
    toggleDragModeOnDblclick: false,
  });
};

const cancelCrop = () => {
  isCropperOpen.value = false;
  if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
  rawImageUrl.value = '';
};

const processCrop = async () => {
  if (!cropperInstance) return;
  submitting.value = true;
  
  try {
    const canvas = cropperInstance.getCroppedCanvas({
      width: 600, // HD output size
      height: 600,
      imageSmoothingEnabled: true,
      imageSmoothingQuality: 'high',
    });
    
    // Custom recursive compression to reach target size (< 20KB limit config)
    const compressFromCanvas = () => {
       return new Promise((resolve) => {
           let quality = 0.8;
           const checkSize = (q) => {
               canvas.toBlob((blob) => {
                  if (blob.size > 22 * 1024 && q > 0.15) { // compress up to 22kb safely
                     checkSize(q - 0.15);
                  } else {
                     const filename = rawFile.value ? rawFile.value.name.replace(/\.[^/.]+$/, "") + ".jpg" : "crop.jpg";
                     const finalFile = new File([blob], filename, { type: 'image/jpeg' });
                     resolve({ file: finalFile, url: canvas.toDataURL('image/jpeg', q) });
                  }
               }, 'image/jpeg', q);
           };
           checkSize(quality);
       });
    };
    
    const { file, url } = await compressFromCanvas();
    if (file.size > 25 * 1024) {
      fileError.value = 'Meskipun sudah dikompres, ukuran file masih terlalu besar. Gunakan gambar/foto lain.';
      selectedFile.value = null;
      imagePreview.value = '';
    } else {
      selectedFile.value = file;
      imagePreview.value = url;
    }
    
    isCropperOpen.value = false;
  } catch (err) {
    fileError.value = 'Gagal menyimpan dan mencompress crop gambar.';
    console.error(err);
  } finally {
    if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
    submitting.value = false;
  }
};

const removeImage = () => {
  selectedFile.value = null;
  imagePreview.value = '';
  fileError.value = '';
};

const handleEditFromDetail = () => {
  const prod = selectedProduct.value;
  closeDetail();
  openModal(prod);
};

const handleSave = async () => {
  submitting.value = true;
  try {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('category_id', form.value.category_id);
    formData.append('stok', form.value.stok);
    formData.append('harga_beli', form.value.harga_beli);
    formData.append('harga_jual', form.value.harga_jual);
    formData.append('deskripsi', form.value.deskripsi || '');
    
    if (selectedFile.value) {
      formData.append('image', selectedFile.value);
    }

    if (isEdit.value) {
      // Use POST with _method spoofing for CI4 multipart PUT
      await client.post(`products/${currentId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await client.post('products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    closeModal();
    fetchProducts();
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan.');
  } finally { submitting.value = false; }
};

const handleDelete = async (id) => {
  toast.ask('Hapus produk ini secara permanen?', async () => {
    try {
      await client.delete(`products/${id}`);
      if (isDetailOpen.value) closeDetail();
      toast.success('Produk berhasil dihapus.');
      fetchProducts();
    } catch (err) { toast.error('Gagal menghapus.'); }
  }, 'Hapus Produk?');
};
 
const openRestock = () => {
  isDetailOpen.value = false;
  restockForm.value = {
    qty: 1,
    harga_beli: selectedProduct.value.harga_beli,
    harga_jual: selectedProduct.value.harga_jual
  };
  isRestockOpen.value = true;
};
 
const handleRestockSubmit = async () => {
  if (restockForm.value.qty <= 0) return;
  submitting.value = true;
  try {
    const newStock = parseInt(selectedProduct.value.stok) + parseInt(restockForm.value.qty);
    
    // We send all required fields to satisfy backend validation
    const res = await client.post(`products/${selectedProduct.value.id}/restock`, {
      qty: restockForm.value.qty,
      harga_beli: restockForm.value.harga_beli,
      harga_jual: restockForm.value.harga_jual
    });
    
    // Update local state with fresh data from server
    const updatedProd = res.data.data.product;
    selectedProduct.value.stok = updatedProd.stok;
    selectedProduct.value.harga_beli = updatedProd.harga_beli;
    selectedProduct.value.harga_jual = updatedProd.harga_jual;
    
    toast.success('Restok berhasil & Kas Keluar tercatat!');
    
    isRestockOpen.value = false;
    fetchProducts();
  } catch (err) {
    toast.error('Gagal update data.');
  } finally {
    submitting.value = false;
  }
};

const filteredProducts = computed(() => {
  return products.value.filter(p => 
    p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};

import { useRoute } from 'vue-router';
const route = useRoute();

onMounted(() => {
  if (route.query.search) {
    searchQuery.value = route.query.search;
  }
  fetchProducts();
  fetchCategories();
});
</script>

<style scoped>
.admin-products { min-height: 100vh; background: #fdfdfd; }

.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; position: sticky; top: 0; background: #fdfdfd; z-index: 10; }
.top-bar .title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }
.add-btn { background: #8b5cf6; color: white; width: 44px; height: 44px; border-radius: 14px; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(139,92,246,0.3); }

.search-box { padding: 0 1.5rem; margin-bottom: 20px; }
.input-wrap { background: #f1f1f1; border-radius: 16px; display: flex; align-items: center; padding: 0 15px; height: 50px; }
.input-wrap input { flex: 1; border: none; background: transparent; padding-left: 10px; font-family: inherit; outline: none; }

.product-list { padding: 0 1.5rem 100px; display: flex; flex-direction: column; gap: 10px; }

/* Minimal List Card */
.minimal-card { background: white; border-radius: 20px; padding: 12px 18px; border: 1px solid #f3f3f3; transition: transform 0.2s; cursor: pointer; }
.minimal-card:active { transform: scale(0.98); background: #f9f9f9; }
.card-inner { display: flex; align-items: center; gap: 15px; }
.prod-thumb { width: 44px; height: 44px; background: #f8f9fa; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.prod-thumb img { width: 100%; height: 100%; object-fit: cover; }
.thumb-placeholder { font-size: 0.7rem; font-weight: 900; color: #ddd; }
.prod-name { flex: 1; font-size: 0.95rem; font-weight: 700; color: #333; }

/* Detail Drawer */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; overflow: hidden; display: flex; align-items: flex-end; }
.detail-drawer { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 35px; border-top-right-radius: 35px; padding: 1rem 1.5rem 2.5rem; animation: slideUp 0.3s ease-out; }

.drawer-handle { width: 40px; height: 5px; background: #eee; border-radius: 5px; margin: 0 auto 1.5rem; }

.drawer-header { display: flex; gap: 20px; align-items: center; margin-bottom: 2rem; }
.prod-img-large { width: 90px; height: 90px; background: #f8f9fa; border-radius: 24px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.prod-img-large img { width: 100%; height: 100%; object-fit: cover; }
.prod-img-large .placeholder { font-size: 0.7rem; font-weight: 900; color: #ccc; }

.header-info h3 { font-size: 1.3rem; font-weight: 900; line-height: 1.2; }
.cat-tag { font-size: 0.65rem; font-weight: 900; color: #8b5cf6; background: #f5f3ff; padding: 4px 10px; border-radius: 8px; text-transform: uppercase; margin-bottom: 5px; display: inline-block; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.info-box { padding: 15px; background: #f8f9fa; border-radius: 18px; }
.info-box.full { grid-column: span 2; }
.info-box .label { display: block; font-size: 0.75rem; color: #999; margin-bottom: 4px; font-weight: 600; }
.info-box .val { font-size: 1rem; font-weight: 800; color: #111; }
.info-box .val.low { color: #ef4444; }
.desc { font-size: 0.85rem; color: #666; line-height: 1.5; }

.drawer-actions { display: flex; flex-direction: column; gap: 12px; margin-top: 2rem; }
.action-row-split { display: grid; grid-template-columns: 2fr 1fr; gap: 12px; }
.btn-action { height: 56px; border-radius: 18px; border: none; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; }
.btn-action.restock { background: #8b5cf6; color: white; box-shadow: 0 8px 20px rgba(139,92,246,0.3); }
.btn-action.edit { background: #111; color: white; }
.btn-action.delete { background: #fff1f2; color: #ef4444; }
 
/* Restock Modal V3 Premium */
.restock-modal { width: 95%; max-width: 400px; background: white; border-radius: 40px; padding: 30px; box-shadow: 0 30px 60px rgba(0,0,0,0.1); max-height: 95vh; overflow-y: auto; }
.modal-head { margin-bottom: 25px; }
.modal-head h3 { font-size: 1.4rem; font-weight: 900; color: #111; margin-bottom: 5px; }
.modal-head p { font-size: 0.85rem; color: #888; font-weight: 600; }

.qty-section { background: #f8fafc; border-radius: 24px; padding: 20px; margin-bottom: 20px; }
.input-label { display: block; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px; }
.qty-control-large { display: flex; align-items: center; justify-content: center; gap: 25px; }
.q-btn { width: 50px; height: 50px; border-radius: 16px; border: none; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-size: 1.5rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
.q-btn:active { transform: scale(0.9); }
.q-btn.plus { background: #111; color: white; }
.q-input { width: 80px; border: none; background: transparent; text-align: center; font-size: 2rem; font-weight: 900; color: #111; outline: none; }

.price-section { display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; }
.price-card { background: white; border: 1.5px solid #f1f5f9; border-radius: 20px; padding: 18px; text-align: left; transition: target 0.3s; }
.price-card:focus-within { border-color: #8b5cf6; background: #faf5ff; }
.p-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
.p-header span { font-size: 0.75rem; font-weight: 800; color: #64748b; }

.p-input-group { position: relative; }
.old-price { font-size: 0.65rem; color: #94a3b8; margin-bottom: 6px; font-weight: 600; }
.input-with-label { display: flex; align-items: center; gap: 4px; }
.input-with-label span { font-weight: 800; font-size: 0.9rem; color: #111; }
.input-with-label input { flex: 1; border: none; background: transparent; font-size: 1.1rem; font-weight: 900; color: #111; outline: none; padding: 4px 0; border-bottom: 2px solid #eee; }

.profit-box { background: #f0fdf4; border: 1.5px solid #dcfce7; border-radius: 22px; padding: 20px; transition: 0.3s; }
.profit-box.is-low { background: #fffbeb; border-color: #fef3c7; }
.profit-box.is-danger { background: #fef2f2; border-color: #fee2e2; }

.p-main { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.p-title { font-size: 0.8rem; font-weight: 800; color: #166534; }
.is-low .p-title { color: #92400e; }
.is-danger .p-title { color: #991b1b; }

.p-percent { font-size: 1.5rem; font-weight: 900; color: #166534; }
.is-low .p-percent { color: #b45309; }
.is-danger .p-percent { color: #ef4444; }

.p-sub { font-size: 0.7rem; font-weight: 500; color: #15803d; opacity: 0.8; }
.is-low .p-sub { color: #92400e; }
.is-danger .p-sub { color: #991b1b; }

.modal-foot { display: flex; gap: 12px; margin-top: 30px; }
.btn-cancel { flex: 1; height: 58px; border-radius: 18px; border: none; background: #f1f5f9; color: #475569; font-weight: 700; cursor: pointer; }
.btn-confirm { flex: 2; height: 58px; border-radius: 18px; border: none; background: #111; color: white; font-weight: 800; font-size: 1rem; cursor: pointer; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
.btn-confirm:active { transform: translateY(2px); }
 
.animate-pop { animation: pop 0.3s cubic-bezier(0.26, 0.53, 0.74, 1.48); }
@keyframes pop { 0% { transform: scale(0.9); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

/* Form Styles */
.form-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1001; display: flex; align-items: flex-end; }
.form-content { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 40px; border-top-right-radius: 40px; padding: 2rem 1.5rem; max-height: 90vh; overflow-y: auto; }
.form-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
.form-header h3 { font-size: 1.3rem; font-weight: 900; }
.close-btn { background: #f5f5f5; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; }

.product-form .form-group { margin-bottom: 1.2rem; }
.limit { font-size: 0.7rem; color: #ef4444; font-weight: 700; }

.upload-container { margin-bottom: 5px; }
.upload-box { width: 100%; height: 120px; background: #f8f9fa; border: 2px dashed #eee; border-radius: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; }
.upload-box:hover { border-color: #8b5cf6; background: #faf5ff; }
.hidden-input { display: none; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 8px; color: #999; font-size: 0.8rem; font-weight: 600; }
.preview-img { width: 100%; height: 100%; object-fit: cover; }
.remove-img { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); color: white; border: none; width: 24px; height: 24px; border-radius: 50%; font-size: 10px; cursor: pointer; }

.error-text { color: #ef4444; font-size: 0.7rem; font-weight: 700; margin-top: 5px; }

.product-form .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 14px; border-radius: 14px; border: 1px solid #eee; background: #f9f9f9; font-family: inherit; }

.submit-btn { width: 100%; height: 56px; background: #8b5cf6; color: white; border: none; border-radius: 18px; font-weight: 800; margin-top: 1rem; cursor: pointer; box-shadow: 0 10px 20px rgba(139,92,246,0.3); }

/* Animations */
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }

.loader { padding: 50px; text-align: center; }
.spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #8b5cf6; border-radius: 50%; animation: spin 1s infinite linear; margin: 0 auto 10px; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Cropper Modal */
.crp-overlay { z-index: 1005 !important; align-items: center; justify-content: center; padding: 20px; }
.crp-popup { width: 100%; max-width: 500px; background: white; border-radius: 20px; padding: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); position: relative; }
.crop-container { width: 100%; height: 60vh; max-height: 400px; background: #e2e8f0; border-radius: 12px; overflow: hidden; }
</style>
