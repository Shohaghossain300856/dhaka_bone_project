<template>
  <div class="position-relative">
    <div class="input-group">
      <input
        type="text"
        class="form-control"
        :placeholder="placeholder"
        v-model="searchQuery"
        @focus="openDropdown"
        style="border-radius: 5px;"
      />

      <span class="input-group-text cursor-pointer" @click="toggleDropdown">
        <i style="font-size: 14px;" class="fa fa-chevron-down"></i>
      </span>
    </div>

    <!-- Dropdown -->
    <ul
      v-if="isOpen"
      class="dropdown-menu show w-100 shadow-sm p-0"
      style="max-height: 250px; overflow-y: auto; display: block; top: 100%; z-index: 9999;"
    >
      <li v-if="filteredOptions.length === 0">
        <span class="dropdown-item text-muted text-center py-2">No results found</span>
      </li>

      <li v-for="option in filteredOptions" :key="option.id">
        <a
          class="dropdown-item py-2 border-bottom cursor-pointer"
          @click.prevent="selectOption(option)"
        >
          {{ option.name }}
        </a>
      </li>

      <li v-if="showMoreHint">
        <span class="dropdown-item text-muted text-center py-2">
          Showing first {{ maxResults }} results. Type more to narrow down.
        </span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  modelValue: [String, Number],
  options: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: "Search...",
  },
  maxResults: {
    type: Number,
    default: 50, // ✅ limit DOM render
  },
  debounceMs: {
    type: Number,
    default: 200, // ✅ debounce
  },
});

const emit = defineEmits(["update:modelValue"]);

const searchQuery = ref("");
const debouncedQuery = ref("");
const isOpen = ref(false);

let timer = null;

// ✅ Debounce typing
watch(searchQuery, (v) => {
  clearTimeout(timer);
  timer = setTimeout(() => {
    debouncedQuery.value = v || "";
  }, props.debounceMs);
});

// ✅ Keep input synced with selected option
const syncSelectedText = () => {
  const selected = props.options.find((o) => String(o.id) === String(props.modelValue));
  searchQuery.value = selected ? selected.name : "";
};

watch(
  () => props.modelValue,
  () => syncSelectedText(),
  { immediate: true }
);

watch(
  () => props.options,
  () => syncSelectedText()
);

// ✅ Filter with limit
const maxResults = computed(() => props.maxResults);

const filteredOptions = computed(() => {
  const q = (debouncedQuery.value || "").trim().toLowerCase();

  // Empty query => show first N only
  if (!q) return props.options.slice(0, maxResults.value);

  // Filter + slice
  return props.options
    .filter((opt) => (opt.name || "").toLowerCase().includes(q))
    .slice(0, maxResults.value);
});

const showMoreHint = computed(() => {
  const q = (debouncedQuery.value || "").trim().toLowerCase();
  if (!q) return props.options.length > maxResults.value;
  // If query matches too many, we still slice
  const count = props.options.filter((opt) => (opt.name || "").toLowerCase().includes(q)).length;
  return count > maxResults.value;
});

const selectOption = (option) => {
  searchQuery.value = option.name;
  emit("update:modelValue", option.id);
  isOpen.value = false;
};

const openDropdown = () => {
  isOpen.value = true;
};

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

// ✅ One global click handler (fast)
const handleClickOutside = (event) => {
  const root = event.target.closest(".position-relative");
  // If click outside this component (approx safe for single instance)
  // Better: use ref for root if you need multiple instances.
  // If you have multiple instances, tell me, I’ll give perfect per-instance solution.
  if (!event.target.closest(".input-group") && !event.target.closest(".dropdown-menu")) {
    if (isOpen.value) {
      isOpen.value = false;
      syncSelectedText();
    }
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside, true);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside, true);
});
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
.dropdown-item:hover {
  background-color: #f8f9fa;
}
.dropdown-menu {
  border: 1px solid #ddd;
  border-radius: 4px;
}
</style>
