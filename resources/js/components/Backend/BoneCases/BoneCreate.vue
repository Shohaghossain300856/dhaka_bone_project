<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-md-10"></div>
      <div class="col-md-2">
        <div v-if="imagePreview" class="mt-3 position-relative d-inline-block">
          <img :src="imagePreview" class="rounded shadow-sm" style="height: 100px; width: 100px; object-fit: cover; border: 1px solid #ddd;">
          <button type="button" @click="removeImage" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0" style="border-radius: 50%; width: 20px; height: 20px;">×</button>
        </div>
      </div>
    </div>
    <div class="card mb-6">
      <h5 class="card-header">{{ isEditing ? 'Edit Bone Post' : 'Create Bone Post' }}</h5>
      <form class="card-body" @submit.prevent="submitForm">
        <div class="row g-4">
         <div class="col-md-6">
          <label class="form-label">Bone Title</label>
          <input v-model="form.title" type="text" :class="['form-control', errors.title ? 'is-invalid' : '']" placeholder="Enter title">
          <div v-if="errors.title" class="invalid-feedback">{{ errors.title[0] }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Bone Name</label>
          <input v-model="form.name" type="text" :class="['form-control', errors.name ? 'is-invalid' : '']" placeholder="Enter bone name">
          <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Starting Price</label>
          <input v-model="form.starting_price" type="number" :class="['form-control', errors.starting_price ? 'is-invalid' : '']" placeholder="0.00">
           <div v-if="errors.starting_price" class="invalid-feedback">{{ errors.starting_price[0] }}</div>
        </div>
        <div class="col-md-6">
        <label class="form-label">Start Date</label>
        <MyDateInput v-model="form.start_date" :class="errors.start_date ? 'is-invalid' : ''" />
        <div v-if="errors.start_date" class="text-danger small mt-1">{{ errors.start_date[0] }}</div>
      </div>
       <div class="col-md-6">
            <label class="form-label">Expire Date</label>
            <MyDateInput v-model="form.expire_date" :class="errors.expire_date ? 'is-invalid' : ''" />
            <div v-if="errors.expire_date" class="text-danger small mt-1">{{ errors.expire_date[0] }}</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Upload Image</label>
          <input type="file" :class="['form-control', errors.image ? 'is-invalid' : '']" @change="handleFileUpload" accept="image/*">
          <div v-if="errors.image" class="invalid-feedback" style="display: block;">{{ errors.image[0] }}</div>
        </div>

<div class="col-12">
  <label class="form-label">Description</label>
  <textarea 
    v-model="form.description" 
    :class="['form-control', errors.description ? 'is-invalid' : '']" 
    rows="2"
  ></textarea>
  
  <div v-if="errors.description" class="invalid-feedback">
    {{ errors.description[0] }}
  </div>
</div>
      </div>

      <div class="pt-6 mt-4">
        <button type="submit" class="btn btn-primary me-4" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm me-1"></span>
          {{ isEditing ? 'Update Post' : 'Submit Post' }}
        </button>
        <button type="button" @click="resetForm" class="btn btn-label-secondary">Cancel</button>
      </div>
    </form>
  </div>

  <div class="card mt-4">
    <h5 class="card-header">Bone Posts Management</h5>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Price</th>
            <th>Dates</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody v-if="items.length > 0">
          <tr v-for="item in items" :key="item.id">
            <td>
             <img 
             :src="item.image ? FontendURL + item.image : '/no-image.png'" 
             class="rounded" 
             style="width: 40px; height: 40px; object-fit: cover;"
             >
           </td>
           <td><strong>{{ item.title }}</strong></td>
           <td>{{ item.starting_price }}</td>
           <td><small>{{ item.start_date }} to {{ item.expire_date }}</small></td>
           <td>

            <button @click="editItem(item)" type="button" class="btn btn-outline-info waves-effect me-2">Edit</button>
            <button @click="deleteItem(item.id)" type="button" class="btn btn-outline-danger waves-effect">Delete</button>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr><td colspan="5" class="text-center p-4">No data found.</td></tr>
      </tbody>
    </table>
  </div>
</div>

</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" @click="confirmDelete">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
</template>

<script setup>
  import { ref, onMounted, getCurrentInstance } from "vue";
  import { useToast } from "vue-toastification";
  import MyDateInput from "@utils/FriendlyDatePicker.vue";

  const toast = useToast();
  const { proxy } = getCurrentInstance();
  const http = proxy.$http;
  const FontendURL = proxy.$http.defaults.FontendURL; 

  const items = ref([]);
  const isLoading = ref(false);
  const isEditing = ref(false);
  const editId = ref(null);
  const imagePreview = ref(null);
  const deleteId = ref(null);
  const errors = ref({});
  const form = ref({
    title: '',
    name: '',
    starting_price: '',
    start_date: '',
    expire_date: '',
    description: '',
    image: null
  });

  const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
      form.value.image = file;
      imagePreview.value = URL.createObjectURL(file);
    }
  };

  const removeImage = () => {
    form.value.image = null;
    imagePreview.value = null;
  };

  const fetchItems = async () => {
    try {
      const res = await http.get('/bone-cases-create/create'); 
      items.value = res.data;
      console.log(items.valu)
    } catch (error) {
      console.error(error);
    }
  };

  const submitForm = async () => {
    isLoading.value = true;
    errors.value = {}; 

    const formData = new FormData();
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null) {
        formData.append(key, form.value[key]);
      }
    });

    if (isEditing.value) {
      formData.append('_method', 'PUT');
    }

    try {
      const url = isEditing.value ? `/bone-cases-create/${editId.value}` : '/bone-cases-create';
      const response = await http.post(url, formData);

      if (response.status === 200 || response.status === 201) {
        toast.success(isEditing.value ? "Post Updated successfully!" : "Post Created successfully!");
        resetForm();
        fetchItems();
      }
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors;
        toast.error("Validation Failed!");
      } else {
        toast.error("Something went wrong!");
      }
    } finally {
      isLoading.value = false;
    }
  };

  const editItem = (item) => {
    isEditing.value = true;
    editId.value = item.id;

    form.value = {
      title: item.title ?? '',
      name: item.name ?? '',
      starting_price: item.starting_price ?? '',
      start_date: item.start_date ?? '',
      expire_date: item.expire_date ?? '',
      description: item.description ?? '',
      image: null,
    };

    imagePreview.value = item.image ? (FontendURL + item.image) : null;

    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const deleteItem = (id) => {
    deleteId.value = id;
    const myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
  };

  const confirmDelete = async () => {
    if (!deleteId.value) return;

    try {
      await http.delete(`/bone-cases-create/${deleteId.value}`);
      toast.info("Post Deleted Successfully!");

      const modalElement = document.getElementById('deleteModal');
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      modalInstance.hide();

      fetchItems(); 
    } catch (error) {
      toast.error("Delete failed!");
    } finally {
      deleteId.value = null;
    }
  };

  const resetForm = () => {
    form.value = { title: '', name: '', starting_price: '', start_date: '', expire_date: '', description: '', image: null };
    imagePreview.value = null;
    isEditing.value = false;
    editId.value = null;
    errors.value = {}; 
  };

  onMounted(() => {

    fetchItems();
  });
</script>