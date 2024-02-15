import { createStore } from "vuex";

import UserModule from "./modules/user";
import TitleModule from "./modules/title";
import ProfileModule from "./modules/profile";
const store = createStore({
    modules: {
        User: UserModule,
        Title: TitleModule,
        Profile: ProfileModule,
    },
});
export default store;
