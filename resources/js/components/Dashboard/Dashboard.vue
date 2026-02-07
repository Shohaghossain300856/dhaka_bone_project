<template>
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="row g-6 mb-6">

    <!-- Total Users -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/user" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Users</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-primary icon-bubble">
                  <i class="ti ti-users-group ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Funds -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/fund" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Funds</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-danger icon-bubble">
                  <i class="ti ti-pig-money ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Categories -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/catagories" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Categories</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-success icon-bubble">
                  <i class="ti ti-category ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Subcategories -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/subCatagories" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Subcategories</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-warning icon-bubble">
                  <i class="ti ti-category-2 ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Supplier -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/Supplier" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Supplier</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-info icon-bubble">
                  <i class="ti ti-building-store ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Products -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/product" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Products</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-info icon-bubble">
                  <i class="ti ti-package ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Stock Qty -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/stock-create" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Stock Qty</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-info icon-bubble">
                  <i class="ti ti-package ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Delivary Qty -->
    <div class="col-sm-6 col-xl-3">
      <a href="/backend/stock-out" class="text-decoration-none">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <div class="d-flex align-items-center my-1">
                  <h4 class="mb-0 me-2">0</h4>
                </div>
                <small class="mb-0">Total Delivary Qty</small>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-info icon-bubble">
                  <i class="ti ti-package ti-26px"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>
</template>