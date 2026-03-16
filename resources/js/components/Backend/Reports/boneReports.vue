<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mt-4 shadow-sm border-0">

      <h5 class="card-header fw-bold text-primary d-flex justify-content-between align-items-center bg-white py-3">
        <span><i class="bx bx-package me-2"></i>Delivery Management</span>
        <button class="btn btn-dark btn-sm px-3" @click="printTable">
          <i class="bx bx-printer me-1"></i> Print Report
        </button>
      </h5>

      <div class="row px-3 pb-3">
        <div class="col-md-3 mb-3">
          <label class="form-label fw-semibold">Search</label>
          <input v-model="searchQuery" type="text" class="form-control" placeholder="Search by user or title..."/>
        </div>

        <div class="col-md-3 mb-3">
          <label class="form-label fw-semibold">Select Status</label>
          <MySelect v-model="selectedStatus" :options="statusOptions" placeholder="Search Status..."/>
        </div>

        <div class="col-md-2 mb-3">
          <label class="form-label fw-semibold">Start Date</label>
          <input type="date" v-model="startDate" class="form-control"/>
        </div>

        <div class="col-md-2 mb-3">
          <label class="form-label fw-semibold">End Date</label>
          <input type="date" v-model="endDate" class="form-control"/>
        </div>

        <div class="col-md-2 mb-3 d-flex align-items-end">
          <button class="btn btn-outline-danger w-100" @click="clearFilters">
            Clear Filters
          </button>
        </div>
      </div>

      <div class="px-3 mb-3" v-if="filteredBones.length">
        <div class="alert alert-primary d-inline-block py-2 px-3 mb-0">
          <strong>Total Bids Amount:</strong> ৳ {{ totalBidsSum }}
        </div>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="fw-bold">#</th>
              <th class="fw-bold">User Name</th>
              <th class="fw-bold">Title & Details</th>
              <th class="fw-bold">Pricing</th>
              <th class="fw-bold">Auction Period</th>
              <th class="fw-bold text-center">Status</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(bone, index) in paginatedBones" :key="bone.id">
              <td>{{ serial(index) }}</td>
              <td>{{ bone.latest_bid?.user?.name ?? bone.user?.name ?? "N/A" }}</td>
              <td>
                <div class="fw-semibold text-dark">{{ bone.title ?? "N/A" }}</div>
                <small class="text-muted">
                  {{ bone.details?.[0]?.body_side ?? "N/A" }} | {{ bone.details?.[0]?.specimen_condition ?? "N/A" }}
                </small>
              </td>
              <td>
                <div class="d-flex flex-column">
                  <span class="text-muted small">Start: ৳{{ bone.starting_price ?? 0 }}</span>
                  <span class="fw-bold text-success">Bid: ৳{{ bone.latest_bid?.amount ?? bone.starting_price ?? 0 }}</span>
                </div>
              </td>
              <td>
                <small class="text-muted">
                  {{ formatDate(bone.start_date) }} - {{ formatDate(bone.expire_date) }}
                </small>
              </td>
              <td class="text-center">
                <span class="badge" :class="statusClass(bone.status)">
                  {{ bone.status?.toUpperCase() ?? "N/A" }}
                </span>
              </td>
            </tr>

            <tr v-if="!paginatedBones.length">
              <td colspan="6" class="text-center text-muted py-5">
                No matching records found.
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredBones.length" class="table-light">
            <tr>
              <td colspan="3" class="text-end fw-bold">Grand Total (Filtered):</td>
              <td colspan="3" class="fw-bold text-primary">৳ {{ totalBidsSum }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-2 border-top">
        <small class="text-muted">
          Showing {{ filteredBones.length ? startItem : 0 }} to {{ endItem }} of {{ filteredBones.length }} entries
        </small>
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-secondary" :disabled="page === 1" @click="page--">Previous</button>
          <button class="btn btn-sm btn-outline-secondary" :disabled="page === totalPages || totalPages === 0" @click="page++">Next</button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, getCurrentInstance, watch } from "vue"
import { useToast } from "vue-toastification"
import MySelect from "@utils/select2.vue"

const bones = ref([])
const searchQuery = ref("")
const selectedStatus = ref(null)
const startDate = ref("")
const endDate = ref("")
const page = ref(1)
const perPage = ref(10)

const { proxy } = getCurrentInstance()
const http = proxy.$http
const toast = useToast()

const statusOptions = ref([
  { id: "active", name: "Active" },
  { id: "sold", name: "Sold" },
  { id: "delivered", name: "Delivered" }
])

const fetchBones = async () => {
  try {
    const res = await http.get("get-bone-reports")
    bones.value = res.data.data ?? []
  } catch (e) {
    toast.error("Failed to load data")
  }
}

const filteredBones = computed(() => {
  let data = [...bones.value]
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    data = data.filter((bone) =>
      bone.title?.toLowerCase().includes(q) ||
      bone.status?.toLowerCase().includes(q) ||
      bone.user?.name?.toLowerCase().includes(q) ||
      bone.latest_bid?.user?.name?.toLowerCase().includes(q)
    )
  }
  if (selectedStatus.value) {
    const status = typeof selectedStatus.value === "object" ? selectedStatus.value.id : selectedStatus.value
    data = data.filter((bone) => bone.status === status)
  }
  if (startDate.value) {
    data = data.filter((bone) => bone.start_date && new Date(bone.start_date) >= new Date(startDate.value))
  }
  if (endDate.value) {
    data = data.filter((bone) => bone.expire_date && new Date(bone.expire_date) <= new Date(endDate.value))
  }
  return data
})

// Calculate Total Bids Sum
const totalBidsSum = computed(() => {
  return filteredBones.value.reduce((sum, bone) => {
    const price = bone.latest_bid?.amount ?? bone.starting_price ?? 0
    return sum + Number(price)
  }, 0).toLocaleString()
})

const totalPages = computed(() => Math.ceil(filteredBones.value.length / perPage.value))
const paginatedBones = computed(() => {
  const start = (page.value - 1) * perPage.value
  return filteredBones.value.slice(start, start + perPage.value)
})
const startItem = computed(() => (page.value - 1) * perPage.value + 1)
const endItem = computed(() => Math.min(page.value * perPage.value, filteredBones.value.length))
const serial = (index) => (page.value - 1) * perPage.value + index + 1

const formatDate = (date) => {
  if (!date) return "N/A"
  return new Date(date).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })
}

const statusClass = (status) => {
  if (status === "active") return "bg-label-warning"
  if (status === "sold") return "bg-label-info"
  if (status === "delivered") return "bg-label-success"
  return "bg-label-secondary"
}

const clearFilters = () => {
  searchQuery.value = ""
  selectedStatus.value = null
  startDate.value = ""
  endDate.value = ""
  page.value = 1
}

watch([searchQuery, selectedStatus, startDate, endDate], () => { page.value = 1 })

const getStatusLabel = () => {
  if (!selectedStatus.value) return "All"
  return typeof selectedStatus.value === "object" ? (selectedStatus.value.name ?? "All") : selectedStatus.value
}

const printTable = () => {
  const rowsHtml = filteredBones.value.length
    ? filteredBones.value.map((bone, index) => `
          <tr>
            <td style="text-align:center">${index + 1}</td>
            <td>${bone.latest_bid?.user?.name ?? bone.user?.name ?? "N/A"}</td>
            <td>
              <div style="font-weight:bold;">${bone.title ?? "N/A"}</div>
              <div style="font-size:11px; color:#666;">${bone.details?.[0]?.body_side ?? "N/A"} | ${bone.details?.[0]?.specimen_condition ?? "N/A"}</div>
            </td>
            <td style="text-align:right">৳ ${ (bone.latest_bid?.amount ?? bone.starting_price ?? 0).toLocaleString() }</td>
            <td style="font-size:11px;">${formatDate(bone.start_date)} - ${formatDate(bone.expire_date)}</td>
            <td style="text-align:center; text-transform: uppercase; font-size:11px;">${bone.status ?? "N/A"}</td>
          </tr>
        `).join("")
    : `<tr><td colspan="6" style="text-align:center; padding:30px;">No Data Available</td></tr>`

  const printWindow = window.open("", "_blank")
  const today = new Date().toLocaleDateString("en-GB", { day: "2-digit", month: "long", year: "numeric", hour: '2-digit', minute:'2-digit' })

  const printHtml = `
    <html>
      <head>
        <title>Delivery Report - DMC</title>
        <style>
          @page { size: A4; margin: 20mm; }
          body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
          .header-table { width: 100%; border-bottom: 2px solid #444; margin-bottom: 20px; }
          .report-title { text-align: center; }
          .report-title h1 { margin: 0; font-size: 22px; color: #000; }
          .report-title p { margin: 5px 0; font-size: 12px; color: #666; }
          .meta-info { width: 100%; margin-bottom: 20px; font-size: 12px; }
          table { width: 100%; border-collapse: collapse; }
          th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px 8px; font-size: 12px; text-transform: uppercase; }
          td { border: 1px solid #dee2e6; padding: 8px; font-size: 12px; }
          .total-row { background-color: #eee; font-weight: bold; }
          .footer { margin-top: 50px; width: 100%; }
          .sig-box { width: 200px; text-align: center; float: right; border-top: 1px solid #000; padding-top: 5px; font-size: 12px; }
          .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(0,0,0,0.05); z-index: -1; }
        </style>
      </head>
      <body>
        <div class="watermark">CONFIDENTIAL</div>
        <table class="header-table">
          <tr>
            <td style="border:none;" class="report-title">
              <h1>DHAKA MEDICAL COLLEGE</h1>
              <p>Department of Anatomy | Delivery Management System</p>
              <h3 style="margin-top:10px; text-decoration: underline;">DELIVERY STATUS REPORT</h3>
            </td>
          </tr>
        </table>

        <table class="meta-info" style="border:none;">
          <tr>
            <td style="border:none;"><strong>Date:</strong> ${today}</td>
            <td style="border:none; text-align:right;"><strong>Filter Status:</strong> ${getStatusLabel()}</td>
          </tr>
          <tr>
            <td style="border:none;"><strong>Period:</strong> ${startDate.value || 'Start'} to ${endDate.value || 'End'}</td>
            <td style="border:none; text-align:right;"><strong>Total Records:</strong> ${filteredBones.value.length}</td>
          </tr>
        </table>

        <table>
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="20%">User Name</th>
              <th width="35%">Item Description</th>
              <th width="15%">Price (BDT)</th>
              <th width="15%">Auction Date</th>
              <th width="10%">Status</th>
            </tr>
          </thead>
          <tbody>
            ${rowsHtml}
          </tbody>
          <tfoot>
            <tr class="total-row">
              <td colspan="3" style="text-align:right;">GRAND TOTAL</td>
              <td style="text-align:right;">৳ ${totalBidsSum.value}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>

        <div class="footer">
          <div class="sig-box" style="float:left;">Generated By System</div>
          <div class="sig-box">Authorized Signature</div>
        </div>
      </body>
    </html>
  `
  printWindow.document.write(printHtml)
  printWindow.document.close()
  setTimeout(() => {
    printWindow.print()
    printWindow.close()
  }, 500)
}

onMounted(() => { fetchBones() })
</script>

<style scoped>
/* Improved Badges */
.bg-label-success { background-color: #e8fadf; color: #71dd37; }
.bg-label-warning { background-color: #fff2e2; color: #ffab00; }
.bg-label-info { background-color: #e7e7ff; color: #696cff; }
.bg-label-secondary { background-color: #ebedef; color: #8592a3; }

.badge {
  font-size: 0.75rem;
  padding: 0.45em 0.8em;
  font-weight: 500;
  border-radius: 0.375rem;
}

.table thead th {
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.card-header {
  border-bottom: 1px solid #f0f2f4;
}
</style>