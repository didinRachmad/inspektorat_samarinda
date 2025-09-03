import { route } from "ziggy-js";

export default (name, params = {}, absolute = true) => {
    return route(name, params, absolute, window.Ziggy);
};
