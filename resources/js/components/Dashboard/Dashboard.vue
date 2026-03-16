<template>
  <div class="container-xxl flex-grow-1 container-p-y bone-market">

    <!-- MARKETPLACE HEADER -->
    <div class="marketplace-header card border-0 shadow-sm mb-6 p-4">
      <div class="row g-4 align-items-center">
        <div class="col-lg-4">
          <h4 class="marketplace-title">Bone Marketplace</h4>
          <p class="marketplace-subtitle">
            <span v-if="bones.length" class="dot-active">● {{bones.length}} Items</span> available for research
          </p>
        </div>

        <div class="col-lg-8">
          <div class="row g-2 justify-content-end">
            <div class="col-md-5">
              <div class="search-box input-group">
                <span class="input-group-text">
                  <i class="ti ti-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Search by Case ID..." />
              </div>
            </div>

            <div class="col-md-4">
              <select class="category-select form-select">
                <option selected>All Categories</option>
                <option>Long Bones</option>
                <option>Cranial Fragments</option>
                <option>Vertebral Column</option>
              </select>
            </div>

            <div class="col-md-2">
              <button class="btn btn-primary filter-btn w-100">Filter</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- LIST CARD -->
    <div class="card mb-6 border-0 shadow-sm">
      <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h5 class="mb-1">Bone Listings</h5>
          <p class="mb-0 text-muted">
            Full-width card • Left gallery smaller • Right details bigger
          </p>
        </div>

        <div class="d-flex gap-2 align-items-center">
          <select class="form-select">
            <option>Sort: Latest</option>
            <option>Ending Soon</option>
            <option>Start Price: Low → High</option>
            <option>Start Price: High → Low</option>
          </select>

          <div class="form-check form-switch ms-2">
           <input
           type="checkbox"
           class="form-check-input"
           id="HideExpired"
           v-model="hideExpired"
           />
           <label class="form-check-label" for="HideExpired">Hide expired</label>
         </div>
       </div>
     </div>

     <div class="card-body pt-4">
      <div class="row gy-6">
        <div v-for="bone in filteredBones" :key="bone.id" class="col-12">
          <div class="listing-wide card border-0 overflow-hidden">
            <div class="row g-0">

              <!-- LEFT -->
              <div class="col-12 col-lg-4 left-col-smaller">
                <div class="gallery-wrap">

                  <!-- WRAP for popout -->
                  <div class="zoom-area">

                    <!-- MAIN IMAGE (hover target) -->
                    <div
                    class="main-zoom"
                    :style="mainCoverStyle(bone)"
                    @mousemove="onZoomMove($event, bone.id)"
                    @mouseenter="onZoomEnter(bone.id)"
                    @mouseleave="onZoomLeave(bone.id)"
                    role="button"
                    title="Hover to zoom"
                    >
                    <div class="cover-gradient"></div>

                    <span style="text-transform: capitalize;" class="badge badge-pill badge-active position-absolute top-0 start-0 m-3">
                      {{bone.status}}
                    </span>

                    <span
                    v-if="getRemainingTime(bone.expire_date) !== 'Expired'"
                    class="badge badge-pill badge-warning position-absolute top-0 end-0 m-3"
                    >
                    Ends in {{ getRemainingTime(bone.expire_date) }}
                  </span>

                  <span
                  v-else
                  style="background:rgb(173 38 39)"
                  class="badge badge-pill position-absolute top-0 end-0 m-3"
                  >
                  Expired
                </span>

                <div class="zoom-hint">
                  <i class="ti ti-zoom-in"></i>
                  <span>Hover to zoom</span>
                </div>
              </div>

              <!-- ✅ POP-OUT ZOOM SECTION (outside main image) -->
              <div
              class="zoom-popout"
              v-show="isZooming(bone.id)"
              :style="zoomPopoutStyle(bone)"
              aria-hidden="true"
              >
              <div class="zoom-popout-head">
                <i class="ti ti-focus-2"></i>
                <span>Zoom Preview</span>
              </div>
            </div>

          </div>

          <!-- THUMBS -->
          <div class="thumb-row">
            <button
            v-for="(img, i) in getImages(bone)"
            :key="img.key"
            class="thumb"
            :class="{ active: getActiveIndex(bone.id) === i }"
            @click="setActiveIndex(bone.id, i)"
            type="button"
            :title="'View image ' + (i + 1)"
            >
            <img :src="img.src" alt="thumb" />
          </button>
        </div>

    <div class="highest-bidder-card">
      <div class="hb-head">
        <i class="ti ti-crown"></i>
        <h6>Highest Bidder</h6>
      </div>
      <p class="hb-name">{{ bone.latest_bid?.user?.name ?? 'No bids yet' }}</p>
    </div>

      </div>
    </div>

    <!-- RIGHT -->
    <div class="col-12 col-lg-8 right-col-bigger">
      <div class="card-body info-body p-4 p-lg-5">

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
          <div class="d-flex gap-3 align-items-center">
            <img
            class="publisher-avatar"
            :src="bone.user?.user_image
            ? FontendURL.replace(/\/$/, '') + '/storage/user/' + bone.user.user_image
            : '/no-image.png'"
            alt="publisher"
            />
            <div>
              <div class="fw-bold publisher-name">{{ bone.user?.name ?? 'Unknown' }}</div>
              <small class="text-muted">Published by</small>
            </div>
          </div>

          <div class="d-flex gap-2 flex-wrap justify-content-end">
            <span class="chip chip-primary">{{ bone.details?.[0]?.bone_type ?? 'N/A' }}</span>
          </div>
        </div>

        <div class="listing-title-wide mb-3">{{ bone.name }}</div>

        <div class="meta-grid mb-4">
          <div class="meta-item">
            <div class="meta-label">Side</div>
            <div class="meta-value">{{ bone.details?.[0]?.body_side ?? 'N/A' }}</div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Quantity</div>
            <div class="meta-value">{{ bone.details?.[0]?.quantity ?? 'N/A' }}</div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Condition</div>
            <div class="meta-value">{{ bone.details?.[0]?.specimen_condition ?? 'N/A' }}</div>
          </div>
          <div class="price-box soft">
            <div class="price-label">Start Date</div>
            <div class="price-value small">{{ formatDateTime(bone.start_date) ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="price-wrap-wide mb-4">
          <div class="price-box">
            <div class="price-label">Start Price</div>
            <div class="price-value">৳{{ bone.starting_price }}</div>
          </div>
          <div class="price-box active">
            <div class="price-label">Current Bid</div>
            <div class="price-value">৳ {{ bone.latest_bid ? bone.latest_bid.amount : bone.starting_price }} </div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Expire Date</div>
            <div class="meta-value">{{ formatDateTime(bone.expire_date) ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="desc-box mb-4">
          <div class="desc-title">Description</div>
          <div class="desc-text">{{ bone.description }}</div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2">
        <a 
          :href="`/backend/dashboard/bone-details/${bone.id}`"
          class="btn btn-outline-primary w-100"
        >
          <i class="ti ti-eye"></i>
          View Details
        </a>
          
          <button
          @click="bidsModel(bone)"
          class="btn btn-primary w-100"
          :disabled="isExpired(bone.expire_date) || bidLoading"
          >
          <i class="ti ti-gavel"></i>
          Place Bid
        </button>
      </div>

    </div>
  </div>

</div>
</div>
</div>

</div>
</div>
</div>


<!-- Bids Modal -->
<div 
v-if="bidsModelOpen" 
class="modal fade show"
style="display:block; background: rgba(0,0,0,0.5);"
>
<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content">

    <div class="modal-header">
      <h5 class="modal-title">Place Bid</h5>
      <button 
      type="button" 
      class="btn-close" 
      @click="bidsModelOpen = false">
    </button>
  </div>

  <div class="modal-body">
    <div class=""></div>
    <label class="form-label">Your Bid Amount</label>
    <input 
    v-model="bidAmount" 
    type="number" 
    class="form-control" 
    placeholder="Enter amount" 
    />
  </div>

  <div class="modal-footer">
    <button 
    type="button" 
    class="btn btn-secondary" 
    @click="bidsModelOpen = false">
    Cancel
  </button>

  <button 
  type="button" 
  class="btn btn-primary" 
  :disabled="bidLoading" 
  @click="submitBid">
  <span 
  v-if="bidLoading" 
  class="spinner-border spinner-border-sm me-1">
</span>
Submit Bid
</button>
</div>

</div>
</div>
</div>

<div v-if="pageLoading" class="page-loader">
  <div class="loader-box">
    <div class="spinner-border custom-spinner text-primary"></div>
    <div class="mt-3 fw-bold">Loading Bones...</div>
  </div>
</div>

</div>

</template>

<script setup>
import { ref, onMounted, computed, onBeforeUnmount, getCurrentInstance } from "vue";
import { useToast } from "vue-toastification";
const pageLoading = ref(false);
const toast = useToast();
const { proxy } = getCurrentInstance();
const http = proxy.$http;
const FontendURL = proxy.$http.defaults.FontendURL;

const bidLoading = ref(false)
const hideExpired = ref(false);
const nowTs = ref(Date.now());
let timer = null;
const bones = ref([]);
const boneId = ref(null)
const bidAmount = ref(null)
const bidsModelOpen = ref(false)

const ui = ref({});
const ensureUI = (id) => {
  if (!ui.value[id]) {
    ui.value[id] = { activeIndex: 0, x: 50, y: 50, zoom: false };
  }
};

const getActiveIndex = (id) => {
  ensureUI(id);
  return ui.value[id].activeIndex;
};

const setActiveIndex = (id, idx) => {
  ensureUI(id);
  ui.value[id].activeIndex = idx;
  ui.value[id].x = 50;
  ui.value[id].y = 50;
};

const isZooming = (id) => {
  ensureUI(id);
  return ui.value[id].zoom;
};

const onZoomEnter = (id) => {
  ensureUI(id);
  ui.value[id].zoom = true;
  ui.value[id].x = 50;
  ui.value[id].y = 50;
};

const onZoomLeave = (id) => {
  ensureUI(id);
  ui.value[id].zoom = false;
  ui.value[id].x = 50;
  ui.value[id].y = 50;
};

const onZoomMove = (e, id) => {
  ensureUI(id);
  const rect = e.currentTarget.getBoundingClientRect();
  const x = ((e.clientX - rect.left) / rect.width) * 100;
  const y = ((e.clientY - rect.top) / rect.height) * 100;
  ui.value[id].x = Math.max(0, Math.min(100, x));
  ui.value[id].y = Math.max(0, Math.min(100, y));
};

const getImages = (bone) => {
  const list = [];
  const cover = bone.image
  ? FontendURL.replace(/\/$/, "") + "/" + bone.image.replace(/^\//, "")
  : "/no-image.png";

  list.push({ key: `cover-${bone.id}`, src: cover });

  const detailsImgs = bone.details?.[0]?.images || [];
  detailsImgs.forEach((imgObj) => {
    list.push({
      key: `detail-${imgObj.id}`,
      src: imgObj.images ? "/storage/" + imgObj.images : "/no-image.png",
    });
  });

  return list;
};
const mainCoverStyle = (bone) => {
  ensureUI(bone.id);
  const st = ui.value[bone.id];
  const imgs = getImages(bone);
  const active = imgs[st.activeIndex] ? imgs[st.activeIndex].src : imgs[0].src;

  return {
    backgroundImage: `url('${active}')`,
    backgroundRepeat: "no-repeat",
    backgroundPosition: "50% 50%",
    backgroundSize: "cover",
  };
};
const zoomPopoutStyle = (bone) => {
  ensureUI(bone.id);
  const st = ui.value[bone.id];

  const imgs = getImages(bone);
  const active = imgs[st.activeIndex] ? imgs[st.activeIndex].src : imgs[0].src;

  return {
    backgroundImage: `url('${active}')`,
    backgroundRepeat: "no-repeat",
    backgroundPosition: `${st.x}% ${st.y}%`,
    backgroundSize: st.zoom ? "240%" : "cover",
  };
};


const filteredBones = computed(() => {
  if (!hideExpired.value) return bones.value;

  return bones.value.filter(
    (bone) => !isExpired(bone.expire_date)
    );
});

const formatDateTime = (dateString) => {
  if (!dateString) return "N/A";

  const date = new Date(String(dateString).replace(" ", "T"));
  if (!Number.isFinite(date.getTime())) return "Invalid Date";

  return date.toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

onBeforeUnmount(() => {
  if (timer) clearInterval(timer);
});

const parseExpireDate = (dateStr) => {
  if (!dateStr) return null;

  if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
    return new Date(dateStr + "T23:59:59");
  }

  // "YYYY-MM-DD HH:mm:ss" -> "YYYY-MM-DDTHH:mm:ss"
  const normalized = String(dateStr).replace(" ", "T");
  return new Date(normalized);
};

const pad2 = (n) => String(n).padStart(2, "0");

const getRemainingTime = (expireDate) => {
  const end = parseExpireDate(expireDate);
  if (!end || !Number.isFinite(end.getTime())) return "Invalid date";

  const diff = end.getTime() - nowTs.value; // nowTs = Date.now()

  if (diff <= 0) return "Expired";

  const totalSeconds = Math.floor(diff / 1000);
  const days = Math.floor(totalSeconds / 86400);
  const hours = Math.floor((totalSeconds % 86400) / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  if (days > 0) return `${days}d ${pad2(hours)}h ${pad2(minutes)}m ${pad2(seconds)}s`;
  return `${pad2(hours)}h ${pad2(minutes)}m ${pad2(seconds)}s`;
};

const isExpired = (expireDate) => {
  const end = parseExpireDate(expireDate);
  if (!end || !Number.isFinite(end.getTime())) return true;
  return end.getTime() <= nowTs.value;
};

const fetchBones = async () => {

  try {
   pageLoading.value = true;
   const res = await http.get("dashboard/bones");
   bones.value = res.data?.data ?? [];
   bones.value.forEach((b) => ensureUI(b.id));
   console.log(res)
 } catch (error) {
  console.error("Details Fetch Error:", error);
  toast.error("Failed to load bone details!");
}finally{
  pageLoading.value = false;
}
};

const bidsModel = (bone) => {
  if (isExpired(bone.expire_date)) {
    toast.error("This post is expired. You can't place bid.");
    return;
  }

  boneId.value = bone.id
  bidAmount.value = bone.latest_bid ? bone.latest_bid.amount : bone.starting_price
  bidsModelOpen.value = true
}

const submitBid = async () => {
  try {
    bidLoading.value = true

    const res = await http.post("bids-create", {
      bid_amount: bidAmount.value,
      bonepost_id: boneId.value,
    })

    await fetchBones();
    toast.success(res.data?.message ?? "Bid submitted!")
    bidsModelOpen.value = false
  } catch (error) {
    console.error(error)
    toast.error(error?.response?.data?.message ?? "Failed to submit bid!")
  } finally {
    bidLoading.value = false
  }
}


onMounted(async () => {
  await fetchBones();
  timer = setInterval(() => {
    nowTs.value = Date.now();
  }, 1000);
});
</script>

<style>
.bone-market{
  --p1:#6d28d9;
  --p2:#0ea5e9;
  --p3:#22c55e;
  --warn:#f59e0b;
  --text:#0f172a;
}

.marketplace-title{ font-weight: 900; letter-spacing:.2px; }
.dot-active{ color: #22c55e; font-weight: 800; }
.listing-wide{
  border-radius: 20px;
  background:#fff;
  box-shadow: 0 18px 55px rgba(15,23,42,.08);
  transition:.2s;
  overflow: visible !important;
  position: relative;
  z-index: 1;
}
.listing-wide:hover{
  transform: translateY(-3px);
  box-shadow: 0 26px 85px rgba(15,23,42,.14);
  z-index: 5;
}

@media (min-width: 992px){
  .left-col-smaller{ width: 30%; flex: 0 0 30%; }
  .right-col-bigger{ width: 70%; flex: 0 0 70%; }
}

.gallery-wrap{
  height: 100%;
  padding: 14px;
  background: linear-gradient(180deg, rgba(109,40,217,.06), rgba(14,165,233,.04));
  position: relative;
  overflow: visible;
}

.zoom-area{
  position: relative;
  overflow: visible;
}

/* main image */
.main-zoom{
  position: relative;
  width: 100%;
  height: 280px;
  border-radius: 18px;
  overflow: hidden;
  cursor: zoom-in;
  background-repeat: no-repeat;
  box-shadow: 0 16px 44px rgba(15,23,42,.12);
  outline: 1px solid rgba(148,163,184,.35);
}
@media (max-width: 991px){
  .main-zoom{ height: 250px; }
}

.cover-gradient{
  position:absolute;
  inset:0;
  background:linear-gradient(to top,rgba(0,0,0,.55),transparent);
  pointer-events:none;
}

/* zoom hint */
.zoom-hint{
  position:absolute;
  left:14px;
  bottom:14px;
  display:flex;
  align-items:center;
  gap:8px;
  padding:8px 10px;
  border-radius:999px;
  background:rgba(15,23,42,.55);
  color:#fff;
  font-size:12px;
  font-weight:900;
  backdrop-filter: blur(8px);
  pointer-events:none;
}
.badge-pill{ border-radius:999px; padding:6px 10px; font-weight:900; font-size:12px; }
.badge-active, .badge-warning{ background: rgba(74, 92, 231, 0.95); color: white; }
.zoom-popout{
  position: absolute;
  top: 0;
  left: calc(100% + 14px);
  width: 480px;
  height: 360px;
  border-radius: 18px;
  overflow: hidden;
  outline: 1px solid rgba(148,163,184,.40);
  box-shadow: 0 18px 60px rgba(15,23,42,.22);
  background-color: #fff;
  transition: background-position .06s linear, background-size .18s ease;
  will-change: background-position, background-size;
  z-index: 50;
  pointer-events: none; /* ✅ hover glitch হবে না */
}

/* popout header */
.zoom-popout-head{
  position:absolute;
  left:12px;
  top:12px;
  display:flex;
  align-items:center;
  gap:8px;
  padding:8px 10px;
  border-radius:999px;
  background:rgba(15,23,42,.60);
  color:#fff;
  font-size:12px;
  font-weight:900;
  backdrop-filter: blur(10px);
}

/* ✅ mobile এ popout hide (space নাই) */
@media (max-width: 1199px){
  .zoom-popout{ display:none !important; }
}

/* thumbs */
.thumb-row{
  display:grid;
  grid-template-columns: repeat(4, 1fr);
  gap:10px;
  margin-top: 12px;
}
.thumb{
  border:0;
  padding:0;
  border-radius: 14px;
  overflow:hidden;
  height: 64px;
  background:#fff;
  cursor:pointer;
  outline: 1px solid rgba(148,163,184,.35);
  transition: .15s;
}
.thumb img{ width:100%; height:100%; object-fit:cover; display:block; }
.thumb:hover{ transform: translateY(-2px); outline: 2px solid rgba(14,165,233,.55); }
.thumb.active{ outline: 2px solid rgba(109,40,217,.7); }

/* RIGHT bigger typography */
.info-body{ font-size: 16px; }

/* publisher */
.publisher-avatar{
  width:52px;
  height:52px;
  border-radius:50%;
  object-fit:cover;
  outline: 2px solid rgba(226,232,240,1);
}
.publisher-name{ font-weight: 950; color: var(--text); font-size: 18px; }

/* chips */
.chip{
  padding: 12px 18px;
  border-radius: 999px;
  font-weight: 950;
  font-size: 14px;
  white-space: nowrap;
}
.chip-primary{ background: rgb(236 231 251); color: rgba(15,23,42,.78); }
.chip-soft{ background: rgba(15,23,42,.04); color: rgba(15,23,42,.75); }

/* title */
.listing-title-wide{
  font-size: 26px;
  font-weight: 1000;
  color: var(--text);
  letter-spacing: .2px;
}

/* meta grid */
.meta-grid{
  display:grid;
  grid-template-columns: repeat(4, 1fr);
  gap:12px;
}
@media (max-width: 991px){
  .meta-grid{ grid-template-columns: repeat(2, 1fr); }
}
.meta-item{
  padding:14px;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid rgba(226,232,240,.9);
}
.meta-label{
  font-size: 13px;
  font-weight: 900;
  color: rgba(71,85,105,.9);
  margin-bottom: 6px;
}
.meta-value{
  font-size: 15px;
  font-weight: 950;
  color: rgba(15,23,42,.85);
}

/* prices */
.price-wrap-wide{
  display:grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap:12px;
}
@media (max-width: 991px){
  .price-wrap-wide{ grid-template-columns: 1fr 1fr; }
}
.price-box{
  padding:14px;
  border-radius:16px;
  background:#f8fafc;
  border: 1px solid rgba(226,232,240,.9);
}
.price-box.active{
  background: linear-gradient(135deg,rgba(109,40,217,.14),rgba(14,165,233,.14));
  border-color: rgba(109,40,217,.18);
}
.price-box.soft{
  background: rgba(34,197,94,.07);
  border-color: rgba(34,197,94,.18);
}
.price-label{
  font-size: 13px;
  font-weight: 900;
  color: rgba(71,85,105,.92);
}
.price-value{
  font-size: 22px;
  font-weight: 1000;
  color: rgba(15,23,42,.9);
}
.price-value.small{ font-size: 15px; }

/* description */
.desc-box{
  padding:16px;
  border-radius:16px;
  background: rgba(34,197,94,.08);
  border: 1px solid rgba(34,197,94,.18);
}
.desc-title{
  font-weight: 1000;
  font-size: 16px;
  color: rgba(15,23,42,.85);
  margin-bottom: 8px;
}
.desc-text{
  color: rgba(15,23,42,.75);
  font-weight: 650;
  line-height: 1.75;
  max-height: 140px;
  overflow: auto;
  font-size: 15px;
}

/* buttons */
.btn-soft{
  background:#f1f5f9;
  border-radius:14px;
  font-weight:950;
  padding: 12px 14px;
  font-size: 16px;
}
.btn-gradient{
  border-radius:14px;
  font-weight:1000;
  color:#fff;
  border:0;
  padding: 12px 14px;
  font-size: 16px;
  background:linear-gradient(135deg,var(--p1),var(--p2));
}
.custom-spinner {
  width: 3rem;
  height: 3rem;
  border-width: 0.4em;
  border-color: #1500f1 transparent #1500f1 transparent;
}
.page-loader{
  position: fixed;
  inset: 0;
  background: rgba(255,255,255,0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loader-box{
  text-align: center;
}


.highest-bidder-card{
  margin-top: 12px;
  padding: 14px 14px;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(109,40,217,.10), rgba(14,165,233,.08));
  border: 1px solid rgba(109,40,217,.18);
  box-shadow: 0 10px 28px rgba(15,23,42,.08);
  position: relative;
  overflow: hidden;
}

.highest-bidder-card::before{
  content:"";
  position:absolute;
  top:0;
  left:0;
  width:100%;
  height:4px;
  background: linear-gradient(90deg, rgba(109,40,217,.9), rgba(14,165,233,.9));
}

.hb-head{
  display:flex;
  align-items:center;
  gap:8px;
  margin-bottom: 8px;
  color: rgba(15,23,42,.75);
}

.hb-head i{
  font-size: 18px;
  color: rgba(245,158,11,.95); 
}

.hb-head h6{
  margin:0;
  font-size: 12px;
  font-weight: 1000;
  letter-spacing: .9px;
  text-transform: uppercase;
}

.hb-name{
  margin: 0;
  font-size: 16px;
  font-weight: 1000;
  color: rgba(15,23,42,.92);
  line-height: 1.2;
  word-break: break-word;
}

.highest-bidder-card:hover{
  transform: translateY(-2px);
  transition: .2s ease;
  box-shadow: 0 18px 44px rgba(15,23,42,.12);
}

@media (max-width: 991px){
  .highest-bidder-card{
    padding: 12px;
    border-radius: 14px;
  }
  .hb-name{ font-size: 15px; }
}
</style>
