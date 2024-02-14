const TitleModule = {
    namespaced: true,
    state() {
        return {
            title: "Welcome",
        };
    },
    mutations: {
        setTitle(state, title) {
            state.title = title + "| Facebook";
            document.title = state.title;
        },
    },
    actions: {
        setPageTitle({ commit }, title) {
            commit("setTitle", title);
        },
    },
    getters: {
        pageTitle(state) {
            return state.title;
        },
    },
};

export default TitleModule;
