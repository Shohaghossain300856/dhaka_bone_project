<template>
  <input
    ref="inputEl"
    type="text"
    class="form-control p_input"
    :class="{ 'is-invalid': isInvalid }"
    :placeholder="placeholder"
    autocomplete="off"
    inputmode="numeric"
    style="width: 100%;"
  />

  <small v-if="isInvalid && errorText" class="text-danger d-block mt-1">
    {{ errorText }}
  </small>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed, useAttrs } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

const attrs = useAttrs();
const emit = defineEmits(["update:modelValue", "invalid"]);

const modelValue = computed(() => attrs.modelValue ?? "");
const placeholder = computed(() => attrs.placeholder ?? "DD-MM-YYYY");
const minDate = computed(() => attrs["min-date"] ?? attrs.minDate ?? "");
const maxDate = computed(() => attrs["max-date"] ?? attrs.maxDate ?? "");

const inputEl = ref(null);
let fp = null;
let blurTimer = null;

const isInvalid = ref(false);
const errorText = ref("");

let isPointerDownInsideCalendar = false;
let justSelectedDate = false; // ✅ NEW: date select tracking
let docPointerDownHandler = null;
let inputHandler = null;
let keydownHandler = null;
let blurHandler = null;

function pad2(n) {
  return String(n).padStart(2, "0");
}

function toYmd(d) {
  return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
}

function fromYmdToDate(ymd) {
  if (!ymd) return null;
  const m = String(ymd).match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!m) return null;
  const y = +m[1], mo = +m[2], dd = +m[3];
  const dt = new Date(y, mo - 1, dd);
  if (dt.getFullYear() !== y || dt.getMonth() !== mo - 1 || dt.getDate() !== dd) return null;
  return dt;
}

function parseDmyFlexible(text) {
  if (!text) return { ok: false, reason: "Date is required" };

  const s = String(text).trim();
  const digits = s.replace(/\D/g, "");

  // DDMMYYYY format
  if (/^\d{8}$/.test(digits)) {
    const dd = parseInt(digits.slice(0, 2), 10);
    const mm = parseInt(digits.slice(2, 4), 10);
    const yy = parseInt(digits.slice(4, 8), 10);

    if (mm < 1 || mm > 12) return { ok: false, reason: "Month must be 1 to 12" };

    const dt = new Date(yy, mm - 1, dd);
    if (dt.getFullYear() !== yy || dt.getMonth() !== mm - 1 || dt.getDate() !== dd) {
      return { ok: false, reason: "Invalid day for this month" };
    }
    return { ok: true, date: dt, reason: "" };
  }

  // DDMMYY format
  if (/^\d{6}$/.test(digits)) {
    const dd = parseInt(digits.slice(0, 2), 10);
    const mm = parseInt(digits.slice(2, 4), 10);
    let yy = parseInt(digits.slice(4, 6), 10);
    yy = 2000 + yy;

    if (mm < 1 || mm > 12) return { ok: false, reason: "Month must be 1 to 12" };

    const dt = new Date(yy, mm - 1, dd);
    if (dt.getFullYear() !== yy || dt.getMonth() !== mm - 1 || dt.getDate() !== dd) {
      return { ok: false, reason: "Invalid day for this month" };
    }
    return { ok: true, date: dt, reason: "" };
  }

  // DD-MM-YYYY format
  const m = s.match(/^(\d{1,2})[\/\-\.\s](\d{1,2})[\/\-\.\s](\d{2}|\d{4})$/);
  if (!m) return { ok: false, reason: "Invalid format. Use DD-MM-YYYY" };

  let dd = parseInt(m[1], 10);
  let mm = parseInt(m[2], 10);
  let yy = parseInt(m[3], 10);

  if (Number.isNaN(dd) || Number.isNaN(mm) || Number.isNaN(yy))
    return { ok: false, reason: "Invalid numbers" };
  if (yy < 100) yy = 2000 + yy;

  if (mm < 1 || mm > 12) return { ok: false, reason: "Month must be 1 to 12" };

  const dt = new Date(yy, mm - 1, dd);
  if (dt.getFullYear() !== yy || dt.getMonth() !== mm - 1 || dt.getDate() !== dd) {
    return { ok: false, reason: "Invalid day for this month" };
  }

  return { ok: true, date: dt, reason: "" };
}

function setInvalid(msg) {
  isInvalid.value = true;
  errorText.value = msg || "Invalid date";
  emit("invalid", { invalid: true, message: errorText.value, value: inputEl.value?.value || "" });
}

function clearInvalid() {
  isInvalid.value = false;
  errorText.value = "";
  emit("invalid", { invalid: false, message: "", value: inputEl.value?.value || "" });
}

function withinMinMax(dt) {
  const min = fromYmdToDate(minDate.value);
  const max = fromYmdToDate(maxDate.value);

  if (min && dt.getTime() < min.getTime()) return { ok: false, reason: `Min date: ${minDate.value}` };
  if (max && dt.getTime() > max.getTime()) return { ok: false, reason: `Max date: ${maxDate.value}` };
  return { ok: true, reason: "" };
}

function applyValidDate(dt, { close = true, emitValue = true } = {}) {
  if (!fp || !dt) return;
  clearInvalid();

  fp.setDate(dt, false);
  inputEl.value.value = `${pad2(dt.getDate())}-${pad2(dt.getMonth() + 1)}-${dt.getFullYear()}`;

  if (emitValue) emit("update:modelValue", toYmd(dt));
  if (close) fp.close();
}

function validateAndApply(dateStr, { closeOnValid = true } = {}) {
  const parsed = parseDmyFlexible(dateStr);
  if (!parsed.ok) {
    setInvalid(parsed.reason);
    return null;
  }

  const range = withinMinMax(parsed.date);
  if (!range.ok) {
    setInvalid(range.reason);
    return null;
  }

  applyValidDate(parsed.date, { close: closeOnValid, emitValue: true });
  return parsed.date;
}

onMounted(() => {
  fp = flatpickr(inputEl.value, {
    dateFormat: "d-m-Y",
    allowInput: true,
    clickOpens: true,
    position: "auto",
    closeOnSelect: true,

    parseDate: (dateStr) => {
      const res = parseDmyFlexible(dateStr);
      if (!res.ok) return null;
      const rng = withinMinMax(res.date);
      if (!rng.ok) return null;
      return res.date;
    },

    onReady: () => {
      if (minDate.value) fp.set("minDate", minDate.value);
      if (maxDate.value) fp.set("maxDate", maxDate.value);
    },

    onChange: (selectedDates) => {
      if (!selectedDates?.length) return;
      const dt = selectedDates[0];

      const range = withinMinMax(dt);
      if (!range.ok) return setInvalid(range.reason);

      // ✅ Set flag যে date select হয়েছে
      justSelectedDate = true;
      
      applyValidDate(dt, { close: true, emitValue: true });
      
      // ✅ 200ms পর flag reset করবো
      setTimeout(() => {
        justSelectedDate = false;
      }, 200);
    },
  });

  // Initial modelValue set
  const initial = fromYmdToDate(modelValue.value);
  if (initial) {
    const range = withinMinMax(initial);
    if (range.ok) applyValidDate(initial, { close: false, emitValue: false });
  }

  // Calendar inside click detection
  docPointerDownHandler = (e) => {
    const cal = fp?.calendarContainer;
    isPointerDownInsideCalendar = !!(cal && cal.contains(e.target));
  };
  document.addEventListener("mousedown", docPointerDownHandler, true);
  document.addEventListener("touchstart", docPointerDownHandler, true);

  // Auto-format while typing
  inputHandler = () => {
    const raw = inputEl.value?.value || "";
    const digits = raw.replace(/\D/g, "");

    if (/[\/\-\.\s]/.test(raw)) return;

    if (digits.length === 8) {
      inputEl.value.value = `${digits.slice(0, 2)}-${digits.slice(2, 4)}-${digits.slice(4, 8)}`;
    } else if (digits.length === 6) {
      inputEl.value.value = `${digits.slice(0, 2)}-${digits.slice(2, 4)}-${digits.slice(4, 6)}`;
    }
  };
  inputEl.value.addEventListener("input", inputHandler);

  // Enter key handler
  keydownHandler = (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      const val = inputEl.value?.value || "";
      const dt = validateAndApply(val, { closeOnValid: true });
      if (!dt) fp?.open();
    }
  };
  inputEl.value.addEventListener("keydown", keydownHandler);

  // Blur handler
  blurHandler = () => {
    clearTimeout(blurTimer);

    blurTimer = setTimeout(() => {
      // ✅ MAIN FIX: যদি date select হয়ে থাকে, blur ignore করো
      if (justSelectedDate) {
        isPointerDownInsideCalendar = false;
        return;
      }

      // Calendar এর ভিতরে click করলে reopen করবে
      if (isPointerDownInsideCalendar) {
        isPointerDownInsideCalendar = false;
        fp?.open();
        return;
      }

      const val = inputEl.value?.value || "";

      if (!val.trim()) {
        emit("update:modelValue", "");
        clearInvalid();
        fp?.close();
        return;
      }

      const dt = validateAndApply(val, { closeOnValid: true });
      if (!dt) fp?.close();
    }, 120);
  };
  inputEl.value.addEventListener("blur", blurHandler);
});

// Watch min/max changes
watch(minDate, (v) => fp && fp.set("minDate", v || null));
watch(maxDate, (v) => fp && fp.set("maxDate", v || null));

// Watch v-model changes
watch(modelValue, (val) => {
  if (!fp) return;

  if (!val) {
    fp.clear();
    clearInvalid();
    return;
  }

  const dt = fromYmdToDate(val);
  if (!dt) return setInvalid("Invalid stored date");

  const range = withinMinMax(dt);
  if (!range.ok) return setInvalid(range.reason);

  const current = fp.selectedDates?.[0];
  if (!current || current.getTime() !== dt.getTime()) {
    applyValidDate(dt, { close: false, emitValue: false });
  } else {
    clearInvalid();
  }
});

onBeforeUnmount(() => {
  clearTimeout(blurTimer);
  blurTimer = null;

  if (docPointerDownHandler) {
    document.removeEventListener("mousedown", docPointerDownHandler, true);
    document.removeEventListener("touchstart", docPointerDownHandler, true);
    docPointerDownHandler = null;
  }

  if (inputHandler && inputEl.value) {
    inputEl.value.removeEventListener("input", inputHandler);
    inputHandler = null;
  }

  if (keydownHandler && inputEl.value) {
    inputEl.value.removeEventListener("keydown", keydownHandler);
    keydownHandler = null;
  }

  if (blurHandler && inputEl.value) {
    inputEl.value.removeEventListener("blur", blurHandler);
    blurHandler = null;
  }

  if (fp) fp.destroy();
  fp = null;
});
</script>

<style scoped>
:deep(.flatpickr-input) {
  background-image: none !important;
  background-repeat: no-repeat !important;
  padding-right: 12px !important;
}

:deep(.flatpickr-input::-webkit-calendar-picker-indicator) {
  display: none !important;
}

.is-invalid {
  border: 1px solid #dc3545 !important;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
}

:deep(.flatpickr-calendar) {
  z-index: 99999 !important;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.16);
  overflow: hidden;
}
</style>