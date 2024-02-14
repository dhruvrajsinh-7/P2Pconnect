import { createStore } from "vuex";

import UserModule from "./modules/user";
import TitleModule from "./modules/title";
const store = createStore({
    modules: {
        User: UserModule,
        Title: TitleModule,
    },
});
export default store;
