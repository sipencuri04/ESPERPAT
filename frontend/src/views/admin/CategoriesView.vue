<template>
  <div class="admin-categories">
    <div class="container mobile-frame">
      <!-- Header -->
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Manage Categories</span>
        <button class="add-btn" @click="openModal()">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
      </header>

      <!-- Category List -->
      <div class="category-list animate-fade-up">
        <div v-if="loading" class="loader">
           <div class="spinner"></div>
           <p>Memuat kategori...</p>
        </div>
        
        <div v-else-if="categories.length === 0" class="empty-state">
           <p>Kategori belum dibuat.</p>
        </div>

        <div v-else v-for="cat in categories" :key="cat.id" class="minimal-card" @click="openEditDrawer(cat)">
          <div class="card-inner">
            <div class="cat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div class="cat-info">
              <h4 class="cat-name">{{ cat.name }}</h4>
              <p class="cat-count">{{ cat.product_count || 0 }} Products</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
        </div>
      </div>

      <!-- Detail/Edit Drawer -->
      <Transition name="slide-up">
        <div v-if="isDrawerOpen" class="modal-overlay" @click.self="closeDrawer">
          <div class="detail-drawer">
            <div class="drawer-handle"></div>
            
            <div class="drawer-header">
                <h3>{{ selectedCategory?.name }}</h3>
                <p>Ubah atau hapus kategori ini</p>
            </div>

            <div class="drawer-actions">
              <button class="btn-action edit" @click="openModal(selectedCategory)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Edit Kategori
              </button>
              <button class="btn-action delete" @click="handleDelete(selectedCategory.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                Hapus Kategori
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Form Overlay -->
      <Transition name="slide-up">
        <div v-if="isModalOpen" class="form-overlay" @click.self="closeModal">
          <div class="form-content">
            <div class="form-header">
              <h3>{{ isEdit ? 'Update Kategori' : 'Tambah Kategori' }}</h3>
              <button @click="closeModal" class="close-btn">✕</button>
            </div>
            
            <form @submit.prevent="handleSave" class="cat-form">
              <div class="form-group">
                <label>Nama Kategori</label>
                <input v-model="form.name" type="text" placeholder="Misal: Mesin, Body, dll..." required />
              </div>

              <div class="form-group">
                <label>Deskripsi (Opsional)</label>
                <textarea v-model="form.description" rows="3" placeholder="Keterangan kategori..."></textarea>
              </div>

              <button type="submit" class="submit-btn" :disabled="submitting">
                {{ submitting ? 'Menyimpan...' : 'Simpan Kategori' }}
              </button>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { useToast } from '../../composables/useToast';
const toast = useToast();

const categories = ref([]);
const loading = ref(true);
const submitting = ref(false);
const isDrawerOpen = ref(false);
const isModalOpen = ref(false);
const isEdit = ref(false);
const selectedCategory = ref(null);

const form = ref({ name: '', description: '' });

const fetchCategories = async () => {
    loading.value = true;
    try {
        const res = await client.get('public/categories?with_count=1');
        categories.value = res.data.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openEditDrawer = (cat) => {
    selectedCategory.value = cat;
    isDrawerOpen.value = true;
};

const closeDrawer = () => { isDrawerOpen.value = false; };

const openModal = (cat = null) => {
    isDrawerOpen.value = false;
    if (cat) {
        isEdit.value = true;
        form.value = { ...cat };
    } else {
        isEdit.value = false;
        form.value = { name: '', description: '' };
    }
    isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const handleSave = async () => {
    submitting.value = true;
    try {
        if (isEdit.value) {
            await client.put(`categories/${form.value.id}`, form.value);
        } else {
            await client.post('categories', form.value);
        }
        closeModal();
        fetchCategories();
    } catch (err) {
        toast.error(err.response?.data?.message || 'Gagal menyimpan.');
    } finally {
        submitting.value = false;
    }
};

const handleDelete = async (id) => {
    toast.ask('Hapus kategori ini? Produk di dalamnya mungkin akan kehilangan kategori.', async () => {
      try {
          await client.delete(`categories/${id}`);
          isDrawerOpen.value = false;
          toast.success('Kategori berhasil dihapus.');
          fetchCategories();
      } catch (err) {
          toast.error('Gagal menghapus kategori.');
      }
    }, 'Hapus Kategori?');
};

onMounted(fetchCategories);
</script>

<style scoped>
.admin-categories { min-height: 100vh; background: #fdfdfd; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; position: sticky; top: 0; z-index: 10; background: #fdfdfd; }
.top-bar .title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }
.add-btn { background: #8b5cf6; color: white; width: 44px; height: 44px; border-radius: 14px; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(139,92,246,0.3); }

/* List */
.category-list { padding: 0 1.5rem 100px; display: flex; flex-direction: column; gap: 10px; }
.minimal-card { background: white; border-radius: 20px; padding: 18px; border: 1px solid #f3f3f3; transition: transform 0.2s; cursor: pointer; }
.minimal-card:active { transform: scale(0.98); background: #f9f9f9; }
.card-inner { display: flex; align-items: center; gap: 18px; }
.cat-icon { width: 44px; height: 44px; background: #f5f3ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.cat-info { flex: 1; }
.cat-name { font-size: 0.95rem; font-weight: 800; color: #111; margin-bottom: 2px; }
.cat-count { font-size: 0.7rem; color: #999; font-weight: 600; }

/* Detail Drawer */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: flex-end; }
.detail-drawer { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 35px; border-top-right-radius: 35px; padding: 1rem 1.7rem 3rem; }
.drawer-handle { width: 40px; height: 5px; background: #eee; border-radius: 5px; margin: 0 auto 1.5rem; }
.drawer-header { margin-bottom: 25px; }
.drawer-header h3 { font-size: 1.3rem; font-weight: 900; color: #111; }
.drawer-header p { font-size: 0.8rem; color: #bbb; font-weight: 600; }

.drawer-actions { display: grid; grid-template-columns: 2fr 1fr; gap: 12px; }
.btn-action { height: 56px; border-radius: 18px; border: none; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; }
.btn-action.edit { background: #111; color: white; }
.btn-action.delete { background: #fff1f2; color: #ef4444; }

/* Form */
.form-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1001; display: flex; align-items: flex-end; }
.form-content { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 40px; border-top-right-radius: 40px; padding: 2rem 1.5rem; max-height: 90vh; overflow-y: auto; }
.form-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
.form-header h3 { font-size: 1.3rem; font-weight: 900; }
.close-btn { background: #f5f5f5; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; }

.cat-form .form-group { margin-bottom: 1.2rem; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; }
.form-group input, .form-group textarea { width: 100%; padding: 14px; border-radius: 14px; border: 1px solid #eee; background: #f9f9f9; font-family: inherit; font-weight: 600; outline: none; }
.form-group input:focus { border-color: #8b5cf6; background: white; }

.submit-btn { width: 100%; height: 56px; background: #8b5cf6; color: white; border: none; border-radius: 18px; font-weight: 800; margin-top: 1rem; cursor: pointer; }

.loader { padding: 50px; text-align: center; }
.spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #8b5cf6; border-radius: 50%; animation: spin 1s infinite linear; margin: 0 auto 10px; }
@keyframes spin { 100% { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 60px; color: #bbb; font-weight: 700; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }
</style>
