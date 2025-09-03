import axios from "axios";
const instance = axios.create({
    baseURL: window.location.origin,
    withCredentials: true,
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});
export default instance;
