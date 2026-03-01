<template>
  <div class="container-xxl py-5 bone-details-pro">
    <div class="row g-4">

      <!-- LEFT -->
      <div class="col-lg-5 col-xl-4">
        <div class="sticky-top" style="top: 20px;">

          <!-- GALLERY -->
          <div class="main-card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-white text-center">
            <div class="position-relative gallery-container" @mouseenter="stopSlider" @mouseleave="startSlider">
              <transition name="fade" mode="out-in">
                <img
                  :key="activeImage"
                  :src="getImageUrl(activeImage)"
                  class="img-fluid display-image"
                  alt="Specimen"
                >
              </transition>

              <div class="slider-nav" v-if="allImages.length > 1">
                <button @click="prevSlide" class="nav-btn left"><i class="ti ti-chevron-left"></i></button>
                <button @click="nextSlide" class="nav-btn right"><i class="ti ti-chevron-right"></i></button>
              </div>
            </div>

            <div class="thumb-strip p-3 d-flex gap-2 overflow-auto justify-content-center" v-if="allImages.length > 1">
              <div
                v-for="(img, idx) in allImages"
                :key="idx"
                class="mini-thumb"
                :class="{ active: currentIndex === idx }"
                @click="setSlide(idx)"
              >
                <img :src="getImageUrl(img)" class="rounded shadow-sm">
              </div>
            </div>
          </div>

          <!-- LATEST BIDDER -->
          <div class="card border-0 shadow-sm rounded-4 bidder-card bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
              <h6 class="fw-bold d-flex align-items-center mb-0">
                <i class="ti ti-history me-2 text-primary"></i> Latest Bidder
              </h6>
            </div>

            <div class="card-body">
              <div v-if="latestBid" class="bid-row d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                  <img
                    :src="getImageUrl('user/' + (latestBid.user?.user_image || ''))"
                    class="rounded-circle"
                    width="35"
                    height="35"
                    style="object-fit: cover;"
                  >
                  <div>
                    <span class="fw-bold d-block small">{{ latestBid.user?.name || 'Unknown' }}</span>
                    <small class="text-muted" style="font-size: 10px;">{{ formatDateTime(latestBid.created_at) }}</small>
                  </div>
                </div>
                <div class="text-primary fw-bold">
                  <span class="tk">৳</span>
                  <span class="amt">{{ latestBid.amount }}</span>
                </div>
              </div>

              <div v-else class="text-center text-muted py-2 small">No bids yet</div>
            </div>
          </div>

          <!-- BID HISTORY -->
          <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex align-items-center justify-content-between">
              <h6 class="fw-bold d-flex align-items-center mb-0">
                <i class="ti ti-list-details me-2 text-primary"></i> Bid History
              </h6>

              <small class="text-muted" v-if="sortedBids.length">
                {{ sortedBids.length }} bids
              </small>
            </div>

            <div class="card-body pt-3">
              <div v-if="sortedBids.length === 0" class="text-center text-muted py-2 small">
                No bids yet
              </div>

              <div v-else class="bid-history">
                <div
                  v-for="bid in visibleBids"
                  :key="bid.id || bid._tmpId"
                  class="history-row d-flex justify-content-between align-items-center py-2"
                >
                  <div class="d-flex align-items-center gap-2">
                    <img
                      :src="getImageUrl('user/' + (bid.user?.user_image || ''))"
                      class="rounded-circle"
                      width="30"
                      height="30"
                      style="object-fit: cover;"
                    >
                    <div>
                      <div class="fw-semibold small">{{ bid.user?.name || 'Unknown' }}</div>
                      <div class="text-muted" style="font-size: 11px;">{{ formatDateTime(bid.created_at) }}</div>
                    </div>
                  </div>

                  <div class="fw-bold text-dark">
                    <span class="tk">৳</span>
                    <span class="amt">{{ bid.amount }}</span>
                  </div>
                </div>

                <div class="text-center pt-3" v-if="sortedBids.length > 5">
                  <button class="btn btn-outline-primary btn-sm px-3" @click="toggleAllBids">
                    {{ showAllBids ? 'Show Less' : 'View All' }}
                  </button>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- RIGHT -->
      <div class="col-lg-7 col-xl-8">
        <div class="details-body ps-lg-3">
          <div class="mb-4">
            <span class="category-tag mb-2 d-inline-block">{{ boneData.details?.[0]?.bone_type }}</span>
            <h1 class="main-title">{{ boneData.name }}</h1>
          </div>

          <div class="action-box card border-0 shadow-lg rounded-4 mb-5 p-4 bg-white">
            <div class="row align-items-center">
              <div class="col-md-7 border-end">
                <span class="text-muted small fw-bold">CURRENT BID</span>

                <!-- ✅ current bid এখন latestBid থেকে live update হবে -->
                <h2 class="current-price mb-0">
                  <span class="tk">৳</span>
                  <span class="amt">{{ currentBidAmount }}</span>
                </h2>

                <div class="mt-2">
                  <small class="text-muted me-3">Start: <b>৳ {{ boneData.starting_price }}</b></small>
                  <small class="text-success fw-bold" v-if="!isExpired">
                    <i class="ti ti-bolt"></i> Live updating:&nbsp;&nbsp;
                    <span style="color:red;font-size:16px">{{ remainingText }}</span>
                  </small>
                  <small class="text-danger fw-bold" v-else>
                    <i class="ti ti-ban"></i> Closed
                  </small>
                </div>
              </div>

              <div class="col-md-5 ps-md-4">
                <div class="input-group mt-3 mt-md-0">
                  <input
                    type="number"
                    class="form-control form-control-lg border-2 shadow-none"
                    :min="minBid"
                    :placeholder="'Min ৳ ' + minBid"
                    v-model.number="bidAmount"
                    :disabled="isExpired || loadingBid"
                  >
                  <button
                    class="btn btn-primary btn-lg px-4 fw-bold"
                    :disabled="isExpired || loadingBid || !bidAmount || bidAmount < minBid"
                    @click="submitBid()"
                  >
                    {{ loadingBid ? '...' : 'Bid' }}
                  </button>
                </div>
                <small class="text-muted d-block mt-2" v-if="!isExpired">
                  Minimum bid must be <span style="font-size:18px">৳</span> {{ minBid }} or higher.
                </small>
              </div>
            </div>
          </div>

          <div class="description-section">
            <h4 class="fw-bold mb-3">Description</h4>
            <div class="title-line mb-4"></div>
            <div class="description-text">
              <p v-for="(p, i) in descriptionParagraphs" :key="i">{{ p }}</p>
            </div>
          </div>

          <div class="specs-section mt-5">
            <h4 class="fw-bold mb-3">Full Specifications</h4>
            <div class="title-line mb-4"></div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
              <div class="row g-0">
                <div
                  v-for="(row, idx) in specRows"
                  :key="idx"
                  class="col-md-6 col-lg-4 border-bottom border-end spec-item"
                >
                  <div class="p-3 d-flex align-items-start gap-3 h-100 bg-white hover-bg">
                    <div class="spec-icon-wrapper">
                      <i :class="row.icon"></i>
                    </div>
                    <div>
                      <small class="text-uppercase text-muted fw-bold ls-1" style="font-size: 10px;">{{ row.label }}</small>
                      <div class="fw-semibold text-dark">{{ row.value || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, getCurrentInstance, onMounted, onUnmounted, watch } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const { proxy } = getCurrentInstance();
const http = proxy.$http;
const currentIndex = ref(0);
const sliderInterval = ref(null);
const bidAmount = ref(null);
const loadingBid = ref(false);

const props = defineProps({
  bone: { type: Object, required: true },
});

const boneData = ref(structuredClone(props.bone));
watch(
  () => props.bone,
  (val) => (boneData.value = structuredClone(val)),
  { deep: true }
);
const getImageUrl = (path) => {
  if (!path) return "https://via.placeholder.com/500x320?text=No+Image";
  if (!path.startsWith("http") && !path.startsWith("/storage") && !path.startsWith("storage")) {
    return "/storage/" + path;
  }
  return path.startsWith("http") ? path : (path.startsWith("/") ? path : "/" + path);
};

const allImages = computed(() => {
  const images = [];
  if (boneData.value.image) images.push(boneData.value.image);

  if (boneData.value.details?.length) {
    boneData.value.details.forEach((detail) => {
      detail.images?.forEach((imgObj) => images.push(imgObj.images));
    });
  }
  return images;
});

const activeImage = computed(() => allImages.value[currentIndex.value]);

const startSlider = () => {
  stopSlider();
  if (allImages.value.length > 1) sliderInterval.value = setInterval(nextSlide, 4000);
};
const stopSlider = () => {
  if (sliderInterval.value) clearInterval(sliderInterval.value);
  sliderInterval.value = null;
};
const nextSlide = () => {
  if (!allImages.value.length) return;
  currentIndex.value = (currentIndex.value + 1) % allImages.value.length;
};
const prevSlide = () => {
  if (!allImages.value.length) return;
  currentIndex.value = (currentIndex.value - 1 + allImages.value.length) % allImages.value.length;
};
const setSlide = (idx) => (currentIndex.value = idx);
const showAllBids = ref(false);

const sortedBids = computed(() => {
  const bids = Array.isArray(boneData.value.bids) ? [...boneData.value.bids] : [];
  bids.sort((a, b) => {
    const ad = new Date(a.created_at || 0).getTime();
    const bd = new Date(b.created_at || 0).getTime();
    return bd - ad || (b.id || 0) - (a.id || 0);
  });
  return bids;
});

const latestBid = computed(() => sortedBids.value[0] || null);

const visibleBids = computed(() => (showAllBids.value ? sortedBids.value : sortedBids.value.slice(0, 5)));
const toggleAllBids = () => (showAllBids.value = !showAllBids.value);
const currentBidAmount = computed(() => {
  return latestBid.value?.amount ?? boneData.value.starting_price;
});

const remainingText = ref("");
const isExpired = ref(false);
const clockInterval = ref(null);

const parseExpireDate = (dateStr) => {
  if (!dateStr) return null;
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return new Date(dateStr + "T23:59:59");
  return new Date(dateStr);
};

const pad2 = (n) => String(n).padStart(2, "0");

const updateCountdown = () => {
  const end = parseExpireDate(boneData.value.expire_date);
  if (!end || isNaN(end.getTime())) {
    remainingText.value = "No Expiry";
    isExpired.value = false;
    return;
  }

  const diff = end.getTime() - Date.now();
  if (diff <= 0) {
    isExpired.value = true;
    remainingText.value = "Expired";
    return;
  }

  isExpired.value = false;
  const totalSeconds = Math.floor(diff / 1000);
  const days = Math.floor(totalSeconds / 86400);
  const hours = Math.floor((totalSeconds % 86400) / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  if (days > 0) remainingText.value = `${days}d ${pad2(hours)}h:${pad2(minutes)}m:${pad2(seconds)}s Left`;
  else remainingText.value = `${pad2(hours)}:${pad2(minutes)}:${pad2(seconds)} Left`;
};

const startClock = () => {
  stopClock();
  updateCountdown();
  clockInterval.value = setInterval(updateCountdown, 1000);
};
const stopClock = () => {
  if (clockInterval.value) clearInterval(clockInterval.value);
  clockInterval.value = null;
};

watch(() => boneData.value.expire_date, updateCountdown);
const minBid = computed(() => {
  const current = parseFloat(latestBid.value?.amount || boneData.value.starting_price || 0);
  return Math.floor(current + 1);
});


const formatDateTime = (date) => {
  if (!date) return "";
  const d = new Date(date);
  if (isNaN(d.getTime())) return "";
  return d.toLocaleString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const descriptionParagraphs = computed(() => (boneData.value.description ? boneData.value.description.split("\r\n\r\n") : []));

const loadBone = async () => {
  const res = await http.get(`bone-details/${boneData.value.id}`);
  boneData.value = res.data;
};

const submitBid = async () => {
  if (isExpired.value) return;

  try {
    loadingBid.value = true;

    const res = await http.post("bids-create", {
      bid_amount: bidAmount.value,
      bonepost_id: boneData.value.id,
    });

    toast.success(res.data?.message ?? "Bid submitted!");
    const newBidFromServer = res.data?.bid;
    if (!Array.isArray(boneData.value.bids)) boneData.value.bids = [];
    if (newBidFromServer) {
      boneData.value.bids.unshift(newBidFromServer);
    } else {
      boneData.value.bids.unshift({
        _tmpId: "tmp_" + Date.now(),
        amount: bidAmount.value,
        created_at: new Date().toISOString(),
        user: res.data?.user || null,
      });
    }
    bidAmount.value = null;
  } catch (error) {
    console.error(error);
    toast.error(error?.response?.data?.message ?? "Failed to submit bid!");
  } finally {
    loadingBid.value = false;
  }
};

const specRows = computed(() => {
  const d = boneData.value.details?.[0] || {};
  const currentBid = currentBidAmount.value;

  return [
    { label: "Title", value: boneData.value.name, icon: "ti ti-article" },
    { label: "Case / Post Name", value: boneData.value.title, icon: "ti ti-id" },

    { label: "Bone Type", value: d.bone_type, icon: "ti ti-bone" },
    { label: "Body Side", value: d.body_side, icon: "ti ti-arrow-left-right" },
    { label: "Specimen Condition", value: d.specimen_condition, icon: "ti ti-shield-check" },
    { label: "Preservation Method", value: d.preservation_method, icon: "ti ti-archive" },
    { label: "Quantity", value: d.quantity, icon: "ti ti-stack" },

    { label: "Start Date", value: boneData.value.start_date, icon: "ti ti-calendar-time" },
    { label: "Expire Date", value: boneData.value.expire_date, icon: "ti ti-calendar-x" },

    { label: "Starting Price", value: ` ${boneData.value.starting_price}`, icon: "ti ti-tag" },
    { label: "Current Bid", value: ` ${currentBid}`, icon: "ti ti-gavel" },

    { label: "Created At", value: formatDateTime(boneData.value.created_at), icon: "ti ti-clock" },
    { label: "Updated At", value: formatDateTime(boneData.value.updated_at), icon: "ti ti-refresh" },
  ];
});

onMounted(() => {
  startSlider();
  startClock();
});

onUnmounted(() => {
  stopSlider();
  stopClock();
});
</script>

<style scoped>
.display-image { height: 320px; width: 100%; object-fit: contain; background: #f8fafc; padding: 15px; border-radius: 15px; }
.mini-thumb { width: 65px; height: 65px; cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden; opacity: 0.6; transition: 0.3s; }
.mini-thumb.active { border-color: #4f46e5; opacity: 1; transform: scale(1.05); }
.mini-thumb img { width: 100%; height: 100%; object-fit: cover; }

.nav-btn { position: absolute; top: 50%; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 35px; height: 35px; opacity: 0; transition: 0.3s; transform: translateY(-50%); z-index: 10; }
.gallery-container:hover .nav-btn { opacity: 1; }
.nav-btn.left { left: 10px; }
.nav-btn.right { right: 10px; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.main-title { font-size: 30px; font-weight: 800; color: #0f172a; }
.current-price { font-size: 38px; font-weight: 900; color: #1e293b; }
.category-tag { background: #eef2ff; color: #4f46e5; padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 12px; }
.title-line { width: 50px; height: 4px; background: #4f46e5; border-radius: 10px; }

.bid-history .history-row { border-bottom: 1px solid #f1f5f9; }
.bid-history .history-row:last-child { border-bottom: 0; }

.specs-section { animation: fadeIn 0.8s ease-out; }
.spec-item { transition: all 0.2s ease; }
.hover-bg:hover { background-color: #f8faff !important; }

.spec-icon-wrapper {
  width: 36px;
  height: 36px;
  background: #f0f3ff;
  color: #4f46e5;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.ls-1 { letter-spacing: 0.5px; }

.border-end:last-child { border-right: 0 !important; }

@media (max-width: 768px) {
  .border-end { border-right: 0 !important; }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.tk{ font-size: 1.05em; font-weight: 900; line-height: 1; vertical-align: baseline; }
.amt{ font-size: 1em; font-weight: 900; letter-spacing: .2px; }
.current-price .tk{ font-size: 0.9em; }
</style>