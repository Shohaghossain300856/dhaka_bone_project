<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mt-4 shadow-sm">
      <h5 class="card-header fw-bold text-primary">Delivery Management</h5>

      <!-- Search + Date Filter -->
      <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-2">
        <input 
          type="text" 
          class="form-control w-auto mb-2" 
          placeholder="Search by User, Title..." 
          v-model="searchQuery" 
        />
        <input 
          type="date" 
          class="form-control w-auto mb-2" 
          v-model="startDateFilter" 
        />
        <input 
          type="date" 
          class="form-control w-auto mb-2" 
          v-model="endDateFilter" 
        />
        <button 
          class="btn btn-primary mb-2" 
          @click="printSelected" 
          :disabled="!selectedBones.length"
        >
          Print Selected ({{ selectedBones.length }})
        </button>
      </div>

      <!-- Table -->
      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle text-nowrap">
          <thead class="table-light">
            <tr>
              <th><input type="checkbox" v-model="selectAll" @change="toggleAll"/></th>
              <th>User Name</th>
              <th>Title</th>
              <th>Price</th>
              <th>Auction Date</th>
              <th style="width:120px;">Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="bone in paginatedBones" :key="bone.id">
              <td><input type="checkbox" v-model="selectedBones" :value="bone"/></td>
              <td>{{ bone.latest_bid?.user?.name ?? bone.user?.name ?? 'N/A' }}</td>
              <td>
                <div class="fw-semibold">{{ bone.title }}</div>
                <small class="text-muted">
                  {{ bone.details?.[0]?.body_side ?? 'N/A' }} |
                  {{ bone.details?.[0]?.specimen_condition ?? 'N/A' }}
                </small>
              </td>
              <td>
                <div class="d-flex flex-column align-items-start">
                  <span class="badge bg-primary text-white px-2 py-1 mb-1">
                    Start: ৳ {{ bone.starting_price }}
                  </span>
                  <span class="badge bg-success text-white px-2 py-1">
                    Bids: ৳ {{ bone.latest_bid?.amount ?? bone.starting_price }}
                  </span>
                </div>
              </td>
              <td>
                <small class="text-muted">
                  {{ formatDate(bone.start_date) }} <br> to <br>
                  {{ formatDate(bone.expire_date) }}
                </small>
              </td>
              <td>
                <button class="btn btn-sm"
                        :class="{
                          'btn-warning': bone.status==='active',
                          'btn-info': bone.status==='sold',
                          'btn-success': bone.status==='delivered'
                        }"
                        @click="openStatusModal(bone)">
                  {{ bone.status?.charAt(0).toUpperCase() + bone.status?.slice(1) }}
                </button>
              </td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" @click="printBone(bone)">
                  Print
                </button>
              </td>
            </tr>
            <tr v-if="!paginatedBones.length">
              <td colspan="7" class="text-center text-muted">No bones found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav class="p-3 d-flex justify-content-center">
        <ul class="pagination mb-0">
          <li class="page-item" :class="{disabled: currentPage === 1}">
            <button class="page-link" @click="currentPage--" :disabled="currentPage===1">Previous</button>
          </li>
          <li class="page-item" v-for="page in totalPages" :key="page" :class="{active: page===currentPage}">
            <button class="page-link" @click="currentPage=page">{{ page }}</button>
          </li>
          <li class="page-item" :class="{disabled: currentPage === totalPages}">
            <button class="page-link" @click="currentPage++" :disabled="currentPage===totalPages">Next</button>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true" ref="statusModalRef">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Status</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <select class="form-select" v-model="modalBone.status">
              <option value="sold">Sold</option>
              <option value="delivered">Delivered</option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" @click="saveStatus" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, getCurrentInstance } from "vue";
import { useToast } from "vue-toastification";
import bootstrap from "bootstrap/dist/js/bootstrap.bundle.min.js";

const bones = ref([]);
const modalBone = ref({});
const searchQuery = ref('');
const startDateFilter = ref('');
const endDateFilter = ref('');
const currentPage = ref(1);
const perPage = 10;
const loading = ref(false);

const selectedBones = ref([]);
const selectAll = ref(false);

const { proxy } = getCurrentInstance();
const http = proxy.$http;
const toast = useToast();
const statusModalRef = ref(null);

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
};

const fetchBones = async () => {
  try {
    const res = await http.get("delivary/create");
    bones.value = res.data?.data ?? [];
  } catch (err) {
    console.error(err);
    toast.error("Failed to fetch bones!");
  }
};

onMounted(fetchBones);

const filteredBones = computed(() => {
  return bones.value.filter(bone => {
    // Search
    const q = searchQuery.value.toLowerCase();
    const matchesSearch = 
      (bone.latest_bid?.user?.name ?? bone.user?.name ?? '').toLowerCase().includes(q) ||
      bone.title.toLowerCase().includes(q);

    // Date filter
    const startDate = startDateFilter.value ? new Date(startDateFilter.value) : null;
    const endDate = endDateFilter.value ? new Date(endDateFilter.value) : null;
    const boneStart = new Date(bone.start_date);
    const boneEnd = new Date(bone.expire_date);

    const matchesDate = (!startDate || boneStart >= startDate) &&
                        (!endDate || boneEnd <= endDate);

    return matchesSearch && matchesDate;
  });
});

const totalPages = computed(() => Math.ceil(filteredBones.value.length / perPage));
const paginatedBones = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredBones.value.slice(start, start + perPage);
});

const toggleAll = () => {
  if (selectAll.value) selectedBones.value = [...paginatedBones.value];
  else selectedBones.value = [];
};

const openStatusModal = (bone) => {
  modalBone.value = { ...bone };
  new bootstrap.Modal(statusModalRef.value).show();
};
const closeModal = () => bootstrap.Modal.getInstance(statusModalRef.value).hide();

const saveStatus = async () => {
  loading.value = true;
  try {
    await http.put(`delivary/${modalBone.value.id}/status`, { status: modalBone.value.status });
    const index = bones.value.findIndex(b => b.id === modalBone.value.id);
    if(index !== -1) bones.value[index].status = modalBone.value.status;
    toast.success("Status updated successfully!");
    closeModal();
  } catch(err) {
    console.error(err);
    toast.error("Failed to update status!");
  } finally {
    loading.value = false;
  }
};

const printBone = (bone) => {
  const html = generatePrintHTML([bone]);
  const newWin = window.open("", "_blank");
  newWin.document.write(html);
  newWin.document.close();
  newWin.print();
};

const printSelected = () => {
  if(!selectedBones.value.length) return;
  const html = generatePrintHTML(selectedBones.value);
  const newWin = window.open("", "_blank");
  newWin.document.write(html);
  newWin.document.close();
  newWin.print();
};

const generatePrintHTML = (bonesToPrint) => {
  let rows = '';
  bonesToPrint.forEach(bone => {
    rows += `
      <tr>
        <td>${bone.latest_bid?.user?.name ?? bone.user?.name ?? 'N/A'}</td>
        <td>${bone.title}</td>
        <td>${bone.details?.[0]?.body_side ?? 'N/A'}</td>
        <td>${bone.details?.[0]?.specimen_condition ?? 'N/A'}</td>
        <td>৳ ${bone.starting_price}</td>
        <td>৳ ${bone.latest_bid?.amount ?? bone.starting_price}</td>
        <td>${bone.status}</td>
      </tr>
    `;
  });

  return `
    <html>
      <head>
        <title>Dhaka Medical College Bone Auction</title>
        <style>
          body { font-family: Arial; padding:20px; font-size:12px; text-align:center; }
          .head-padding{ padding-bottom:50px; }
          h2, h4, h3 { margin:0;}
          h2 { font-weight:bold; font-size:16px;}
          h4 { font-weight:normal; font-size:12px; color:#555; margin-bottom:10px;}
          h3 { font-size:14px; margin:15px 0; font-weight:bold;}
          table { width:100%; border-collapse:collapse; margin:0 auto; font-size:11px;}
          th, td { border:1px solid #ddd; padding:5px; text-align:left; }
          th { background:#f8f8f8; }
          .signature { display:flex; justify-content:center; gap:50px; margin-top:50px; }
          .signature div { border-top:1px solid #000; padding-top:5px; width:200px; text-align:center; font-size:12px; }
        </style>
      </head>
      <body>
        <div class="head-padding">
          <h2>Dhaka Medical College</h2>
          <h4>Shahbagh, Dhaka-1000, Bangladesh</h4>
          <h3>Bone Auction Details</h3>
        </div>
        <table>
          <thead>
            <tr>
              <th>User</th>
              <th>Title</th>
              <th>Body Side</th>
              <th>Condition</th>
              <th>Start Price</th>
              <th>Latest Bid</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
        <div class="signature">
          <div>Prepared by</div>
          <div>Checked by</div>
        </div>
      </body>
    </html>
  `;
};
</script>

<style scoped>
.table-hover tbody tr:hover { background-color: #f8f9fa; }
.badge { font-size:0.875rem; font-weight:500; border-radius:0.5rem; }
</style>