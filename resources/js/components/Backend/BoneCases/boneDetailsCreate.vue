<template>
  <div class="container-xxl flex-grow-1 container-p-y">

    <!-- ✅ PAGE LOADER -->
    <div v-if="pageLoading" class="card mb-4">
      <div class="card-body text-center p-4">
        <span class="spinner-border spinner-border-sm me-2"></span>
        Loading...
      </div>
    </div>

    <div v-else class="card mb-6">
      <h5 class="card-header">{{ isEditing ? 'Edit Bone Details' : 'Create Bone Details' }}</h5>

      <form class="card-body" @submit.prevent="submitForm">
        <div class="row g-4">

          <div class="col-md-6">
            <label class="form-label">Select Bone Title</label>

            <MySelect
              v-model="form.bonepost_id"
              :options="boneOptions"
              placeholder="Search Case Code..."
              :maxResults="50"
              :debounceMs="200"
            />

            <div v-if="errors.bonepost_id" class="text-danger small mt-1">
              {{ errors.bonepost_id[0] }}
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Bone Type</label>
            <input
              v-model="form.bone_type"
              type="text"
              :class="['form-control', errors.bone_type ? 'is-invalid' : '']"
              placeholder="e.g. Long Bone, Flat Bone"
            >
            <div v-if="errors.bone_type" class="invalid-feedback">
              {{ errors.bone_type[0] }}
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Body Side</label>
            <select
              v-model="form.body_side"
              :class="['form-select', errors.body_side ? 'is-invalid' : '']"
            >
              <option value="">Select Side</option>
              <option value="Left">Left</option>
              <option value="Right">Right</option>
              <option value="Bilateral">Bilateral</option>
            </select>
            <div v-if="errors.body_side" class="invalid-feedback">
              {{ errors.body_side[0] }}
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Specimen Condition</label>
            <input
              v-model="form.specimen_condition"
              type="text"
              class="form-control"
              placeholder="e.g. Intact, Fragmented"
            >
          </div>

          <div class="col-md-6">
            <label class="form-label">Preservation Method</label>
            <input
              v-model="form.preservation_method"
              type="text"
              class="form-control"
              placeholder="e.g. Dry, Formalin"
            >
          </div>

          <div class="col-md-6">
            <label class="form-label">Quantity</label>
            <input
              v-model.number="form.quantity"
              type="number"
              class="form-control"
              placeholder="1"
              min="1"
            >
          </div>

          <!-- ✅ MULTIPLE IMAGE INPUT -->
          <div class="col-md-12">
            <label class="form-label">Upload Images (Multiple)</label>
            <input
              ref="imageInput"
              type="file"
              class="form-control"
              accept="image/*"
              multiple
              @change="handleImages"
            />
            <div v-if="errors.images" class="text-danger small mt-1">
              {{ errors.images[0] }}
            </div>
          </div>

          <!-- ✅ EXISTING IMAGES (Edit mode only) -->
          <div v-if="isEditing && existingImages.length" class="col-md-12">
            <label class="form-label">Saved Images</label>

            <div class="border rounded p-3 d-flex flex-wrap gap-3">
              <div
                v-for="img in existingImages"
                :key="img.id"
                class="position-relative"
                style="width:110px;height:110px;"
              >
                <img
                  :src="img.images ? '/storage/' + img.images : '/no-image.png'"
                  class="rounded shadow-sm"
                  style="width:110px;height:110px;object-fit:cover;border:1px solid #ddd;"
                />

                <button
                  type="button"
                  class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0"
                  style="border-radius:50%;width:22px;height:22px;line-height:18px;"
                  @click="deleteSavedImage(img)"
                >
                  ×
                </button>
              </div>
            </div>

            <!-- ✅ OPTIONAL: show delete queue count -->
            <div v-if="deletedImageIds.length" class="text-muted small mt-2">
              Marked for delete: {{ deletedImageIds.length }} image(s)
            </div>
          </div>

          <!-- ✅ NEW PREVIEW IMAGES -->
          <div v-if="imagePreviews.length" class="col-md-12">
            <label class="form-label">New Image Preview</label>

            <div class="border rounded p-3 d-flex flex-wrap gap-3">
              <div
                v-for="(src, idx) in imagePreviews"
                :key="src + idx"
                class="position-relative"
                style="width:110px;height:110px;"
              >
                <img
                  :src="src"
                  class="rounded shadow-sm"
                  style="width:110px;height:110px;object-fit:cover;border:1px solid #ddd;"
                />

                <button
                  type="button"
                  class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0"
                  style="border-radius:50%;width:22px;height:22px;line-height:18px;"
                  @click="removeNewImage(idx)"
                >
                  ×
                </button>
              </div>
            </div>
          </div>

        </div>

        <div class="pt-6 mt-4">
          <button type="submit" class="btn btn-primary me-4" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm me-1"></span>
            {{ isEditing ? 'Update Details' : 'Submit Details' }}
          </button>

          <button type="button" @click="resetForm" class="btn btn-label-secondary">
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- ✅ TABLE + PAGINATION -->
    <div class="card mt-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Manage Bone Details</h5>

        <div class="d-flex gap-2 align-items-center">
          <label class="small text-muted mb-0">Per Page</label>
          <select
            class="form-select form-select-sm"
            style="width: 90px"
            v-model.number="perPage"
          >
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
        </div>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>Bone Post</th>
              <th>Type</th>
              <th>Side</th>
              <th>Quantity</th>
              <th>Images</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody v-if="paginatedList.length">
            <tr v-for="item in paginatedList" :key="item.id">
              <td><strong>{{ item.bone?.title || 'N/A' }}</strong></td>
              <td>{{ item.bone_type }}</td>
              <td>{{ item.body_side }}</td>
              <td>{{ item.quantity }}</td>

              <td>
                <div v-if="item.images?.length" class="d-flex flex-wrap gap-2">
                  <img
                    v-for="img in item.images"
                    :key="img.id"
                    :src="img.images ? '/storage/' + img.images : '/no-image.png'"
                    style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #ddd;"
                  />
                </div>
                <span v-else class="text-muted">No image</span>
              </td>

              <td>
                <button
                  @click="editItem(item)"
                  type="button"
                  class="btn btn-sm btn-outline-info me-2"
                >
                  Edit
                </button>

                <button
                  @click="openDeleteModal(item)"
                  type="button"
                  class="btn btn-sm btn-outline-danger"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>

          <tbody v-else>
            <tr><td colspan="6" class="text-center p-4">No details found.</td></tr>
          </tbody>
        </table>
      </div>

      <div class="card-body pt-0 d-flex justify-content-between align-items-center">
        <div class="text-muted small">
          Showing {{ detailsList.length === 0 ? 0 : startIndex + 1 }} - {{ endIndex }} of {{ detailsList.length }}
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary" :disabled="page === 1" @click="page--">Prev</button>
          <button class="btn btn-sm btn-outline-secondary" :disabled="page >= totalPages" @click="page++">Next</button>
        </div>
      </div>
    </div>

    <!-- ✅ DELETE CONFIRM MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" ref="deleteModalEl">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            আপনি কি নিশ্চিত যে আপনি এই পোস্টটি ডিলিট করতে চান?
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
              Cancel
            </button>

            <button type="button" class="btn btn-danger" :disabled="deleteLoading" @click="confirmDelete">
              <span v-if="deleteLoading" class="spinner-border spinner-border-sm me-1"></span>
              Yes, Delete
            </button>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, getCurrentInstance, computed, watch, nextTick } from "vue";
import { useToast } from "vue-toastification";
import MySelect from "@utils/select2.vue";

const toast = useToast();
const { proxy } = getCurrentInstance();
const http = proxy.$http;
const FontendURL = proxy.$http.defaults.FontendURL;

// Bootstrap modal instance
let bsModal = null;

// ---- States ----
const boneOptions = ref([]);
const detailsList = ref([]);

const pageLoading = ref(false);
const isLoading = ref(false);

const isEditing = ref(false);
const editId = ref(null);
const errors = ref({});

// Pagination
const page = ref(1);
const perPage = ref(10);

// Multiple Images states
const imageInput = ref(null);
const newImageFiles = ref([]);  
const imagePreviews = ref([]);  
const existingImages = ref([]);  
const deletedImageIds = ref([]); 

// Form
const form = ref({
  bonepost_id: "",
  bone_type: "",
  body_side: "",
  specimen_condition: "",
  preservation_method: "",
  quantity: 1,
});

const deleteModalEl = ref(null);
const deleteTarget = ref(null);
const deleteLoading = ref(false);

// ---- Computeds ----
const totalPages = computed(() => Math.max(1, Math.ceil(detailsList.value.length / perPage.value)));

watch(perPage, () => (page.value = 1));
watch(detailsList, () => {
  if (page.value > totalPages.value) page.value = totalPages.value;
});

const startIndex = computed(() => (page.value - 1) * perPage.value);
const endIndex = computed(() => Math.min(page.value * perPage.value, detailsList.value.length));
const paginatedList = computed(() => detailsList.value.slice(startIndex.value, endIndex.value));

const getBoneId = () => {
  const v = form.value.bonepost_id;
  if (!v) return "";
  if (typeof v === "object" && v.id) return v.id;
  return v;
};

// ---- Fetch ----
const fetchData = async () => {
  pageLoading.value = true;
  try {
    const [boneRes, detailsRes] = await Promise.all([
      http.get("/bone-cases-create/create"),
      http.get("/bone-details-create/create"),
    ]);

    boneOptions.value = (boneRes.data || []).map((item) => ({
      id: item.id,
      name: item.title,
    }));

    detailsList.value = detailsRes.data || [];
  } catch (error) {
    console.error("Fetch Error:", error);
    toast.error("Failed to load data!");
  } finally {
    pageLoading.value = false;
  }
};


const handleImages = (e) => {
  const files = Array.from(e.target.files || []);
  if (!files.length) return;

  files.forEach((file) => {
    if (!file.type?.startsWith("image/")) return;

    newImageFiles.value.push(file);
    imagePreviews.value.push(URL.createObjectURL(file));
  });
  e.target.value = "";
};

const removeNewImage = (idx) => {
  const oldUrl = imagePreviews.value[idx];
  if (oldUrl) URL.revokeObjectURL(oldUrl);

  imagePreviews.value.splice(idx, 1);
  newImageFiles.value.splice(idx, 1);
};

const clearNewImages = () => {
  imagePreviews.value.forEach((u) => URL.revokeObjectURL(u));
  imagePreviews.value = [];
  newImageFiles.value = [];
  if (imageInput.value) imageInput.value.value = "";
};

const deleteSavedImage = async (img) => {
  if (!img?.id) return;

  existingImages.value = existingImages.value.filter((x) => x.id !== img.id);

  if (!deletedImageIds.value.includes(img.id)) {
    deletedImageIds.value.push(img.id);
  }
};

const submitForm = async () => {
  isLoading.value = true;
  errors.value = {};
  try {
    const fd = new FormData();
    fd.append("bonepost_id", getBoneId());
    fd.append("bone_type", form.value.bone_type || "");
    fd.append("body_side", form.value.body_side || "");
    fd.append("specimen_condition", form.value.specimen_condition || "");
    fd.append("preservation_method", form.value.preservation_method || "");
    fd.append("quantity", String(form.value.quantity || 1));
    newImageFiles.value.forEach((file) => fd.append("images[]", file));
    deletedImageIds.value.forEach((id) => fd.append("deleted_image_ids[]", String(id)));

    let url = "/bone-details-create";
    if (isEditing.value && editId.value) {
      url = `/bone-details-create/${editId.value}`;
      fd.append("_method", "PUT");
    }

    await http.post(url, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    toast.success(isEditing.value ? "Updated successfully!" : "Created successfully!");
    resetForm();
    await fetchData();
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {};
    } else {
      console.error(error);
      toast.error("Process failed.");
    }
  } finally {
    isLoading.value = false;
  }
};

// ---- Edit ----
const editItem = (item) => {
  isEditing.value = true;
  editId.value = item.id;

  form.value = {
    bonepost_id: item.bonepost_id,
    bone_type: item.bone_type || "",
    body_side: item.body_side || "",
    specimen_condition: item.specimen_condition || "",
    preservation_method: item.preservation_method || "",
    quantity: item.quantity || 1,
  };

  existingImages.value = Array.isArray(item.images) ? item.images : [];

  deletedImageIds.value = []; // ✅ reset delete queue on each edit
  clearNewImages();
  window.scrollTo({ top: 0, behavior: "smooth" });
};

// ---- Reset ----
const resetForm = () => {
  form.value = {
    bonepost_id: "",
    bone_type: "",
    body_side: "",
    specimen_condition: "",
    preservation_method: "",
    quantity: 1,
  };

  isEditing.value = false;
  editId.value = null;
  errors.value = {};

  existingImages.value = [];
  deletedImageIds.value = []; 
  clearNewImages();
};

// ---- Delete modal ----
const openDeleteModal = async (item) => {
  deleteTarget.value = item;
  await nextTick();

  const Modal = window.bootstrap?.Modal;
  if (!Modal) {
    toast.error("Bootstrap Modal JS not found!");
    return;
  }

  if (!bsModal) bsModal = new Modal(deleteModalEl.value);
  bsModal.show();
};

const confirmDelete = async () => {
  if (!deleteTarget.value?.id) return;

  deleteLoading.value = true;
  const deleteId = deleteTarget.value.id;
  try {
    await http.delete(`/bone-details-create/${deleteId}`);
    toast.success("Deleted successfully!");
    bsModal?.hide();

    if (editId.value === deleteId) resetForm();

    deleteTarget.value = null;
    await fetchData();
  } catch (error) {
    console.error(error);
    toast.error("Delete failed!");
  } finally {
    deleteLoading.value = false;
  }
};

onMounted(fetchData);

onBeforeUnmount(() => {
  clearNewImages();
});
</script>
