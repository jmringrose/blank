
/*
Generic error handler for AXIOS
checks for 405 - expired and other AXIOS errors
 */
export default {errorhandler}
import { useToast } from "vue-toastification";
const toast = useToast();

export function errorhandler(err) {
    // 403 or code 4 the video has expired, code can't connect
    let msg = '';
    let txt = "Don't reload if you need to copy any data you have added.";
    if (typeof err.response === 'undefined') {
        //console.log('No error seen.')
        return;
    }
    else if (err.response) {
        //console.log(err.response.data);
        //console.log(err.response.status);
        //console.log(err.response.headers);
        // client received an error response (5xx, 4xx)
        if (err.response.status === 419) {
            msg = "Your session has expired."
            txt = "Please reload the page."
        }
        else if (err.response.status === 405) {
            msg = "Your login session seems to have expired.";
        }
        else if (err.response.status === 423) {
            msg = "The item is locked.";
            txt = "Please unlock it first.";
        }
        else if (err.response.status === 400) {
            msg = "Something is up!";
            txt = "That went wrong. Please try again.";
        }
        else if (err.response.status === 401) {
            msg = "Your login session seems to have expired.";
            txt = "Please click reload and login again.";
        }
        else {
            msg = "There is a network issue. Please try again later.";
        }
    } else if (err.request) {
        // client never received a response, or request never left
        msg = 'There was a problem. The server remained oddly silent - weird.';
    } else {
        // anything else
        msg = 'There was a problem of unknown type, which is not exactly helpful! :-) Please refresh first. '
    }
    // this is the vue version
    toast.error(msg, { description: txt, })
}
