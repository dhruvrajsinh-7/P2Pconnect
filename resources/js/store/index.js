import { createStore } from "vuex";

import UserModule from "./modules/user";
import TitleModule from "./modules/title";
import ProfileModule from "./modules/profile";
import PostModule from "./modules/posts";
const store = createStore({
    modules: {
        User: UserModule,
        Title: TitleModule,
        Profile: ProfileModule,
        NewsPost: PostModule,
    },
});
export default store;
