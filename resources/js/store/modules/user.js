import { createStore } from "vuex";
import axios from "axios";

const UserModule = {
    namespaced: true,
    state() {
        return {
            user: null,
            userStatus: null,
        };
    },
    mutations: {
        setAuthUser(state, payload) {
            state.user = payload;
        },
    },
    actions: {
        async fetchAuthUser({ commit }) {
            try {
                const res = await axios.get("/api/auth-user");
                commit("setAuthUser", res.data);
            } catch (error) {
                console.error(error);
            }
        },
        async logout({ commit }) {
            try {
                await axios.post("/logout");
                commit("setAuthUser", null);
            } catch (error) {
                console.error(error);
            }
        },
    },
    getters: {
        authUser(state) {
            return state.user;
        },
    },
};

export default UserModule;
