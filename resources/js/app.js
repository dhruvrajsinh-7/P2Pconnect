import "./bootstrap";
import { createApp } from "vue";
import router from "./router";
import store from "./store/index";
import App from "./components/App.vue";

const app = createApp({});
app.component("App", App);
app.use(router);
app.use(store);
app.mount("#app");
