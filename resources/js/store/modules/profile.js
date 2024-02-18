import axios from "axios";

const ProfileModule = {
    namespaced: true,
    state() {
        return {
            user: null,
            userStatus: null,
        };
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setUserStatus(state, status) {
            state.userStatus = status;
        },
        setUserFriendShip(state, friendship) {
            if (state.user && state.user.data && state.user.data.attributes) {
                state.user.data.attributes.friendship = friendship;
            } else {
                console.error("User data or attributes are null or undefined.");
            }
        },
    },
    actions: {
        async fetchUser({ commit, dispatch }, userId) {
            commit("setUserStatus", "loading");
            try {
                const res = await axios.get("/api/users/" + userId);
                commit("setUser", res.data);
                commit("setUserStatus", "success");
            } catch (error) {
                commit("setUserStatus", "error");
            }
        },
        async sendRequest({ commit, getters }, friendId) {
            if (getters.FriendbuttonText !== "Add Friend") {
                return;
            }
            try {
                const res = await axios.post("/api/friend-request", {
                    friend_id: friendId,
                });
                commit("setUserFriendShip", res.data);
            } catch (error) {}
        },
        async acceptRequest({ commit, state }, userId) {
            try {
                const res = await axios.post("/api/friend-request-response", {
                    user_id: userId,
                    status: 1,
                });
                commit("setUserFriendShip", res.data);
            } catch (error) {}
        },
        async ignoreRequest({ commit, state }, userId) {
            try {
                const res = await axios.delete(
                    "/api/friend-request-response/delete",
                    {
                        data: {
                            user_id: userId,
                        },
                    }
                );
                commit("setUserFriendShip", null);
            } catch (error) {}
        },
    },
    getters: {
        User(state) {
            return state.user;
        },
        status(state) {
            return {
                user: state.userStatus,
                posts: state.postStatus,
            };
        },
        friendShipStatus(state) {
            return state?.user?.data?.attributes.friendship;
        },
        FriendbuttonText(state, getters, rootState) {
            if (
                rootState.User?.user?.data?.user_id ===
                state?.user?.data?.user_id
            ) {
                return "";
            } else if (getters.friendShipStatus === null) {
                return "Add Friend";
            } else if (
                getters.friendShipStatus?.data?.attributes?.confirmed_at ===
                    null &&
                getters.friendShipStatus?.data?.attributes?.friend_id !==
                    rootState.User?.user?.data?.user_id
            ) {
                return "Pending Friend Request";
            } else if (
                getters.friendShipStatus?.data?.attributes?.confirmed_at !==
                null
            ) {
                return "";
            }

            return "Accept";
        },
    },
};

export default ProfileModule;
