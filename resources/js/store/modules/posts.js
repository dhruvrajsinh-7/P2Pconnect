const PostModule = {
    namespaced: true,
    state() {
        return {
            newsPosts: null,
            newsPostsStatus: null,
            postMessage: "",
        };
    },
    mutations: {
        setPosts(state, posts) {
            state.newsPosts = posts;
        },
        setPostsStatus(state, status) {
            state.newsPostsStatus = status;
        },
        updateMessage(state, message) {
            state.postMessage = message;
        },
        pushPosts(state, post) {
            state.newsPosts?.data?.unshift(post);
        },
    },
    actions: {
        async fetchNewsPosts({ commit }) {
            commit("setPostsStatus", "loading");
            try {
                const response = await axios.get("/api/posts");
                commit("setPosts", response.data);
                commit("setPostsStatus", "success");
            } catch (error) {
                commit("setPostsStatus", "error");
            }
        },
        async postMessage({ commit }) {
            commit("setPostsStatus", "loading");
            try {
                const response = await axios.post("/api/posts", {
                    body: state.postMessage,
                });
                commit("pushPosts", response?.data);
                commit("updateMessage", "");
                commit("setPostsStatus", "success");
            } catch (error) {
                commit("setPostsStatus", "error");
            }
        },
    },
    getters: {
        newsPosts(state) {
            return state.newsPosts;
        },
        newsStatus(state) {
            return {
                newsPostsStatus: state.newsPostsStatus,
            };
        },
        postMessage(state) {
            return state.postMessage;
        },
    },
};

export default PostModule;
