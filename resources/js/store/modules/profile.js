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
            state.user.data.attributes.friendship = friendship;
        },
        setButtonStatus(state, status) {
            state.Friendbutton = status;
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
        async sendRequest({ commit, state }, friendId) {
            commit("setButtonStatus", "loading");
            try {
                const res = await axios.post("/api/friend-request", {
                    friend_id: friendId,
                });
                commit("setUserFriendShip", res.data);
            } catch (error) {
                commit("setButtonStatus", "Add Friend");
            }
        },
    },
    getters: {
        User(state) {
            return state.user;
        },
        FriendbuttonText(state, getters, rootState) {
            if (getters.friendship == null) {
                return "Add Friend";
            } else if (
                getters.friendship.data.attributes.confirmed_at === null
            ) {
                return "Pending Friend Request";
            }
            return state.Friendbutton;
        },
        friendShipStatus(state) {
            return state.user.data.attributes.friendship;
        },
    },
};

export default ProfileModule;
