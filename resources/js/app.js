import "./bootstrap";
import { createApp } from "vue";
import router from "./router";
import store from "./store/index";
import App from "./components/App.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";

const app = createApp({});
app.component("App", App);
app.component("font-awesome-icon", FontAwesomeIcon);
app.use(router);
app.use(store);
app.mount("#app");
