import axios from 'axios'

var laravelToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const axiosInstance = axios.create({
    withCredentials: false,
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': laravelToken
    }
});

export default axiosInstance
