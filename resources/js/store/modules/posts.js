const PostModule = {
    namespaced: true,
    state() {
        return {
            posts: [],
            postsStatus: null,
            postMessage: "",
        };
    },
    mutations: {
        setPosts(state, posts) {
            state.posts = posts;
        },
        setPostsStatus(state, status) {
            state.postsStatus = status;
        },
        updateMessage(state, message) {
            state.postMessage = message;
        },
        pushPosts(state, post) {
            state.posts.data.unshift(post);
        },
        pushLikes(state, data) {
            state.posts.data[data.postKey].data.attributes.likes = data.likes;
        },
        pushComments(state, data) {
            state.posts.data[data.postKey].data.attributes.comments =
                data.comments;
        },
    },
    actions: {
        async fetchposts({ commit }) {
            commit("setPostsStatus", "loading");
            try {
                const response = await axios.get("/api/posts");
                commit("setPosts", response.data);
                commit("setPostsStatus", "success");
            } catch (error) {
                commit("setPostsStatus", "error");
            }
        },
        async postMessage({ commit, state }) {
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
        async likePost({ commit, state }, data) {
            try {
                const res = await axios.post(
                    "/api/posts/" + data.postId + "/like"
                );
                commit("pushLikes", { likes: res.data, postKey: data.postKey });
            } catch {}
        },
        async commentPost({ commit, state }, data) {
            try {
                const res = await axios.post(
                    "/api/posts/" + data.postId + "/comment",
                    {
                        body: data.body,
                    }
                );
                commit("pushComments", {
                    comments: res.data,
                    postKey: data.postKey,
                });
            } catch {
                console.error("Error while commenting on post.");
            }
        },
        async fetchUserPost({ commit, dispatch }, userId) {
            commit("setPostsStatus", "loading");
            try {
                const res = await axios.get("/api/users/" + userId + "/posts");
                commit("setPosts", res.data);
                commit("setPostsStatus", "success");
            } catch (error) {
                commit("setPostsStatus", "error");
            }
        },
    },
    getters: {
        posts(state) {
            return state.posts;
        },
        newsStatus(state) {
            return {
                postsStatus: state.postsStatus,
            };
        },
        postMessage(state) {
            return state.postMessage;
        },
    },
};

export default PostModule;
