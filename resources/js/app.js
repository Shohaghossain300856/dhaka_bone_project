import { createApp } from "vue";
import Dashboard from "./components/Dashboard/Dashboard.vue";
import BoneCreate from "./components/backend/BoneCases/BoneCreate.vue";
import boneDetails from "./components/backend/BoneCases/boneDetailsCreate.vue";
import boneDetailsShow from "./components/backend/BoneCases/BoneDetails.vue";
import delivary from "./components/backend/Delivary/index.vue";
import http from "./lib/http";


// loader
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

// Select2
import 'select2/dist/css/select2.min.css'
import $ from 'jquery'
import 'select2'

// toast
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const app = createApp({});
app.config.globalProperties.$http = http;
app.component("Loading", Loading);
app.component("dashboard", Dashboard);
app.component("bonecreate", BoneCreate);
app.component("bonedetails", boneDetails);
app.component("bonedetailshow", boneDetailsShow);
app.component("delivary", delivary);
 
 
app.use(Toast, {
  position: "top-right",
  timeout: 3000,
  toastClassName: "my-toast"
});

app.mount("#app");
